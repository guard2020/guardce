
$modules = (@('./compose.yml ' + @(Get-ChildItem */compose.yml)) -replace ' ',' -f ')
$cmd = "docker compose -f $modules $args"
Invoke-Expression $cmd
