Param(
    [Parameter(Mandatory=$true)]
    [String]
    $remoteHostname
)

cd ..
7z a aaa.tar aaa/ -xr!'.git' -xr!'testbed'

$cmd = "scp aaa.tar ${remoteHostname}:~/"
Invoke-Expression $cmd

cd aaa/
rm ../aaa.tar
