# ─── ECS Cluster ──────────────────────────────────────────────────────────────
resource "aws_ecs_cluster" "vishnu_terraform_ecs_cluster_ror" {
  name = "vishnu-terraform-shared-ecs-cluster-ror"

  setting {
    name  = "containerInsights"
    value = "enabled"
  }

  tags = { Name = "vishnu_terraform_ecs_cluster_ror" }
}

# ─── ECS Capacity Provider (EC2) ──────────────────────────────────────────────
resource "aws_ecs_capacity_provider" "vishnu_terraform_ec2_cp_ror" {
  name = "vishnu-terraform-ec2-cp-ror"

  auto_scaling_group_provider {
    auto_scaling_group_arn         = aws_autoscaling_group.vishnu_terraform_ecs_asg_ror.arn
    managed_termination_protection = "DISABLED"

    managed_scaling {
      maximum_scaling_step_size = 2
      minimum_scaling_step_size = 1
      status                    = "ENABLED"
      target_capacity           = 80
    }
  }

  tags = { Name = "vishnu_terraform_ec2_cp_ror" }
}

resource "aws_ecs_cluster_capacity_providers" "vishnu_terraform_ecs_cp_ror" {
  cluster_name       = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
  capacity_providers = [aws_ecs_capacity_provider.vishnu_terraform_ec2_cp_ror.name]

  default_capacity_provider_strategy {
    capacity_provider = aws_ecs_capacity_provider.vishnu_terraform_ec2_cp_ror.name
    weight            = 1
    base              = 1
  }
}

# ─── ECS-Optimised AMI (latest Amazon Linux 2) ────────────────────────────────
data "aws_ssm_parameter" "vishnu_terraform_ecs_ami_ror" {
  name = "/aws/service/ecs/optimized-ami/amazon-linux-2/recommended/image_id"
}

# ─── Launch Template for ECS EC2 instances ────────────────────────────────────
resource "aws_launch_template" "vishnu_terraform_ecs_lt_ror" {
  name          = "vishnu-terraform-ecs-lt-ror"
  image_id      = data.aws_ssm_parameter.vishnu_terraform_ecs_ami_ror.value
  instance_type = var.ecs_instance_type
  key_name      = var.key_pair_name

  iam_instance_profile {
    name = aws_iam_instance_profile.vishnu_terraform_ec2_ecs_profile_ror.name
  }

  vpc_security_group_ids = [aws_security_group.vishnu_terraform_sg_ecs_ror.id]

  block_device_mappings {
    device_name = "/dev/xvda"
    ebs {
      volume_size           = 30
      volume_type           = "gp3"
      delete_on_termination = true
      encrypted             = true
    }
  }

  # Register instance into the ECS cluster on boot
  user_data = base64encode(<<-USERDATA
    #!/bin/bash
    echo ECS_CLUSTER=${aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name} >> /etc/ecs/ecs.config
    echo ECS_ENABLE_CONTAINER_METADATA=true >> /etc/ecs/ecs.config
    echo ECS_ENABLE_AWSLOGS_EXECUTIONROLE_OVERRIDE=true >> /etc/ecs/ecs.config
  USERDATA
  )

  tag_specifications {
    resource_type = "instance"
    tags = {
      Name    = "vishnu-terraform-ecs-ec2-ror"
      Project = "vishnu-terraform"
    }
  }

  tags = { Name = "vishnu_terraform_ecs_lt_ror" }
}

# ─── Auto Scaling Group for ECS EC2 instances ─────────────────────────────────
resource "aws_autoscaling_group" "vishnu_terraform_ecs_asg_ror" {
  name                = "vishnu-terraform-ecs-asg-ror"
  vpc_zone_identifier = aws_subnet.vishnu_terraform_private_subnet_ror[*].id
  min_size            = var.asg_min_size
  max_size            = var.asg_max_size
  desired_capacity    = var.asg_desired_capacity

  # Protect from scale-in when ECS tasks are running
  protect_from_scale_in = true

  launch_template {
    id      = aws_launch_template.vishnu_terraform_ecs_lt_ror.id
    version = "$Latest"
  }

  tag {
    key                 = "Name"
    value               = "vishnu-terraform-ecs-ec2-ror"
    propagate_at_launch = true
  }

  tag {
    key                 = "AmazonECSManaged"
    value               = "true"
    propagate_at_launch = true
  }

  lifecycle {
    ignore_changes = [desired_capacity]
  }
}

# ─── CloudWatch Log Groups ────────────────────────────────────────────────────
resource "aws_cloudwatch_log_group" "vishnu_terraform_lg_ror" {
  name              = "/ecs/vishnu-terraform-ror-task"
  retention_in_days = 30
  tags              = { Name = "vishnu_terraform_lg_ror" }
}

resource "aws_cloudwatch_log_group" "vishnu_terraform_lg_php" {
  name              = "/ecs/vishnu-terraform-php-task"
  retention_in_days = 30
  tags              = { Name = "vishnu_terraform_lg_php" }
}

# ─── Task Definition – Ruby on Rails ─────────────────────────────────────────
resource "aws_ecs_task_definition" "vishnu_terraform_ror_task" {
  family                   = "vishnu-terraform-ror-task"
  network_mode             = "bridge" # EC2 bridge mode
  requires_compatibilities = ["EC2"]
  cpu                      = var.ror_task_cpu
  memory                   = var.ror_task_memory
  execution_role_arn       = aws_iam_role.vishnu_terraform_ecs_task_exec_role_ror.arn
  task_role_arn            = aws_iam_role.vishnu_terraform_ecs_task_role_ror.arn

  container_definitions = jsonencode([
    {
      name      = "movie-ror-container"
      image     = "${aws_ecr_repository.vishnu_terraform_ecr_ror.repository_url}:latest"
      essential = true
      cpu       = var.ror_task_cpu
      memory    = var.ror_task_memory

      portMappings = [
        {
          containerPort = 3000
          hostPort      = 0 # 0 = dynamic port mapping (bridge mode)
          protocol      = "tcp"
        }
      ]

      environment = [
        { name = "RAILS_ENV", value = var.ror_rails_env },
        { name = "RAILS_LOG_TO_STDOUT", value = "true" },
        { name = "RAILS_SERVE_STATIC_FILES", value = "true" },
        { name = "DATABASE_URL", value = "mysql2://${var.db_username}:${var.db_password}@${aws_db_instance.vishnu_terraform_rds_shared.endpoint}/${var.ror_db_name}" },
        { name = "SECRET_KEY_BASE", value = var.ror_secret_key_base },
        { name = "RAILS_HOSTNAME", value = var.ror_subdomain }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = aws_cloudwatch_log_group.vishnu_terraform_lg_ror.name
          "awslogs-region"        = var.aws_region
          "awslogs-stream-prefix" = "ecs"
        }
      }

      healthCheck = {
        command     = ["CMD-SHELL", "curl -f http://localhost:3000/ || exit 1"]
        interval    = 30
        timeout     = 5
        retries     = 3
        startPeriod = 60
      }
    }
  ])

  tags = { Name = "vishnu_terraform_ror_task" }
}

# ─── ECS Service – Ruby on Rails ─────────────────────────────────────────────
resource "aws_ecs_service" "vishnu_terraform_ror_service" {
  name            = "vishnu-terraform-ror-service"
  cluster         = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.id
  task_definition = aws_ecs_task_definition.vishnu_terraform_ror_task.arn
  desired_count   = var.ror_service_desired_count

  # EC2 launch type — no network_configuration block needed for bridge mode
  launch_type = "EC2"

  load_balancer {
    target_group_arn = aws_lb_target_group.vishnu_terraform_tg_ror.arn
    container_name   = "movie-ror-container"
    container_port   = 3000
  }

  deployment_minimum_healthy_percent = 50
  deployment_maximum_percent         = 200

  deployment_circuit_breaker {
    enable   = true
    rollback = true
  }

  depends_on = [
    aws_lb_listener.vishnu_terraform_https_listener_ror,
    aws_iam_role_policy_attachment.vishnu_terraform_ecs_task_exec_attach_ror,
    aws_autoscaling_group.vishnu_terraform_ecs_asg_ror
  ]

  lifecycle {
    ignore_changes = [task_definition, desired_count]
  }

  tags = { Name = "vishnu_terraform_ror_service" }
}

# ─── Task Definition – Laravel / PHP ─────────────────────────────────────────
resource "aws_ecs_task_definition" "vishnu_terraform_php_task" {
  family                   = "vishnu-terraform-php-task"
  network_mode             = "bridge"
  requires_compatibilities = ["EC2"]
  cpu                      = var.php_task_cpu
  memory                   = var.php_task_memory
  execution_role_arn       = aws_iam_role.vishnu_terraform_ecs_task_exec_role_ror.arn
  task_role_arn            = aws_iam_role.vishnu_terraform_ecs_task_role_ror.arn

  container_definitions = jsonencode([
    {
      name      = "food-php-container"
      image     = "${aws_ecr_repository.vishnu_terraform_ecr_php.repository_url}:latest"
      essential = true
      cpu       = var.php_task_cpu
      memory    = var.php_task_memory

      portMappings = [
        {
          containerPort = 80
          hostPort      = 0 # dynamic port mapping
          protocol      = "tcp"
        }
      ]

      environment = [
        { name = "APP_ENV", value = var.php_app_env },
        { name = "APP_KEY", value = var.php_app_key },
        { name = "APP_URL", value = "https://${var.php_subdomain}" },
        { name = "DB_CONNECTION", value = "mysql" },
        { name = "DB_HOST", value = aws_db_instance.vishnu_terraform_rds_shared.address },
        { name = "DB_PORT", value = "3306" },
        { name = "DB_DATABASE", value = var.php_db_name },
        { name = "DB_USERNAME", value = var.db_username },
        { name = "APP_DEBUG", value = "true" },
        { name = "DB_PASSWORD", value = var.db_password }

      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = aws_cloudwatch_log_group.vishnu_terraform_lg_php.name
          "awslogs-region"        = var.aws_region
          "awslogs-stream-prefix" = "ecs"
        }
      }

      healthCheck = {
        command     = ["CMD-SHELL", "curl -f http://localhost:80/ || exit 1"]
        interval    = 30
        timeout     = 5
        retries     = 3
        startPeriod = 60
      }
    }
  ])

  tags = { Name = "vishnu_terraform_php_task" }
}

# ─── ECS Service – Laravel / PHP ─────────────────────────────────────────────
resource "aws_ecs_service" "vishnu_terraform_php_service" {
  name            = "vishnu-terraform-php-service"
  cluster         = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.id
  task_definition = aws_ecs_task_definition.vishnu_terraform_php_task.arn
  desired_count   = var.php_service_desired_count
  launch_type     = "EC2"

  load_balancer {
    target_group_arn = aws_lb_target_group.vishnu_terraform_tg_php.arn
    container_name   = "food-php-container"
    container_port   = 80
  }

  deployment_minimum_healthy_percent = 50
  deployment_maximum_percent         = 200

  deployment_circuit_breaker {
    enable   = true
    rollback = true
  }

  depends_on = [
    aws_lb_listener_rule.vishnu_terraform_php_rule_ror,
    aws_iam_role_policy_attachment.vishnu_terraform_ecs_task_exec_attach_ror,
    aws_autoscaling_group.vishnu_terraform_ecs_asg_ror
  ]

  lifecycle {
    ignore_changes = [task_definition, desired_count]
  }

  tags = { Name = "vishnu_terraform_php_service" }
}

