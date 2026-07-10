#!/usr/bin/env bash
# scripts/check-agent-readiness.sh
# Verifies the project's health and ensures the agent is ready to proceed.

set -eo pipefail

echo "========================================="
echo "    AI Agent Project Readiness Auditor    "
echo "========================================="
echo ""

# 1. Check for required documents
echo "Checking critical documentation files..."
REQUIRED_DOCS=(
    "README.md"
    "DATABASE_WORKFLOW.md"
    "DEPLOYMENT.md"
    ".agents/AGENTS.md"
    ".github/copilot-instructions.md"
)

ALL_DOCS_FOUND=true
for doc in "${REQUIRED_DOCS[@]}"; do
    if [ -f "$doc" ]; then
        echo "  [OK] $doc is present"
    else
        echo "  [ERROR] Missing $doc"
        ALL_DOCS_FOUND=false
    fi
done

if [ "$ALL_DOCS_FOUND" = false ]; then
    echo "CRITICAL: Some documentation files are missing. Please restore them."
    exit 1
fi

# 1.5 List all available project documentation
echo ""
echo "Discovered Project Documentation Files:"
find . -maxdepth 2 -name "*.md" -not -path "*/node_modules/*" -not -path "*/vendor/*" -not -path "*/.git/*" | sort | while read -r doc_file; do
    # Remove leading ./ for cleaner output
    clean_path="${doc_file#./}"
    echo "  - $clean_path"
done

# 2. Check if .agents/AGENTS.md and .github/copilot-instructions.md are in sync
echo ""
echo "Checking agent instructions synchronization..."
if cmp -s ".agents/AGENTS.md" ".github/copilot-instructions.md"; then
    echo "  [OK] Agent rules and Copilot instructions are synchronized."
else
    echo "  [WARNING] .agents/AGENTS.md and .github/copilot-instructions.md differ."
    echo "            Please sync changes between these two files."
fi

# 2.5 Check environment variable synchronization
echo ""
if ! ./scripts/check-env-sync.sh; then
    echo "  [ERROR] Environment variables are out of sync. Please see errors above."
    exit 1
fi

# 3. Check container status
echo ""
echo "Checking Docker containers..."
if ! docker compose ps --format json | grep -q "proyex-workspace-dev"; then
    echo "  [ERROR] Docker containers are not running. Please start them using 'docker compose up -d'."
    exit 1
fi
echo "  [OK] Docker containers are running."

# 4. Run tests
echo ""
echo "Running PHPUnit tests inside workspace..."
if docker compose exec -T workspace php artisan test; then
    echo "  [OK] All tests passed successfully."
else
    echo "  [ERROR] Tests are failing. Please fix them before starting new work."
    exit 1
fi

echo ""
echo "========================================="
echo "  Agent status: READY TO PROCEED         "
echo "========================================="
exit 0
