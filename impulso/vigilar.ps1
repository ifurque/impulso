# Vigila la base de datos y public/uploads; ante un cambio espera unos segundos y ejecuta guardar.ps1.
param([int]$EsperaSegundos = 20)
$app = $PSScriptRoot

function Get-Firma {
    $archivos = @(Get-Item "$app\database\database.sqlite" -ErrorAction SilentlyContinue)
    $archivos += Get-ChildItem "$app\public\uploads" -Recurse -File -ErrorAction SilentlyContinue
    ($archivos | ForEach-Object { "$($_.FullName)|$($_.Length)|$($_.LastWriteTimeUtc.Ticks)" }) -join "`n"
}

$guardada = Get-Firma
$cambio = $null
while ($true) {
    Start-Sleep -Seconds 5
    $actual = Get-Firma
    if ($actual -ne $guardada) {
        $guardada = $actual
        $cambio = Get-Date
    }
    if ($cambio -and ((Get-Date) - $cambio).TotalSeconds -ge $EsperaSegundos) {
        $cambio = $null
        & "$app\guardar.ps1" "Auto: guardar datos e imagenes $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
        $guardada = Get-Firma
    }
}
