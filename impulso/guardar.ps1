# Sube al repositorio todo lo generado en esta PC (base de datos + public/uploads).
param([string]$Mensaje = "Guardar datos e imagenes $(Get-Date -Format 'yyyy-MM-dd HH:mm')")
$root = (git -C $PSScriptRoot rev-parse --show-toplevel)
git -C $root pull --rebase --autostash origin main
git -C $root add -A
git -C $root commit -m $Mensaje
git -C $root push origin main
