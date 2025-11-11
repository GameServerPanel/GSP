# Normalize metadata files under modules/billing/docs
# - Removes the `complete` field from all metadata.json
# - If complete == false, set category to "todo"
# - Writes pretty JSON without BOM

# Determine repository root from script location and set docs path
$scriptDir = (Get-Item $PSScriptRoot).FullName
$repoRoot = (Get-Item $scriptDir).Parent.FullName
$docsRoot = Join-Path $repoRoot "modules\billing\docs"
if (-not (Test-Path $docsRoot)) {
    # If that fails, try current directory fallback
    $docsRoot = Join-Path (Get-Location) "modules\billing\docs"
}

Write-Host "Scanning metadata under: $docsRoot"

Get-ChildItem -Path $docsRoot -Recurse -Filter metadata.json | ForEach-Object {
    $file = $_.FullName
    Write-Host "Processing: $file"
    $raw = Get-Content $file -Raw
    # Remove BOM if present
    $raw = $raw -replace "^\uFEFF", ""

    try {
        $obj = $raw | ConvertFrom-Json -ErrorAction Stop
    } catch {
        Write-Warning "Failed to parse JSON: $file - skipping"
        return
    }

    # Build a hash without the 'complete' key
    $h = @{}
    foreach ($p in $obj.PSObject.Properties) {
        if ($p.Name -ieq 'complete') { continue }
        $h[$p.Name] = $p.Value
    }

    # If original had complete == $false, ensure category = 'todo'
    if ($obj.PSObject.Properties.Name -contains 'complete') {
        if ($obj.complete -eq $false) {
            # only set if not already a real category
            $h['category'] = 'todo'
        }
    }

    # Ensure category exists
    if (-not ($h.ContainsKey('category'))) {
        # default to 'todo' for items without category
        $h['category'] = 'todo'
    }

    # Convert to JSON with consistent ordering (name, description, category, order)
    $ordered = @{}
    if ($h.ContainsKey('name')) { $ordered['name'] = $h['name'] }
    if ($h.ContainsKey('description')) { $ordered['description'] = $h['description'] }
    if ($h.ContainsKey('category')) { $ordered['category'] = $h['category'] }
    if ($h.ContainsKey('order')) { $ordered['order'] = $h['order'] }
    foreach ($k in $h.Keys) {
        if ($k -in @('name','description','category','order')) { continue }
        $ordered[$k] = $h[$k]
    }

    $json = $ordered | ConvertTo-Json -Depth 10
    # Make it pretty: ConvertTo-Json outputs compact arrays sometimes; ensure 4-space indent
    $formatted = $json -replace '\\r?\\n', "`n" # normalize newlines

    # Write without BOM
    [System.IO.File]::WriteAllText($file, $formatted, [System.Text.Encoding]::UTF8)
    Write-Host "Updated: $file"
}
Write-Host "Done."