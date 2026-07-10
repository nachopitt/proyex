#!/usr/bin/env bash
# scripts/check-test-coverage.sh
# Verifies that application changes are accompanied by matching test file changes.

set -eo pipefail

MODE="$1"

if [ "$MODE" = "--staged" ]; then
    echo "Checking staged changes..."
    CHANGED_FILES=$(git diff --cached --name-only)
elif [ -n "$MODE" ]; then
    echo "Checking changes against: $MODE"
    CHANGED_FILES=$(git diff --name-only "$MODE"...HEAD)
else
    echo "Checking all local changes (staged + unstaged)..."
    CHANGED_FILES=$(git diff HEAD --name-only)
fi

if [ -z "$CHANGED_FILES" ]; then
    echo "No changes detected."
    exit 0
fi

APP_FILES=()
TEST_FILES=()

# Read files line by line
while IFS= read -r file; do
    if [[ -z "$file" ]]; then continue; fi

    # Check if file belongs to application code (app/ PHP files, or frontend components)
    if [[ "$file" =~ ^app/.*\.php$ ]] || [[ "$file" =~ ^resources/js/.*\.([tj]sx?|vue)$ ]]; then
        # Ignore configuration, migrations, and standard baseline routes
        if [[ ! "$file" =~ ^app/Providers/ ]] && [[ ! "$file" =~ ^app/Console/Kernel.php ]]; then
            APP_FILES+=("$file")
        fi
    # Check if file is a test file
    elif [[ "$file" =~ ^tests/.*\.php$ ]]; then
        TEST_FILES+=("$file")
    fi
done <<< "$CHANGED_FILES"

if [ "${#APP_FILES[@]}" -eq 0 ]; then
    echo "No application changes detected that require tests."
    exit 0
fi

echo "Modified/Added application files (${#APP_FILES[@]}):"
for f in "${APP_FILES[@]}"; do
    echo "  - $f"
done

echo "Modified/Added test files (${#TEST_FILES[@]}):"
for t in "${TEST_FILES[@]}"; do
    echo "  - $t"
done

if [ "${#TEST_FILES[@]}" -eq 0 ]; then
    # Allow local override using environment variable
    if [ "$SKIP_TEST_CHECK" = "1" ] || [ "$SKIP_TEST_CHECK" = "true" ]; then
        echo "WARNING: App files changed without matching test files, but SKIP_TEST_CHECK is active. Proceeding."
        exit 0
    fi

    echo "======================================================================"
    echo "ERROR: Application changes detected, but no test changes were found!"
    echo "Please add corresponding test coverage under the tests/ directory."
    echo "----------------------------------------------------------------------"
    echo "To temporarily bypass this check, run:"
    echo "  SKIP_TEST_CHECK=1 git commit"
    echo "======================================================================"
    exit 1
fi

echo "Success: Test coverage alignment check passed!"
exit 0
