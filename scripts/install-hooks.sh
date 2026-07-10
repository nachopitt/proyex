#!/usr/bin/env bash
# scripts/install-hooks.sh
# Installs git hooks to run automated checks before committing or pushing.

set -e

HOOK_DIR=".git/hooks"
PRE_PUSH_HOOK="$HOOK_DIR/pre-push"
PRE_COMMIT_HOOK="$HOOK_DIR/pre-commit"

echo "Installing Git Hooks..."

# Ensure hook directory exists
mkdir -p "$HOOK_DIR"

# 1. Pre-push hook: run the full test suite
echo "Creating $PRE_PUSH_HOOK..."
cat << 'EOF' > "$PRE_PUSH_HOOK"
#!/usr/bin/env bash
# Pre-push hook: Runs tests before code is pushed to remote.

echo "Running pre-push test checks..."
if ! docker compose exec -T workspace php artisan test; then
    echo "ERROR: Tests failed! Aborting push."
    exit 1
fi
EOF

# 2. Pre-commit hook: run the test coverage checker
echo "Creating $PRE_COMMIT_HOOK..."
cat << 'EOF' > "$PRE_COMMIT_HOOK"
#!/usr/bin/env bash
# Pre-commit hook: Verifies that code additions have corresponding tests.

echo "Running pre-commit test coverage alignment check..."
if ! ./scripts/check-test-coverage.sh --staged; then
    echo "ERROR: Test coverage check failed! Aborting commit."
    exit 1
fi
EOF

# Make hooks executable
chmod +x "$PRE_PUSH_HOOK"
chmod +x "$PRE_COMMIT_HOOK"

echo "Git hooks installed successfully!"
