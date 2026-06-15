# ─── General ──────────────────────────────────────────────────────────────────
variable "aws_region" {
  description = "AWS region"
  type        = string
  default     = "ap-south-1"
}

variable "environment" {
  description = "Deployment environment"
  type        = string
  default     = "production"
}

# ─── VPC & Networking ─────────────────────────────────────────────────────────
variable "vpc_cidr" {
  description = "CIDR block for the VPC"
  type        = string
  default     = "10.30.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "CIDR blocks for public subnets (one per AZ)"
  type        = list(string)
  default     = ["10.30.1.0/24", "10.30.2.0/24"]
}

variable "private_subnet_cidrs" {
  description = "CIDR blocks for private subnets (one per AZ)"
  type        = list(string)
  default     = ["10.30.3.0/24", "10.30.4.0/24"]
}

variable "availability_zones" {
  description = "Availability zones to use"
  type        = list(string)
  default     = ["ap-south-1a", "ap-south-1b"]
}

# ─── Domain & SSL ─────────────────────────────────────────────────────────────
variable "domain_name" {
  description = "Root domain managed in Route 53"
  type        = string
  default     = "vichubro.online"
}

variable "ror_subdomain" {
  description = "Subdomain for the Rails app"
  type        = string
  default     = "movies.vichubro.online"
}

variable "php_subdomain" {
  description = "Subdomain for the Laravel app"
  type        = string
  default     = "food.vichubro.online"
}

# ─── EC2 / Bastion ────────────────────────────────────────────────────────────
variable "key_pair_name" {
  description = "EC2 key pair name"
  type        = string
  default     = "vishnu-terraform-key"
}

variable "bastion_instance_type" {
  description = "Instance type for bastion host"
  type        = string
  default     = "t3.micro"
}

variable "ecs_instance_type" {
  description = "Instance type for ECS EC2 launch-type nodes"
  type        = string
  default     = "t3.small"
}

# ─── ECS ──────────────────────────────────────────────────────────────────────
variable "ror_task_cpu" {
  description = "CPU units for the RoR ECS task"
  type        = number
  default     = 512
}

variable "ror_task_memory" {
  description = "Memory (MB) for the RoR ECS task"
  type        = number
  default     = 1024
}

variable "php_task_cpu" {
  description = "CPU units for the PHP ECS task"
  type        = number
  default     = 512
}

variable "php_task_memory" {
  description = "Memory (MB) for the PHP ECS task"
  type        = number
  default     = 1024
}

variable "ror_service_desired_count" {
  description = "Desired number of RoR ECS tasks"
  type        = number
  default     = 1
}

variable "php_service_desired_count" {
  description = "Desired number of PHP ECS tasks"
  type        = number
  default     = 1
}

# ─── RDS ──────────────────────────────────────────────────────────────────────
variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.micro"
}

variable "db_engine_version" {
  description = "MySQL engine version"
  type        = string
  default     = "8.0"
}

variable "ror_db_name" {
  description = "Database name for Rails app"
  type        = string
  default     = "movie_review_production"
}

variable "php_db_name" {
  description = "Database name for Laravel app"
  type        = string
  default     = "food_ordering_production"
}

variable "db_username" {
  description = "Master username for RDS"
  type        = string
  default     = "admin"
  sensitive   = true
}

variable "db_password" {
  description = "Master password for RDS — provide via TF_VAR_db_password env var"
  type        = string
  sensitive   = true
}

# ─── Rails app config ─────────────────────────────────────────────────────────
variable "ror_secret_key_base" {
  description = "SECRET_KEY_BASE for Rails — provide via TF_VAR_ror_secret_key_base"
  type        = string
  sensitive   = true
}

variable "ror_rails_env" {
  description = "RAILS_ENV value"
  type        = string
  default     = "production"
}

# ─── Laravel app config ───────────────────────────────────────────────────────
variable "php_app_key" {
  description = "Laravel APP_KEY — provide via TF_VAR_php_app_key"
  type        = string
  sensitive   = true
}

variable "php_app_env" {
  description = "Laravel APP_ENV value"
  type        = string
  default     = "production"
}

# ─── Auto Scaling ─────────────────────────────────────────────────────────────
variable "asg_min_size" {
  description = "Minimum number of ECS EC2 instances"
  type        = number
  default     = 1
}

variable "asg_max_size" {
  description = "Maximum number of ECS EC2 instances"
  type        = number
  default     = 3
}

variable "asg_desired_capacity" {
  description = "Desired number of ECS EC2 instances"
  type        = number
  default     = 1
}

# ─── GitHub / CodePipeline ────────────────────────────────────────────────────
variable "github_owner" {
  description = "GitHub username or org"
  type        = string
  default     = "VichuA2"
}

variable "ror_github_repo" {
  description = "GitHub repo name for Rails app"
  type        = string
  default     = "movie-review-system"
}

variable "php_github_repo" {
  description = "GitHub repo name for Laravel app"
  type        = string
  default     = "food-ordering-system-php"
}

variable "github_branch" {
  description = "Branch to deploy from"
  type        = string
  default     = "terraform-migration"
}

variable "github_oauth_token" {
  description = "GitHub OAuth token — provide via TF_VAR_github_oauth_token"
  type        = string
  sensitive   = true
}

# ─── CodePipeline S3 artifact bucket ─────────────────────────────────────────
variable "artifact_bucket_name" {
  description = "S3 bucket for CodePipeline artifacts"
  type        = string
  default     = "vishnu-terraform-pipeline-artifacts"
}
