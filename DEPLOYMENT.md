# Proyex Production Deployment on DigitalOcean

## TL;DR (After One-Time Setup)

Once everything is configured, **deployment is this simple:**

```bash
git push main
# Wait 3-5 minutes...
# Your app is live on https://yourdomain.com
```

That's it. No SSH, no manual commands on the server. GitHub Actions handles the rest automatically.

## What You Need To Do

One time only:

1. Create a DigitalOcean Ubuntu droplet.
2. Point your domain DNS A record to droplet IP.
3. Add GitHub Actions secrets: DEPLOY_KEY, DEPLOY_HOST, DEPLOY_USER.
4. On the droplet, clone repo into /opt/proyex and create production .env.
5. Configure SSL (Certbot) and Nginx.

Every deployment:

1. Commit your code.
2. Push to main.
3. Open GitHub Actions and confirm the workflow is green.

## Architecture

- **GitHub Actions** (`.github/workflows/build-and-deploy.yml`): On every push to main, builds and pushes both images (proyex-app and proyex-web) to GHCR, then deploys to your DigitalOcean droplet
- **DigitalOcean Droplet**: Runs containers by pulling pre-built images (no local Docker builds needed)
- **Result**: Every `git push main` automatically updates your production app in ~3-5 minutes

## Prerequisites

- DigitalOcean account with a droplet (Ubuntu 22.04 LTS, $6/mo minimum)
- Domain name with DNS pointing to droplet IP
- GitHub repository with push access
- SSH key pair for droplet access

## Initial Setup (One Time)

### 1. Create SSH Key for GitHub Actions

Generate SSH key for deployments:
```bash
ssh-keygen -t ed25519 -f ~/.ssh/proyex_deploy -C "github-actions-deploy"
```

Add public key to droplet's `~/.ssh/authorized_keys`:
```bash
cat ~/.ssh/proyex_deploy.pub | ssh root@YOUR_DROPLET_IP 'cat >> ~/.ssh/authorized_keys'
```

### 2. Add GitHub Secrets

Go to GitHub repo → Settings → Secrets and Variables → Actions → New repository secret

Add these secrets:

| Secret Name | Value |
|------------|-------|
| `DEPLOY_KEY` | Contents of `~/.ssh/proyex_deploy` (private key) |
| `DEPLOY_HOST` | Your droplet IP or domain |
| `DEPLOY_USER` | `root` (or your non-root user) |

### 3. Prepare DigitalOcean Droplet

SSH into droplet:
```bash
ssh root@YOUR_DROPLET_IP
```

Update system and install Docker:
```bash
apt update && apt upgrade -y
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
usermod -aG docker root
newgrp docker
```

Clone repository:
```bash
cd /opt
git clone https://github.com/your-username/proyex.git
cd proyex
```

Create production `.env`:
```bash
cp .env.example .env
nano .env
```

**Production `.env` settings:**
```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=db
DB_PORT=3306
DB_DATABASE=proyex_prod
DB_USERNAME=proyex_prod
DB_PASSWORD=StrongPassword123!@#
DB_ROOT_PASSWORD=StrongRootPassword123!@#

REDIS_HOST=redis
REDIS_PASSWORD=StrongRedisPassword123!@#

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. Set Up Nginx Reverse Proxy with SSL

Install Nginx:
```bash
apt install nginx certbot python3-certbot-nginx -y
```

Create Nginx config:
```bash
nano /etc/nginx/sites-available/proyex
```

Paste:
```nginx
upstream proyex_fpm {
    server app:9000;
}

server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    root /opt/proyex/public;
    index index.php;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass proyex_fpm;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_SCHEME https;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable and test:
```bash
ln -s /etc/nginx/sites-available/proyex /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

Get SSL certificate:
```bash
certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com
```

### 5. Initial Deployment

Pull images and start containers:
```bash
cd /opt/proyex
docker compose pull
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed  # optional
```

Verify:
```bash
docker compose ps
```

### 6. Set Up Auto-Start

Create systemd service:
```bash
nano /etc/systemd/system/proyex.service
```

Paste:
```ini
[Unit]
Description=Proyex Docker Compose Application
Requires=docker.service
After=docker.service network-online.target
Wants=network-online.target

[Service]
Type=oneshot
WorkingDirectory=/opt/proyex
ExecStart=/usr/local/bin/docker compose up -d
ExecStop=/usr/local/bin/docker compose down
RemainAfterExit=yes
Restart=on-failure
RestartSec=10s

[Install]
WantedBy=multi-user.target
```

Enable:
```bash
systemctl daemon-reload
systemctl enable proyex
systemctl start proyex
```

### 7. Firewall

```bash
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

## Continuous Deployment

**Every push to `main` branch automatically:**

1. GitHub Actions builds Docker image
2. Pushes to `ghcr.io/your-username/proyex:latest`
3. SSHs into droplet
4. Pulls latest images
5. Runs `docker compose up -d`
6. Runs migrations
7. Clears caches

**No manual intervention needed** — just `git push`.

## Manual Deployments

If you need to manually trigger a deployment:

1. Go to GitHub repo → Actions → "Build and Deploy to Production"
2. Click "Run workflow"
3. Select branch and click "Run workflow"

## Database Backups

Create backup script:
```bash
nano /opt/proyex/backup-db.sh
```

Paste:
```bash
#!/bin/bash
BACKUP_DIR="/opt/proyex/backups"
mkdir -p $BACKUP_DIR
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/proyex_$TIMESTAMP.sql.gz"

docker compose exec -T db mysqldump \
  -u proyex_prod \
  -pStrongPassword123!@# \
  proyex_prod | gzip > $BACKUP_FILE

# Keep 30 days of backups
find $BACKUP_DIR -type f -mtime +30 -delete
echo "Backup: $BACKUP_FILE"
```

Add to cron:
```bash
chmod +x /opt/proyex/backup-db.sh
crontab -e
```

Add:
```
0 2 * * * /opt/proyex/backup-db.sh
```

## Monitoring & Logs

View deployment logs on GitHub:
```
GitHub repo → Actions → "Build and Deploy to Production" → latest run
```

View application logs on droplet:
```bash
docker compose logs -f app
docker compose logs -f db
```

Check service status:
```bash
systemctl status proyex
docker compose ps
```

## Troubleshooting

**Deployment failed in GitHub Actions:**
- Check Actions tab for error logs
- Common issues: wrong secrets, SSH key permissions

**Containers won't start on droplet:**
```bash
docker compose ps
docker compose logs app
ssh -i ~/.ssh/deploy_key root@YOUR_DROPLET_IP
```

**Test database connection:**
```bash
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

**Manual restart:**
```bash
docker compose restart
# or full redeploy
docker compose down && docker compose up -d
```

## Rolling Back

If deployment breaks, SSH to droplet and checkout previous code:
```bash
cd /opt/proyex
git log --oneline | head -10
git checkout <previous-commit-hash>
docker compose pull
docker compose up -d
```

Or wait for next deployment push from GitHub.
DB_ROOT_PASSWORD=your_secure_root_password
EOF
chmod 600 ~/app/.env
```

Test that images can be pulled (they will fail to run until pushed from GitHub):
```bash
docker compose pull || echo "Images not yet in GHCR — this is normal before first release"
```

### 2. Generate SSH Deploy Key

On your **local machine**:
```bash
ssh-keygen -t ed25519 -f ~/.ssh/proyex_deploy -C "GitHub Actions Deploy" -N ""
```

Add the public key to your droplet's authorized_keys:
```bash
ssh-copy-id -i ~/.ssh/proyex_deploy.pub your_user@your.droplet.ip
```

Verify:
```bash
ssh -i ~/.ssh/proyex_deploy your_user@your.droplet.ip "echo 'SSH key works'"
```

### 3. Add GitHub Secrets

In your GitHub repo, go to **Settings > Secrets and variables > Actions > New repository secret** and add:

| Secret | Value | Example |
|--------|-------|---------|
| DROPLET_HOST | Droplet IP address | `192.0.2.1` |
| DROPLET_USER | SSH username on droplet | `root` or `deploy` |
| DROPLET_SSH_KEY | Contents of `~/.ssh/proyex_deploy` (private key) | `-----BEGIN PRIVATE KEY-----...` |
| DROPLET_SSH_PORT | (optional, defaults to 22) | `22` |

**Important:** The private key file is sensitive — keep it safe and never commit it.

### 4. Test Deployment

1. Create a GitHub Release:
   - Go to your repo
   - Click **Releases** → **Draft a new release**
   - Tag: `v1.0.0`
   - Title: `Release 1.0.0`
   - Click **Publish release**

2. Watch the workflow:
   - Go to **Actions** tab
   - Click the "Deploy on release" workflow run
   - Check **build-and-push** and **deploy** job logs

3. On success, SSH into droplet and verify:
   ```bash
   docker compose ps
   docker image ls | grep ghcr
   ```

## How It Works

1. **Trigger:** You publish a GitHub release (v1.0.0, etc.)
2. **Build:** GitHub Actions builds `app` and `web` images with Dockerfile targets
3. **Push:** Images are tagged and pushed to GHCR (ghcr.io/nachopitt/proyex-app:latest, etc.)
4. **Deploy:** GitHub Actions SSHs the droplet and runs:
   - `docker compose pull` — fetches latest images from GHCR
   - `docker compose up -d --remove-orphans` — starts/restarts containers
   - `docker image prune -f` — cleans up old images

## Troubleshooting

**"SSH key permission denied"**
- Verify key was added: `ssh-keygen -l -f ~/.ssh/proyex_deploy.pub`
- Check droplet's `~/.ssh/authorized_keys` contains the public key
- Verify SSH port is correct (default 22)

**"Docker compose pull failed"**
- Ensure images were pushed to GHCR (check Actions → build-and-push logs)
- Verify GHCR images are public (or add auth to droplet)
- SSH into droplet and test: `docker pull ghcr.io/nachopitt/proyex-app:latest`

**"Permission denied while trying to connect"**
- Verify DROPLET_SSH_KEY secret is the **private** key, not public
- Check DROPLET_USER and DROPLET_HOST are correct
- Test manually: `ssh -i ~/.ssh/proyex_deploy user@host`

## Manual Deployment (if needed)

SSH into droplet:
```bash
cd ~/app
# Pull latest images
docker compose pull

# Restart containers
docker compose up -d --remove-orphans

# View logs
docker compose logs -f
```

## Database Migrations

If your release includes database migrations, add a step to the deploy workflow or run manually after deployment:
```bash
# On droplet
cd ~/app
docker compose exec -T app php artisan migrate --force
```

## Rollback

If deployment fails, restore the previous version:
```bash
# On droplet
cd ~/app
docker compose down
git pull origin <previous-tag-or-branch>
docker compose up -d
```

Or pull a specific image tag:
```bash
docker compose down
# Edit docker-compose.yml to use a specific tag (e.g., release-v1.0.0)
docker compose up -d
```
