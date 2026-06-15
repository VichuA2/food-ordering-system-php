# ─── ALB Security Group ───────────────────────────────────────────────────────
resource "aws_security_group" "vishnu_terraform_sg_alb_ror" {
  name        = "vishnu_terraform_sg_alb_ror"
  description = "Allow HTTP and HTTPS inbound to ALB"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id

  ingress {
    description = "HTTP from internet"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS from internet"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "All outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "vishnu_terraform_sg_alb_ror" }
}

# ─── ECS (EC2 instances / Fargate tasks) Security Group ──────────────────────
resource "aws_security_group" "vishnu_terraform_sg_ecs_ror" {
  name        = "vishnu_terraform_sg_ecs_ror"
  description = "Allow traffic from ALB to ECS tasks on dynamic port range"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id

  # Dynamic port range used by ECS EC2 bridge-mode
  ingress {
    description     = "Dynamic ports from ALB"
    from_port       = 32768
    to_port         = 65535
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_alb_ror.id]
  }

  # Fargate / awsvpc containers — app ports directly
  ingress {
    description     = "RoR app port from ALB (Puma)"
    from_port       = 3000
    to_port         = 3000
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_alb_ror.id]
  }

  ingress {
    description     = "PHP/Apache app port from ALB"
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_alb_ror.id]
  }

  # SSH from bastion
  ingress {
    description     = "SSH from bastion"
    from_port       = 22
    to_port         = 22
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_bastion_ror.id]
  }

  egress {
    description = "All outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "vishnu_terraform_sg_ecs_ror" }
}

# ─── RDS Security Group ───────────────────────────────────────────────────────
resource "aws_security_group" "vishnu_terraform_sg_rds_ror" {
  name        = "vishnu_terraform_sg_rds_ror"
  description = "Allow MySQL access from ECS tasks only"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id

  ingress {
    description     = "MySQL from ECS"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_ecs_ror.id]
  }

  # Allow from bastion for manual admin
  ingress {
    description     = "MySQL from bastion"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.vishnu_terraform_sg_bastion_ror.id]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "vishnu_terraform_sg_rds_ror" }
}

# ─── Bastion Security Group ───────────────────────────────────────────────────
resource "aws_security_group" "vishnu_terraform_sg_bastion_ror" {
  name        = "vishnu_terraform_sg_bastion_ror"
  description = "SSH access to bastion host"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id

  ingress {
    description = "SSH access"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = { Name = "vishnu_terraform_sg_bastion_ror" }
}
