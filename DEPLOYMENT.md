# Deployment Guide

## Overview
Proyex deploys automatically on GitHub release. The workflow builds Docker images, pushes them to GHCR, then SSHs your DigitalOcean droplet to pull and restart containers.

## Prerequisites
- GitHub repository: `github.com/nachopitt/proyex`
- DigitalOcean droplet ($6/mo, North America)
- SSH access to droplet

## Setup

### 1. Prepare DigitalOcean Droplet

SSH into your droplet and install Docker:
```bash
sudo apt update && sudo apt install -y docker.io docker-compose-plugin
sudo systemctl enable --now docker
sudo usermod -aG docker $USER
# log out and back in for group to take effect
exit
```

Create app deployment directory:
```bash
mkdir -p ~/app
cd ~/app
```

Copy production compose files into the droplet app directory and use the production override:

```bash
# on droplet (example)
cp /path/to/repo/docker-compose.yml /home/<your-user>/app/
cp /path/to/repo/docker-compose.prod.yml /home/<your-user>/app/

cd /home/<your-user>/app
# optional: set IMAGE_TAG in .env to a release tag (e.g. IMAGE_TAG=release-v1.0.0)
docker compose -f docker-compose.yml -f docker-compose.prod.yml pull
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

This ensures the droplet pulls published GHCR images rather than attempting local builds. You can set IMAGE_TAG in ~/app/.env to specify a release tag (e.g., IMAGE_TAG=release-v1.0.0); otherwise :latest is used.

Create `.env` file with production credentials (never commit this):
```bash
cat > ~/app/.env <<EOF
DB_DATABASE=proyex_prod
DB_USERNAME=proyex_user
DB_PASSWORD=your_secure_password_here
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
