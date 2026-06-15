# ─── CodePipeline – Ruby on Rails ─────────────────────────────────────────────
resource "aws_codepipeline" "vishnu_terraform_pipeline_ror" {
  name     = "vishnu-terraform-ror-pipeline"
  role_arn = aws_iam_role.vishnu_terraform_codepipeline_role_ror.arn

  artifact_store {
    location = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.bucket
    type     = "S3"
  }

  # ── Stage 1: Source (GitHub OAuth v1 — works in ap-south-2) ─────────────────
  stage {
    name = "Source"

    action {
      name             = "GitHub_Source"
      category         = "Source"
      owner            = "ThirdParty"
      provider         = "GitHub"
      version          = "1"
      output_artifacts = ["source_output"]

      configuration = {
        Owner                = var.github_owner
        Repo                 = var.ror_github_repo
        Branch               = var.github_branch
        OAuthToken           = var.github_oauth_token
        PollForSourceChanges = false # use webhook instead
      }
    }
  }

  # ── Stage 2: Build ───────────────────────────────────────────────────────────
  stage {
    name = "Build"

    action {
      name             = "CodeBuild_Build"
      category         = "Build"
      owner            = "AWS"
      provider         = "CodeBuild"
      version          = "1"
      input_artifacts  = ["source_output"]
      output_artifacts = ["build_output"]

      configuration = {
        ProjectName = aws_codebuild_project.vishnu_terraform_codebuild_ror.name
      }
    }
  }

  # ── Stage 3: Deploy ──────────────────────────────────────────────────────────
  stage {
    name = "Deploy"

    action {
      name            = "ECS_Deploy"
      category        = "Deploy"
      owner           = "AWS"
      provider        = "ECS"
      version         = "1"
      input_artifacts = ["build_output"]

      configuration = {
        ClusterName = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
        ServiceName = aws_ecs_service.vishnu_terraform_ror_service.name
        FileName    = "imagedefinitions.json"
      }
    }
  }

  tags = { Name = "vishnu_terraform_pipeline_ror" }
}

# GitHub webhook for RoR pipeline
resource "aws_codepipeline_webhook" "vishnu_terraform_webhook_ror" {
  name            = "vishnu-terraform-webhook-ror"
  authentication  = "GITHUB_HMAC"
  target_action   = "GitHub_Source"
  target_pipeline = aws_codepipeline.vishnu_terraform_pipeline_ror.name

  authentication_configuration {
    secret_token = var.github_oauth_token
  }

  filter {
    json_path    = "$.ref"
    match_equals = "refs/heads/${var.github_branch}"
  }

  tags = { Name = "vishnu_terraform_webhook_ror" }
}

# ─── CodePipeline – Laravel / PHP ─────────────────────────────────────────────
resource "aws_codepipeline" "vishnu_terraform_pipeline_php" {
  name     = "vishnu-terraform-php-pipeline"
  role_arn = aws_iam_role.vishnu_terraform_codepipeline_role_ror.arn

  artifact_store {
    location = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.bucket
    type     = "S3"
  }

  stage {
    name = "Source"

    action {
      name             = "GitHub_Source"
      category         = "Source"
      owner            = "ThirdParty"
      provider         = "GitHub"
      version          = "1"
      output_artifacts = ["source_output"]

      configuration = {
        Owner                = var.github_owner
        Repo                 = var.php_github_repo
        Branch               = var.github_branch
        OAuthToken           = var.github_oauth_token
        PollForSourceChanges = false
      }
    }
  }

  stage {
    name = "Build"

    action {
      name             = "CodeBuild_Build"
      category         = "Build"
      owner            = "AWS"
      provider         = "CodeBuild"
      version          = "1"
      input_artifacts  = ["source_output"]
      output_artifacts = ["build_output"]

      configuration = {
        ProjectName = aws_codebuild_project.vishnu_terraform_codebuild_php.name
      }
    }
  }

  stage {
    name = "Deploy"

    action {
      name            = "ECS_Deploy"
      category        = "Deploy"
      owner           = "AWS"
      provider        = "ECS"
      version         = "1"
      input_artifacts = ["build_output"]

      configuration = {
        ClusterName = aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name
        ServiceName = aws_ecs_service.vishnu_terraform_php_service.name
        FileName    = "imagedefinitions.json"
      }
    }
  }

  tags = { Name = "vishnu_terraform_pipeline_php" }
}

resource "aws_codepipeline_webhook" "vishnu_terraform_webhook_php" {
  name            = "vishnu-terraform-webhook-php"
  authentication  = "GITHUB_HMAC"
  target_action   = "GitHub_Source"
  target_pipeline = aws_codepipeline.vishnu_terraform_pipeline_php.name

  authentication_configuration {
    secret_token = var.github_oauth_token
  }

  filter {
    json_path    = "$.ref"
    match_equals = "refs/heads/${var.github_branch}"
  }

  tags = { Name = "vishnu_terraform_webhook_php" }
}
