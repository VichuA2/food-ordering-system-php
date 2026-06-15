# ─── DB Subnet Group ─────────────────────────────────────────────────────────
resource "aws_db_subnet_group" "vishnu_terraform_db_subnet_group_ror" {
  name       = "vishnu-terraform-db-subnet-group-ror"
  subnet_ids = aws_subnet.vishnu_terraform_private_subnet_ror[*].id

  tags = {
    Name = "vishnu_terraform_db_subnet_group_ror"
  }
}

# ─── RDS Parameter Group ──────────────────────────────────────────────────────
resource "aws_db_parameter_group" "vishnu_terraform_db_pg_ror" {
  name   = "vishnu-terraform-db-pg-ror"
  family = "mysql8.0"

  parameter {
    name  = "character_set_server"
    value = "utf8mb4"
  }

  parameter {
    name  = "collation_server"
    value = "utf8mb4_unicode_ci"
  }

  tags = {
    Name = "vishnu_terraform_db_pg_ror"
  }
}

# ─── Shared MySQL RDS Instance ────────────────────────────────────────────────
resource "aws_db_instance" "vishnu_terraform_rds_shared" {
  identifier = "vishnu-terraform-rds-shared"

  engine         = "mysql"
  engine_version = var.db_engine_version
  instance_class = var.db_instance_class

  allocated_storage     = 20
  max_allocated_storage = 100

  storage_type      = "gp3"
  storage_encrypted = true

  # First database created automatically
  db_name = var.php_db_name

  username = var.db_username
  password = var.db_password

  db_subnet_group_name   = aws_db_subnet_group.vishnu_terraform_db_subnet_group_ror.name
  parameter_group_name   = aws_db_parameter_group.vishnu_terraform_db_pg_ror.name
  vpc_security_group_ids = [aws_security_group.vishnu_terraform_sg_rds_ror.id]

  publicly_accessible = false
  multi_az            = false

  # Required because AWS is restricting your account
  backup_retention_period = 1

  auto_minor_version_upgrade = true

  deletion_protection = false
  skip_final_snapshot = true

  tags = {
    Name = "vishnu_terraform_rds_shared"
  }
}
