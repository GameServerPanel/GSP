# Update all metadata.json files to add "complete" field
# Minecraft is marked as complete, all others are marked as incomplete (TODO)

$docsPath = "c:\Users\FrankHarris\Desktop\Projects\Xampp\htdocs\GSP\modules\billing\docs"

# Games that are complete
$completeGames = @('minecraft')

# Get all subdirectories
$folders = Get-ChildItem -Path $docsPath -Directory

$updated = 0
$skipped = 0

foreach ($folder in $folders) {
    $metadataPath = Join-Path $folder.FullName "metadata.json"
    
    if (Test-Path $metadataPath) {
        # Read the metadata file
        $json = Get-Content $metadataPath -Raw | ConvertFrom-Json
        
        # Check if "complete" field already exists
        if ($null -eq $json.complete) {
            # Determine if this game is complete
            $isComplete = $completeGames -contains $folder.Name
            
            # Add the complete field
            $json | Add-Member -MemberType NoteProperty -Name "complete" -Value $isComplete
            
            # Write back to file with proper formatting
            $json | ConvertTo-Json -Depth 10 | Set-Content $metadataPath -Encoding UTF8
            
            Write-Host "Updated: $($folder.Name) - complete: $isComplete" -ForegroundColor Green
            $updated++
        } else {
            Write-Host "Skipped: $($folder.Name) - already has complete field" -ForegroundColor Yellow
            $skipped++
        }
    } else {
        Write-Host "No metadata.json in: $($folder.Name)" -ForegroundColor Red
    }
}

Write-Host "`nSummary:" -ForegroundColor Cyan
Write-Host "  Updated: $updated" -ForegroundColor Green
Write-Host "  Skipped: $skipped" -ForegroundColor Yellow
