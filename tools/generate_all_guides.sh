#!/bin/bash
# Comprehensive Server Admin Guide Generation Workflow
# Automates the complete guide generation and validation process

set -e

echo "=== Comprehensive Server Admin Guide Generation ==="
echo "Starting workflow at $(date)"
echo

# Step 1: Generate all guides
echo "1. Generating Markdown guides and PDFs..."
python3 tools/generate_server_guides.py
echo

# Step 2: Validate output
echo "2. Validating generated guides..."
python3 tools/validate_guides.py
echo

# Step 3: Statistics
echo "3. Generation Statistics:"
echo "   - Markdown guides: $(find docs/games -name 'index.md' | wc -l)"
echo "   - PDF files: $(find dist/pdfs -name '*.pdf' | wc -l)"
echo "   - Total file size: $(du -sh dist/pdfs/ | cut -f1)"
echo

# Step 4: Quality check
echo "4. Quality Check:"
WARNINGS=$(python3 tools/validate_guides.py 2>&1 | grep -c "⚠️" || true)
ERRORS=$(python3 tools/validate_guides.py 2>&1 | grep -c "❌" || true)

echo "   - Validation warnings: $WARNINGS"
echo "   - Validation errors: $ERRORS"
echo

if [ "$ERRORS" -eq 0 ]; then
    echo "✅ All guides generated successfully!"
    echo "📁 Guides available at: docs/games/"
    echo "📄 PDFs available at: dist/pdfs/"
    echo "📋 Index page: docs/games/_index.md"
    echo "📊 Manifest: dist/pdfs/manifest.json"
else
    echo "❌ Guide generation completed with errors. Please review validation output."
    exit 1
fi

echo
echo "=== Workflow completed at $(date) ==="