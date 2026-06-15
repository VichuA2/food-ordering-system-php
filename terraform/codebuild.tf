# ─── CodeBuild – Ruby on Rails ────────────────────────────────────────────────
resource "aws_codebuild_project" "vishnu_terraform_codebuild_ror" {
  name          = "vishnu-terraform-ror-codebuild"
  description   = "Build Docker image for movie-review-system (Rails)"
  service_role  = aws_iam_role.vishnu_terraform_codebuild_role_ror.arn
  build_timeout = 20

  artifacts {
    type = "CODEPIPELINE"
  }

  cache {
    type  = "LOCAL"
    modes = ["LOCAL_DOCKER_LAYER_CACHE", "LOCAL_SOURCE_CACHE"]
  }

  environment {
    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/standard:7.0"
    type                        = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true # required for Docker daemon

    environment_variable {
      name  = "AWS_DEFAULT_REGION"
      value = var.aws_region
    }
    environment_variable {
      name  = "AWS_ACCOUNT_ID"
      value = data.aws_caller_identity.current.account_id
    }
    environment_variable {
      name  = "ECR_REPO_URI"
      value = aws_ecr_repository.vishnu_terraform_ecr_ror.repository_url
    }
    environment_variable {
      name  = "IMAGE_REPO_NAME"
      value = aws_ecr_repository.vishnu_terraform_ecr_ror.name
    }
    environment_variable {
      name  = "ECS_CONTAINER_NAME"
      value = "movie-ror-container"
    }
  }

  source {
    type      = "CODEPIPELINE"
    buildspec = "buildspec.yml"
  }

  logs_config {
    cloudwatch_logs {
      group_name  = "/codebuild/vishnu-terraform-ror"
      stream_name = "build"
    }
  }

  tags = { Name = "vishnu_terraform_codebuild_ror" }
}

# ─── CodeBuild – Laravel / PHP ────────────────────────────────────────────────
resource "aws_codebuild_project" "vishnu_terraform_codebuild_php" {
  name          = "vishnu-terraform-php-codebuild"
  description   = "Build Docker image for food-ordering-system (Laravel)"
  service_role  = aws_iam_role.vishnu_terraform_codebuild_role_ror.arn
  build_timeout = 20

  artifacts {
    type = "CODEPIPELINE"
  }

  cache {
    type  = "LOCAL"
    modes = ["LOCAL_DOCKER_LAYER_CACHE", "LOCAL_SOURCE_CACHE"]
  }

  environment {
    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/standard:7.0"
    type                        = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true

    environment_variable {
      name  = "AWS_DEFAULT_REGION"
      value = var.aws_region
    }
    environment_variable {
      name  = "AWS_ACCOUNT_ID"
      value = data.aws_caller_identity.current.account_id
    }
    environment_variable {
      name  = "ECR_REPO_URI"
      value = aws_ecr_repository.vishnu_terraform_ecr_php.repository_url
    }
    environment_variable {
      name  = "IMAGE_REPO_NAME"
      value = aws_ecr_repository.vishnu_terraform_ecr_php.name
    }
    environment_variable {
      name  = "ECS_CONTAINER_NAME"
      value = "food-php-container"
    }
  }

  source {
    type      = "CODEPIPELINE"
    buildspec = "buildspec.yml"
  }

  logs_config {
    cloudwatch_logs {
      group_name  = "/codebuild/vishnu-terraform-php"
      stream_name = "build"
    }
  }

  tags = { Name = "vishnu_terraform_codebuild_php" }
}
