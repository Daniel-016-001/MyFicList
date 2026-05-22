# Script to zip the MyFicList project cleanly for local deployment

$src = "C:\Users\Daniel\Desktop\MyFicList"
$destZip = "C:\Users\Daniel\Desktop\MyFicList.zip"
$tempDir = Join-Path $src "scratch\temp_zip_deployment"

if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
}

# Create temp dir structure
New-Item -ItemType Directory -Path $tempDir | Out-Null

Write-Host "Copying files to temporary deployment folder..."

# Define folders/files to exclude
$excludePatterns = @(
    "\.git$",
    "node_modules",
    "vendor",
    "scratch",
    "storage\\framework\\cache",
    "storage\\framework\\sessions",
    "storage\\framework\\views",
    "storage\\logs",
    "database\\database\.sqlite$"
)

# Get all files recursively
$files = Get-ChildItem -Path $src -Recurse -File

foreach ($file in $files) {
    $relative = $file.FullName.Substring($src.Length + 1)
    
    # Check if relative path matches any exclusion
    $exclude = $false
    foreach ($pattern in $excludePatterns) {
        if ($relative -match $pattern) {
            $exclude = $true
            break
        }
    }
    
    if (-not $exclude) {
        $targetFile = Join-Path $tempDir $relative
        $targetDir = Split-Path $targetFile -Parent
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir | Out-Null
        }
        Copy-Item -Path $file.FullName -Destination $targetFile -Force
    }
}

# Ensure an empty SQLite file structure is created if needed, or keep it to be created by the user according to the README
# We also make sure the storage directories exist in the zipped folder
$subDirsToEnsure = @(
    "storage\app\public",
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\views",
    "storage\logs"
)
foreach ($dir in $subDirsToEnsure) {
    $fullDir = Join-Path $tempDir $dir
    if (-not (Test-Path $fullDir)) {
        New-Item -ItemType Directory -Path $fullDir | Out-Null
    }
}

Write-Host "Compressing to ZIP file: $destZip"
if (Test-Path $destZip) {
    Remove-Item -Force $destZip
}

# Compress
Compress-Archive -Path "$tempDir\*" -DestinationPath $destZip -Force

# Clean up temp folder
Remove-Item -Recurse -Force $tempDir

Write-Host "ZIP compression complete!"
