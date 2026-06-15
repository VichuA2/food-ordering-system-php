# ─── ECS Task Execution Role ──────────────────────────────────────────────────
resource "aws_iam_role" "vishnu_terraform_ecs_task_exec_role_ror" {
  name = "vishnu_terraform_ecs_task_exec_role_ror"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "ecs-tasks.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })

  tags = { Name = "vishnu_terraform_ecs_task_exec_role_ror" }
}

resource "aws_iam_role_policy_attachment" "vishnu_terraform_ecs_task_exec_attach_ror" {
  role       = aws_iam_role.vishnu_terraform_ecs_task_exec_role_ror.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

# Extra policy: allow reading secrets / SSM and writing to CloudWatch
resource "aws_iam_role_policy" "vishnu_terraform_ecs_task_exec_extra_ror" {
  name = "vishnu_terraform_ecs_task_exec_extra_ror"
  role = aws_iam_role.vishnu_terraform_ecs_task_exec_role_ror.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ssm:GetParameters",
          "secretsmanager:GetSecretValue",
          "kms:Decrypt"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutLogEvents"
        ]
        Resource = "*"
      }
    ]
  })
}

# ─── ECS Task Role (runtime permissions) ─────────────────────────────────────
resource "aws_iam_role" "vishnu_terraform_ecs_task_role_ror" {
  name = "vishnu_terraform_ecs_task_role_ror"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "ecs-tasks.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })

  tags = { Name = "vishnu_terraform_ecs_task_role_ror" }
}

resource "aws_iam_role_policy" "vishnu_terraform_ecs_task_policy_ror" {
  name = "vishnu_terraform_ecs_task_policy_ror"
  role = aws_iam_role.vishnu_terraform_ecs_task_role_ror.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutLogEvents",
          "s3:GetObject",
          "s3:PutObject"
        ]
        Resource = "*"
      }
    ]
  })
}

# ─── EC2 Instance Profile for ECS EC2 launch type ────────────────────────────
resource "aws_iam_role" "vishnu_terraform_ec2_ecs_role_ror" {
  name = "vishnu_terraform_ec2_ecs_role_ror"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "ec2.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })

  tags = { Name = "vishnu_terraform_ec2_ecs_role_ror" }
}

resource "aws_iam_role_policy_attachment" "vishnu_terraform_ec2_ecs_attach_ror" {
  role       = aws_iam_role.vishnu_terraform_ec2_ecs_role_ror.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceforEC2Role"
}

resource "aws_iam_role_policy_attachment" "vishnu_terraform_ec2_ssm_attach_ror" {
  role       = aws_iam_role.vishnu_terraform_ec2_ecs_role_ror.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_instance_profile" "vishnu_terraform_ec2_ecs_profile_ror" {
  name = "vishnu_terraform_ec2_ecs_profile_ror"
  role = aws_iam_role.vishnu_terraform_ec2_ecs_role_ror.name
}

# ─── CodeBuild Role ───────────────────────────────────────────────────────────
resource "aws_iam_role" "vishnu_terraform_codebuild_role_ror" {
  name = "vishnu_terraform_codebuild_role_ror"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "codebuild.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })

  tags = { Name = "vishnu_terraform_codebuild_role_ror" }
}

resource "aws_iam_role_policy" "vishnu_terraform_codebuild_policy_ror" {
  name = "vishnu_terraform_codebuild_policy_ror"
  role = aws_iam_role.vishnu_terraform_codebuild_role_ror.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutLogEvents"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "ecr:GetAuthorizationToken",
          "ecr:BatchCheckLayerAvailability",
          "ecr:GetDownloadUrlForLayer",
          "ecr:BatchGetImage",
          "ecr:InitiateLayerUpload",
          "ecr:UploadLayerPart",
          "ecr:CompleteLayerUpload",
          "ecr:PutImage"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:GetObjectVersion",
          "s3:PutObject",
          "s3:GetBucketAcl",
          "s3:GetBucketLocation"
        ]
        Resource = "*"
      }
    ]
  })
}

# ─── CodePipeline Role ────────────────────────────────────────────────────────
resource "aws_iam_role" "vishnu_terraform_codepipeline_role_ror" {
  name = "vishnu_terraform_codepipeline_role_ror"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Effect    = "Allow"
      Principal = { Service = "codepipeline.amazonaws.com" }
      Action    = "sts:AssumeRole"
    }]
  })

  tags = { Name = "vishnu_terraform_codepipeline_role_ror" }
}

resource "aws_iam_role_policy" "vishnu_terraform_codepipeline_policy_ror" {
  name = "vishnu_terraform_codepipeline_policy_ror"
  role = aws_iam_role.vishnu_terraform_codepipeline_role_ror.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:GetObjectVersion",
          "s3:PutObject",
          "s3:GetBucketAcl",
          "s3:GetBucketLocation",
          "s3:ListBucket"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "codebuild:BatchGetBuilds",
          "codebuild:StartBuild"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "ecs:DescribeServices",
          "ecs:DescribeTaskDefinition",
          "ecs:DescribeTasks",
          "ecs:ListTasks",
          "ecs:RegisterTaskDefinition",
          "ecs:UpdateService",
          "ecs:TagResource"

        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = [
          "iam:PassRole"
        ]
        Resource = "*"
      }
    ]
  })
}
