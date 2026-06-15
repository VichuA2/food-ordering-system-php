# ─── S3 Artifact Bucket ───────────────────────────────────────────────────────
resource "aws_s3_bucket" "vishnu_terraform_artifact_bucket_ror" {
  bucket        = var.artifact_bucket_name
  force_destroy = true

  tags = { Name = "vishnu_terraform_artifact_bucket_ror" }
}

resource "aws_s3_bucket_versioning" "vishnu_terraform_artifact_versioning_ror" {
  bucket = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.id
  versioning_configuration {
    status = "Enabled"
  }
}

resource "aws_s3_bucket_server_side_encryption_configuration" "vishnu_terraform_artifact_sse_ror" {
  bucket = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.id

  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
  }
}

resource "aws_s3_bucket_public_access_block" "vishnu_terraform_artifact_pab_ror" {
  bucket                  = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.id
  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

resource "aws_s3_bucket_lifecycle_configuration" "vishnu_terraform_artifact_lc_ror" {
  bucket = aws_s3_bucket.vishnu_terraform_artifact_bucket_ror.id

  rule {
    id     = "expire-old-artifacts"
    status = "Enabled"
    filter {}

    expiration {
      days = 30
    }

    noncurrent_version_expiration {
      noncurrent_days = 7
    }
  }
}
