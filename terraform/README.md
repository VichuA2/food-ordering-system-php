# vishnu-terraform — AWS Infrastructure

Full Terraform deployment for:
- **movie-review-system** (Ruby on Rails) → `movie.vichubro.online`
- **food-ordering-system** (Laravel/PHP) → `php.vichubro.online`

Region: `ap-south-2` (Hyderabad)

---

## File Map

| File | What it creates |
|------|----------------|
| `main.tf` | Provider, S3 backend |
| `variables.tf` | All input variables |
| `vpc.tf` | VPC, subnets, IGW, NAT GW, route tables, NACLs |
| `security_groups.tf` | SGs for ALB, ECS, RDS, Bastion |
| `ec2.tf` | Bastion host + Elastic IP |
| `rds.tf` | RDS MySQL for both apps + subnet/parameter groups |
| `ecr.tf` | ECR repos for both apps + lifecycle policies |
| `iam.tf` | ECS task exec role, EC2 instance profile, CodeBuild/Pipeline roles |
| `alb.tf` | ALB, target groups (RoR + PHP), HTTP→HTTPS redirect, host-based routing |
| `acm.tf` | ACM cert (DNS validation), Route 53 A records |
| `ecs.tf` | ECS cluster (Fargate), task defs + services for both apps |
| `asg.tf` | App Auto Scaling (CPU + Memory), CloudWatch CPU alarms |
| `cloudwatch.tf` | Log groups, SNS topic, dashboard, ALB 5XX alarm |
| `s3.tf` | CodePipeline artifact bucket |
| `codebuild.tf` | CodeBuild projects for both apps |
| `codepipeline.tf` | CodePipeline (Source→Build→Deploy) + GitHub webhooks |
| `outputs.tf` | All useful outputs |

---

## Prerequisites

1. Terraform ≥ 1.5 installed
2. AWS CLI configured (`aws configure`)
3. S3 backend bucket exists: `my-terraform-state-vichu`
4. EC2 key pair `vishnu-terraform-key` created in ap-south-2
5. `buildspec.yml` copied to root of each GitHub repo

---

## Deployment Steps

### 1. Set secrets via environment variables
```bash
export TF_VAR_db_password="YourSecurePassword123!"
export TF_VAR_ror_secret_key_base="$(openssl rand -hex 64)"
export TF_VAR_php_app_key="base64:$(openssl rand -base64 32)"
export TF_VAR_github_oauth_token="ghp_your_token_here"
```

### 2. Create tfvars
```bash
cp terraform.tfvars.example terraform.tfvars
# Edit terraform.tfvars with your non-secret values
```

### 3. Initialize
```bash
terraform init
```

### 4. Plan
```bash
terraform plan -out=tfplan
```

### 5. Apply
```bash
terraform apply tfplan
```

### 6. Push buildspec.yml to each repo
Copy `buildspecs/buildspec_ror.yml` → `movie-review-system/buildspec.yml`
Copy `buildspecs/buildspec_php.yml` → `food-ordering-system/buildspec.yml`

### 7. Push initial Docker images (first-time only)
The ECS service needs at least one image in ECR before the service can start.
```bash
# Authenticate
aws ecr get-login-password --region ap-south-2 | \
  docker login --username AWS --password-stdin \
  $(terraform output -raw ecr_ror_repository_url | cut -d/ -f1)

# Build & push RoR
docker build -t $(terraform output -raw ecr_ror_repository_url):latest .
docker push $(terraform output -raw ecr_ror_repository_url):latest

# Build & push PHP
docker build -t $(terraform output -raw ecr_php_repository_url):latest .
docker push $(terraform output -raw ecr_php_repository_url):latest
```

---

## Naming Convention

All resources follow: `vishnu_terraform_<resource>_ror`

Examples:
- `vishnu_terraform_vpc_ror`
- `vishnu_terraform_sg_alb_ror`
- `vishnu_terraform_rds_php` (PHP-specific suffix for PHP resources)
- `vishnu_terraform_ecs_cluster_ror` (shared cluster)

---

## Architecture

```
Internet
    │
    ▼
Route 53 (movie.vichubro.online / php.vichubro.online)
    │
    ▼
ACM (SSL)
    │
    ▼
ALB (vishnu-terraform-alb-ror)
 ├─ HTTP:80  → redirect 301 → HTTPS
 └─ HTTPS:443
     ├─ movie.vichubro.online → TG:3000 → RoR ECS (Fargate)
     └─ php.vichubro.online   → TG:80   → PHP ECS (Fargate)
                                               │
                                           Private Subnets
                                               │
                                      ┌────────┴────────┐
                                   RDS MySQL          RDS MySQL
                                   (movie_review)   (food_ordering)

GitHub → CodePipeline → CodeBuild → ECR → ECS (rolling deploy)
```

---

## Destroy

```bash
terraform destroy
```
