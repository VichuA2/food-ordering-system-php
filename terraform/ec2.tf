data "aws_ami" "amazon_linux_2023" {
  most_recent = true
  owners      = ["amazon"]

  filter {
    name   = "name"
    values = ["al2023-ami-*-x86_64"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}

# ─── Bastion Host ─────────────────────────────────────────────────────────────
resource "aws_instance" "vishnu_terraform_bastion_ror" {
  ami                         = data.aws_ami.amazon_linux_2023.id
  instance_type               = var.bastion_instance_type
  subnet_id                   = aws_subnet.vishnu_terraform_public_subnet_ror[0].id
  vpc_security_group_ids      = [aws_security_group.vishnu_terraform_sg_bastion_ror.id]
  key_name                    = var.key_pair_name
  associate_public_ip_address = true

  root_block_device {
    volume_type           = "gp3"
    volume_size           = 30
    delete_on_termination = true
    encrypted             = true
  }

  user_data = <<-EOF
    #!/bin/bash
    yum update -y
    yum install -y mysql
    echo "Bastion ready" >> /var/log/user-data.log
  EOF

  tags = { Name = "vishnu_terraform_bastion_ror" }
}


# ─── Elastic IP for Bastion ───────────────────────────────────────────────────
resource "aws_eip" "vishnu_terraform_bastion_eip_ror" {
  instance = aws_instance.vishnu_terraform_bastion_ror.id
  domain   = "vpc"

  tags = { Name = "vishnu_terraform_bastion_eip_ror" }
}
