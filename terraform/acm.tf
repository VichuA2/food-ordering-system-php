# ─────────────────────────────────────────────────────────────────────────────
# Route53 Hosted Zone
# ─────────────────────────────────────────────────────────────────────────────

data "aws_route53_zone" "vishnu_terraform_zone_ror" {
  name         = var.domain_name
  private_zone = false
}

# ─────────────────────────────────────────────────────────────────────────────
# movies.vichubro.online
# ─────────────────────────────────────────────────────────────────────────────

resource "aws_route53_record" "vishnu_terraform_dns_ror" {
  zone_id = data.aws_route53_zone.vishnu_terraform_zone_ror.zone_id
  name    = var.ror_subdomain
  type    = "A"

  alias {
    name                   = aws_lb.vishnu_terraform_alb_ror.dns_name
    zone_id                = aws_lb.vishnu_terraform_alb_ror.zone_id
    evaluate_target_health = true
  }
}

# ─────────────────────────────────────────────────────────────────────────────
# food.vichubro.online
# ─────────────────────────────────────────────────────────────────────────────

resource "aws_route53_record" "vishnu_terraform_dns_php" {
  zone_id = data.aws_route53_zone.vishnu_terraform_zone_ror.zone_id
  name    = var.php_subdomain
  type    = "A"

  alias {
    name                   = aws_lb.vishnu_terraform_alb_ror.dns_name
    zone_id                = aws_lb.vishnu_terraform_alb_ror.zone_id
    evaluate_target_health = true
  }
}
