# ─── ECR – Ruby on Rails ──────────────────────────────────────────────────────
resource "aws_ecr_repository" "vishnu_terraform_ecr_ror" {
  name                 = "vishnu-terraform/movie-ror"
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }

  encryption_configuration {
    encryption_type = "AES256"
  }

  tags = { Name = "vishnu_terraform_ecr_ror" }
}

resource "aws_ecr_lifecycle_policy" "vishnu_terraform_ecr_lcp_ror" {
  repository = aws_ecr_repository.vishnu_terraform_ecr_ror.name

  policy = jsonencode({
    rules = [
      {
        rulePriority = 1
        description  = "Keep last 10 tagged images"
        selection = {
          tagStatus     = "tagged"
          tagPrefixList = ["v"]
          countType     = "imageCountMoreThan"
          countNumber   = 10
        }
        action = { type = "expire" }
      },
      {
        rulePriority = 2
        description  = "Expire untagged images older than 7 days"
        selection = {
          tagStatus   = "untagged"
          countType   = "sinceImagePushed"
          countUnit   = "days"
          countNumber = 7
        }
        action = { type = "expire" }
      }
    ]
  })
}

# ─── ECR – Laravel / PHP ──────────────────────────────────────────────────────
resource "aws_ecr_repository" "vishnu_terraform_ecr_php" {
  name                 = "vishnu-terraform/food-php"
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }

  encryption_configuration {
    encryption_type = "AES256"
  }

  tags = { Name = "vishnu_terraform_ecr_php" }
}

resource "aws_ecr_lifecycle_policy" "vishnu_terraform_ecr_lcp_php" {
  repository = aws_ecr_repository.vishnu_terraform_ecr_php.name

  policy = jsonencode({
    rules = [
      {
        rulePriority = 1
        description  = "Keep last 10 tagged images"
        selection = {
          tagStatus     = "tagged"
          tagPrefixList = ["v"]
          countType     = "imageCountMoreThan"
          countNumber   = 10
        }
        action = { type = "expire" }
      },
      {
        rulePriority = 2
        description  = "Expire untagged images older than 7 days"
        selection = {
          tagStatus   = "untagged"
          countType   = "sinceImagePushed"
          countUnit   = "days"
          countNumber = 7
        }
        action = { type = "expire" }
      }
    ]
  })
}
