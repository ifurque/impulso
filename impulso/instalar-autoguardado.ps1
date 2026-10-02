# Ejecutar una vez en cada PC: registra la tarea que vigila y sube datos/imagenes al iniciar sesion.
$nombre = 'Impulso-AutoGuardado'
$script = Join-Path $PSScriptRoot 'vigilar.ps1'
$accion = New-ScheduledTaskAction -Execute 'powershell.exe' -Argument "-NoProfile -WindowStyle Hidden -ExecutionPolicy Bypass -File `"$script`"" -WorkingDirectory $PSScriptRoot
$disparador = New-ScheduledTaskTrigger -AtLogOn -User $env:USERNAME
$config = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -ExecutionTimeLimit ([TimeSpan]::Zero) -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1)
Register-ScheduledTask -TaskName $nombre -Action $accion -Trigger $disparador -Settings $config -Force | Out-Null
Start-ScheduledTask -TaskName $nombre
Write-Host "Tarea '$nombre' instalada y en ejecucion."
