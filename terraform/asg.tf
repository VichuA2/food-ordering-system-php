# ─── App Auto Scaling – PHP ECS Service ──────────────────────────────────────
resource "aws_appautoscaling_target" "vishnu_terraform_asg_target_php" {
  max_capacity       = var.asg_max_size
  min_capacity       = var.asg_min_size
  resource_id        = "service/${aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name}/${aws_ecs_service.vishnu_terraform_php_service.name}"
  scalable_dimension = "ecs:service:DesiredCount"
  service_namespace  = "ecs"
}

resource "aws_appautoscaling_policy" "vishnu_terraform_asg_cpu_php" {
  name               = "vishnu-terraform-asg-cpu-php"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.vishnu_terraform_asg_target_php.resource_id
  scalable_dimension = aws_appautoscaling_target.vishnu_terraform_asg_target_php.scalable_dimension
  service_namespace  = aws_appautoscaling_target.vishnu_terraform_asg_target_php.service_namespace

  target_tracking_scaling_policy_configuration {
    target_value       = 70.0
    scale_in_cooldown  = 300
    scale_out_cooldown = 60
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageCPUUtilization"
    }
  }
}

resource "aws_appautoscaling_policy" "vishnu_terraform_asg_mem_php" {
  name               = "vishnu-terraform-asg-mem-php"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.vishnu_terraform_asg_target_php.resource_id
  scalable_dimension = aws_appautoscaling_target.vishnu_terraform_asg_target_php.scalable_dimension
  service_namespace  = aws_appautoscaling_target.vishnu_terraform_asg_target_php.service_namespace

  target_tracking_scaling_policy_configuration {
    target_value       = 75.0
    scale_in_cooldown  = 300
    scale_out_cooldown = 60
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageMemoryUtilization"
    }
  }
}

# ─── App Auto Scaling – RoR ECS Service ──────────────────────────────────────
resource "aws_appautoscaling_target" "vishnu_terraform_asg_target_ror" {
  max_capacity       = var.asg_max_size
  min_capacity       = var.asg_min_size
  resource_id        = "service/${aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name}/${aws_ecs_service.vishnu_terraform_ror_service.name}"
  scalable_dimension = "ecs:service:DesiredCount"
  service_namespace  = "ecs"
}

resource "aws_appautoscaling_policy" "vishnu_terraform_asg_cpu_ror" {
  name               = "vishnu-terraform-asg-cpu-ror"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.resource_id
  scalable_dimension = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.scalable_dimension
  service_namespace  = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.service_namespace

  target_tracking_scaling_policy_configuration {
    target_value       = 70.0
    scale_in_cooldown  = 300
    scale_out_cooldown = 60
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageCPUUtilization"
    }
  }
}

resource "aws_appautoscaling_policy" "vishnu_terraform_asg_mem_ror" {
  name               = "vishnu-terraform-asg-mem-ror"
  policy_type        = "TargetTrackingScaling"
  resource_id        = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.resource_id
  scalable_dimension = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.scalable_dimension
  service_namespace  = aws_appautoscaling_target.vishnu_terraform_asg_target_ror.service_namespace

  target_tracking_scaling_policy_configuration {
    target_value       = 75.0
    scale_in_cooldown  = 300
    scale_out_cooldown = 60
    predefined_metric_specification {
      predefined_metric_type = "ECSServiceAverageMemoryUtilization"
    }
  }
}

# ─── CloudWatch CPU Alarms ────────────────────────────────────────────────────
resource "aws_cloudwatch_metric_alarm" "vishnu_terraform_cpu_high_ror" {
  alarm_name          = "vishnu-terraform-cpu-high-ror"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = 2
  metric_name         = "CPUUtilization"
  namespace           = "AWS/ECS"
  period              = 60
  statistic           = "Average"
  threshold           = 80
  alarm_description   = "RoR ECS CPU > 80%"

  dimensions = {
    ClusterName = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
    ServiceName = aws_ecs_service.vishnu_terraform_ror_service.name
  }

  alarm_actions = [aws_sns_topic.vishnu_terraform_sns_ror.arn]
  ok_actions    = [aws_sns_topic.vishnu_terraform_sns_ror.arn]
  tags          = { Name = "vishnu_terraform_cpu_high_ror" }
}

resource "aws_cloudwatch_metric_alarm" "vishnu_terraform_cpu_high_php" {
  alarm_name          = "vishnu-terraform-cpu-high-php"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = 2
  metric_name         = "CPUUtilization"
  namespace           = "AWS/ECS"
  period              = 60
  statistic           = "Average"
  threshold           = 80
  alarm_description   = "PHP ECS CPU > 80%"

  dimensions = {
    ClusterName = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
    ServiceName = aws_ecs_service.vishnu_terraform_php_service.name
  }

  alarm_actions = [aws_sns_topic.vishnu_terraform_sns_ror.arn]
  ok_actions    = [aws_sns_topic.vishnu_terraform_sns_ror.arn]
  tags          = { Name = "vishnu_terraform_cpu_high_php" }
}
