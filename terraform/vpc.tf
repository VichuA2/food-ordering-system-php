# ─── VPC ──────────────────────────────────────────────────────────────────────
resource "aws_vpc" "vishnu_terraform_vpc_ror" {
  cidr_block           = var.vpc_cidr
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = { Name = "vishnu_terraform_vpc_ror" }
}

# ─── Internet Gateway ─────────────────────────────────────────────────────────
resource "aws_internet_gateway" "vishnu_terraform_igw_ror" {
  vpc_id = aws_vpc.vishnu_terraform_vpc_ror.id

  tags = { Name = "vishnu_terraform_igw_ror" }
}

# ─── Public Subnets ───────────────────────────────────────────────────────────
resource "aws_subnet" "vishnu_terraform_public_subnet_ror" {
  count                   = length(var.public_subnet_cidrs)
  vpc_id                  = aws_vpc.vishnu_terraform_vpc_ror.id
  cidr_block              = var.public_subnet_cidrs[count.index]
  availability_zone       = var.availability_zones[count.index]
  map_public_ip_on_launch = true

  tags = { Name = "vishnu_terraform_public_subnet_ror_${count.index + 1}" }
}

# ─── Private Subnets ──────────────────────────────────────────────────────────
resource "aws_subnet" "vishnu_terraform_private_subnet_ror" {
  count             = length(var.private_subnet_cidrs)
  vpc_id            = aws_vpc.vishnu_terraform_vpc_ror.id
  cidr_block        = var.private_subnet_cidrs[count.index]
  availability_zone = var.availability_zones[count.index]

  tags = { Name = "vishnu_terraform_private_subnet_ror_${count.index + 1}" }
}

# ─── Elastic IPs for NAT Gateways ────────────────────────────────────────────
resource "aws_eip" "vishnu_terraform_nat_eip_ror" {
  count  = length(var.public_subnet_cidrs)
  domain = "vpc"

  tags = { Name = "vishnu_terraform_nat_eip_ror_${count.index + 1}" }
}

# ─── NAT Gateways (one per public subnet / AZ) ───────────────────────────────
resource "aws_nat_gateway" "vishnu_terraform_nat_ror" {
  count         = length(var.public_subnet_cidrs)
  allocation_id = aws_eip.vishnu_terraform_nat_eip_ror[count.index].id
  subnet_id     = aws_subnet.vishnu_terraform_public_subnet_ror[count.index].id

  tags = { Name = "vishnu_terraform_nat_ror_${count.index + 1}" }

  depends_on = [aws_internet_gateway.vishnu_terraform_igw_ror]
}

# ─── Public Route Table ───────────────────────────────────────────────────────
resource "aws_route_table" "vishnu_terraform_public_rt_ror" {
  vpc_id = aws_vpc.vishnu_terraform_vpc_ror.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.vishnu_terraform_igw_ror.id
  }

  tags = { Name = "vishnu_terraform_public_rt_ror" }
}

resource "aws_route_table_association" "vishnu_terraform_public_rta_ror" {
  count          = length(var.public_subnet_cidrs)
  subnet_id      = aws_subnet.vishnu_terraform_public_subnet_ror[count.index].id
  route_table_id = aws_route_table.vishnu_terraform_public_rt_ror.id
}

# ─── Private Route Tables (one per AZ) ───────────────────────────────────────
resource "aws_route_table" "vishnu_terraform_private_rt_ror" {
  count  = length(var.private_subnet_cidrs)
  vpc_id = aws_vpc.vishnu_terraform_vpc_ror.id

  route {
    cidr_block     = "0.0.0.0/0"
    nat_gateway_id = aws_nat_gateway.vishnu_terraform_nat_ror[count.index].id
  }

  tags = { Name = "vishnu_terraform_private_rt_ror_${count.index + 1}" }
}

resource "aws_route_table_association" "vishnu_terraform_private_rta_ror" {
  count          = length(var.private_subnet_cidrs)
  subnet_id      = aws_subnet.vishnu_terraform_private_subnet_ror[count.index].id
  route_table_id = aws_route_table.vishnu_terraform_private_rt_ror[count.index].id
}

# ─── Network ACL – Public ─────────────────────────────────────────────────────
resource "aws_network_acl" "vishnu_terraform_public_nacl_ror" {
  vpc_id     = aws_vpc.vishnu_terraform_vpc_ror.id
  subnet_ids = aws_subnet.vishnu_terraform_public_subnet_ror[*].id

  # Inbound: allow HTTP
  ingress {
    rule_no    = 100
    protocol   = "tcp"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 80
    to_port    = 80
  }

  # Inbound: allow HTTPS
  ingress {
    rule_no    = 110
    protocol   = "tcp"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 443
    to_port    = 443
  }

  # Inbound: allow SSH from anywhere (restrict to your IP in production)
  ingress {
    rule_no    = 120
    protocol   = "tcp"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 22
    to_port    = 22
  }

  # Inbound: allow ephemeral return traffic
  ingress {
    rule_no    = 130
    protocol   = "tcp"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 1024
    to_port    = 65535
  }

  # Outbound: allow all
  egress {
    rule_no    = 100
    protocol   = "-1"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 0
    to_port    = 0
  }

  tags = { Name = "vishnu_terraform_public_nacl_ror" }
}

# ─── Network ACL – Private ────────────────────────────────────────────────────
resource "aws_network_acl" "vishnu_terraform_private_nacl_ror" {
  vpc_id     = aws_vpc.vishnu_terraform_vpc_ror.id
  subnet_ids = aws_subnet.vishnu_terraform_private_subnet_ror[*].id

  # Inbound: allow all from within VPC
  ingress {
    rule_no    = 100
    protocol   = "-1"
    action     = "allow"
    cidr_block = var.vpc_cidr
    from_port  = 0
    to_port    = 0
  }

  # Inbound: allow ephemeral return traffic from internet (via NAT)
  ingress {
    rule_no    = 110
    protocol   = "tcp"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 1024
    to_port    = 65535
  }

  # Outbound: allow all
  egress {
    rule_no    = 100
    protocol   = "-1"
    action     = "allow"
    cidr_block = "0.0.0.0/0"
    from_port  = 0
    to_port    = 0
  }

  tags = { Name = "vishnu_terraform_private_nacl_ror" }
}
