# ─── SNS Topic for Alarms ─────────────────────────────────────────────────────
resource "aws_sns_topic" "vishnu_terraform_sns_ror" {
  name = "vishnu-terraform-alerts-ror"
  tags = { Name = "vishnu_terraform_sns_ror" }
}

resource "aws_sns_topic_subscription" "vishnu_terraform_sns_email_ror" {
  topic_arn = aws_sns_topic.vishnu_terraform_sns_ror.arn
  protocol  = "email"
  endpoint  = "vishnuarumugam0207@gmail.com"
}

# ─── CloudWatch Dashboard ─────────────────────────────────────────────────────
resource "aws_cloudwatch_dashboard" "vishnu_terraform_dashboard_ror" {
  dashboard_name = "vishnu-terraform-dashboard-ror"

  dashboard_body = jsonencode({
    widgets = [
      {
        type   = "metric"
        x      = 0
        y      = 0
        width  = 12
        height = 6
        properties = {
          title   = "RoR ECS CPU Utilization"
          region  = var.aws_region
          period  = 60
          stat    = "Average"
          view    = "timeSeries"
          stacked = false
          metrics = [
            ["AWS/ECS", "CPUUtilization",
              "ClusterName", aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name,
            "ServiceName", aws_ecs_service.vishnu_terraform_ror_service.name]
          ]
          annotations = { horizontal = [] }
        }
      },
      {
        type   = "metric"
        x      = 12
        y      = 0
        width  = 12
        height = 6
        properties = {
          title   = "PHP ECS CPU Utilization"
          region  = var.aws_region
          period  = 60
          stat    = "Average"
          view    = "timeSeries"
          stacked = false
          metrics = [
            ["AWS/ECS", "CPUUtilization",
              "ClusterName", aws_ecs_cluster.vishnu_terraform_ecs_cluster_ror.name,
            "ServiceName", aws_ecs_service.vishnu_terraform_php_service.name]
          ]
          annotations = { horizontal = [] }
        }
      },
      {
        type   = "metric"
        x      = 0
        y      = 6
        width  = 12
        height = 6
        properties = {
          title   = "ALB Request Count"
          region  = var.aws_region
          period  = 60
          stat    = "Sum"
          view    = "timeSeries"
          stacked = false
          metrics = [
            ["AWS/ApplicationELB", "RequestCount",
            "LoadBalancer", aws_lb.vishnu_terraform_alb_ror.arn_suffix]
          ]
          annotations = { horizontal = [] }
        }
      },
      {
        type   = "metric"
        x      = 12
        y      = 6
        width  = 12
        height = 6
        properties = {
          title   = "ALB 5XX Errors"
          region  = var.aws_region
          period  = 60
          stat    = "Sum"
          view    = "timeSeries"
          stacked = false
          metrics = [
            ["AWS/ApplicationELB", "HTTPCode_ELB_5XX_Count",
            "LoadBalancer", aws_lb.vishnu_terraform_alb_ror.arn_suffix]
          ]
          annotations = { horizontal = [] }
        }
      }
    ]
  })
}

# ─── ALB 5XX Alarm ────────────────────────────────────────────────────────────
resource "aws_cloudwatch_metric_alarm" "vishnu_terraform_alb_5xx_ror" {
  alarm_name          = "vishnu_terraform_alb_5xx_ror"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = 1
  metric_name         = "HTTPCode_ELB_5XX_Count"
  namespace           = "AWS/ApplicationELB"
  period              = 60
  statistic           = "Sum"
  threshold           = 10
  treat_missing_data  = "notBreaching"

  dimensions = {
    LoadBalancer = aws_lb.vishnu_terraform_alb_ror.arn_suffix
  }

  alarm_actions = [aws_sns_topic.vishnu_terraform_sns_ror.arn]
  tags          = { Name = "vishnu_terraform_alb_5xx_ror" }
}
