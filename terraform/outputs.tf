# ─── Networking ───────────────────────────────────────────────────────────────
output "vpc_id" {
  description = "VPC ID"
  value       = aws_vpc.vishnu_terraform_vpc_ror.id
}

output "public_subnet_ids" {
  description = "Public subnet IDs"
  value       = aws_subnet.vishnu_terraform_public_subnet_ror[*].id
}

output "private_subnet_ids" {
  description = "Private subnet IDs"
  value       = aws_subnet.vishnu_terraform_private_subnet_ror[*].id
}

# ─── Bastion ──────────────────────────────────────────────────────────────────
output "bastion_public_ip" {
  description = "Bastion host public IP"
  value       = aws_eip.vishnu_terraform_bastion_eip_ror.public_ip
}

# ─── ALB ──────────────────────────────────────────────────────────────────────
output "alb_dns_name" {
  description = "ALB DNS name"
  value       = aws_lb.vishnu_terraform_alb_ror.dns_name
}

output "ror_app_url" {
  description = "Ruby on Rails app URL"
  value       = "https://${var.ror_subdomain}"
}

output "php_app_url" {
  description = "Laravel app URL"
  value       = "https://${var.php_subdomain}"
}

# ─── ECR ──────────────────────────────────────────────────────────────────────
output "ecr_ror_repository_url" {
  description = "ECR URL for Rails image"
  value       = aws_ecr_repository.vishnu_terraform_ecr_ror.repository_url
}

output "ecr_php_repository_url" {
  description = "ECR URL for Laravel image"
  value       = aws_ecr_repository.vishnu_terraform_ecr_php.repository_url
}

# ─── RDS ──────────────────────────────────────────────────────────────────────
output "rds_shared_endpoint" {
  description = "Shared RDS endpoint"
  value       = aws_db_instance.vishnu_terraform_rds_shared.endpoint
}

# ─── ECS ──────────────────────────────────────────────────────────────────────
output "ecs_cluster_name" {
  description = "ECS cluster name"
  value       = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
}

output "ror_service_name" {
  description = "Rails ECS service name"
  value       = aws_ecs_service.vishnu_terraform_ror_service.name
}

output "php_service_name" {
  description = "Laravel ECS service name"
  value       = aws_ecs_service.vishnu_terraform_php_service.name
}

# ─── CodePipeline ─────────────────────────────────────────────────────────────
output "ror_pipeline_name" {
  description = "Rails CodePipeline name"
  value       = aws_codepipeline.vishnu_terraform_pipeline_ror.name
}

output "php_pipeline_name" {
  description = "Laravel CodePipeline name"
  value       = aws_codepipeline.vishnu_terraform_pipeline_php.name
}

# ─── S3 ───────────────────────────────────────────────────────────────────────
output "artifact_bucket_name" {
  description = "CodePipeline artifact bucket"
  value       = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.bucket
}
