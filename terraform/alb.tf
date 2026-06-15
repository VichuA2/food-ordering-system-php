# ─── Application Load Balancer (Shared) ──────────────────────────────────────

resource "aws_lb" "vishnu_terraform_alb_ror" {
  name               = "vishnu-terraform-alb-ror"
  internal           = false
  load_balancer_type = "application"

  security_groups = [
    aws_security_group.vishnu_terraform_sg_alb_ror.id
  ]

  subnets = aws_subnet.vishnu_terraform_public_subnet_ror[*].id

  enable_deletion_protection = false

  tags = {
    Name = "vishnu_terraform_alb_ror"
  }
}

# ─── Target Group - Ruby on Rails ────────────────────────────────────────────

resource "aws_lb_target_group" "vishnu_terraform_tg_ror" {
  name        = "vishnu-terraform-tg-ror"
  port        = 3000
  protocol    = "HTTP"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id
  target_type = "instance"

  health_check {
    enabled             = true
    path                = "/"
    protocol            = "HTTP"
    port                = "traffic-port"
    healthy_threshold   = 2
    unhealthy_threshold = 3
    interval            = 30
    timeout             = 5
    matcher             = "200-399"
  }

  tags = {
    Name = "vishnu_terraform_tg_ror"
  }
}

# ─── Target Group - Laravel PHP ──────────────────────────────────────────────

resource "aws_lb_target_group" "vishnu_terraform_tg_php" {
  name        = "vishnu-terraform-tg-php"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = aws_vpc.vishnu_terraform_vpc_ror.id
  target_type = "instance"

  health_check {
    enabled             = true
    path                = "/"
    protocol            = "HTTP"
    port                = "traffic-port"
    healthy_threshold   = 2
    unhealthy_threshold = 3
    interval            = 30
    timeout             = 5
    matcher             = "200-399"
  }

  tags = {
    Name = "vishnu_terraform_tg_php"
  }
}

# ─── HTTP Listener - Redirect to HTTPS ───────────────────────────────────────

resource "aws_lb_listener" "vishnu_terraform_http_listener_ror" {
  load_balancer_arn = aws_lb.vishnu_terraform_alb_ror.arn
  port              = 80
  protocol          = "HTTP"

  default_action {
    type = "redirect"

    redirect {
      protocol    = "HTTPS"
      port        = "443"
      status_code = "HTTP_301"
    }
  }

  tags = {
    Name = "vishnu_terraform_http_listener_ror"
  }
}

# ─── HTTPS Listener ──────────────────────────────────────────────────────────

resource "aws_lb_listener" "vishnu_terraform_https_listener_ror" {
  load_balancer_arn = aws_lb.vishnu_terraform_alb_ror.arn

  port       = 443
  protocol   = "HTTPS"
  ssl_policy = "ELBSecurityPolicy-TLS13-1-2-2021-06"

  certificate_arn = "arn:aws:acm:ap-south-1:782208973532:certificate/348e9f5e-6a42-4dc6-b428-fafa0f031d66"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.vishnu_terraform_tg_ror.arn
  }

  tags = {
    Name = "vishnu_terraform_https_listener_ror"
  }
}

# ─── Host Routing: food.vichubro.online → Laravel ────────────────────────────

resource "aws_lb_listener_rule" "vishnu_terraform_php_rule_ror" {
  listener_arn = aws_lb_listener.vishnu_terraform_https_listener_ror.arn
  priority     = 10

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.vishnu_terraform_tg_php.arn
  }

  condition {
    host_header {
      values = [
        "food.vichubro.online"
      ]
    }
  }

  tags = {
    Name = "vishnu_terraform_php_rule_ror"
  }
}
