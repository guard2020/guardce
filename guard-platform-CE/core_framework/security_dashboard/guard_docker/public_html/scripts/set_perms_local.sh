#!/bin/bash

###################################
owner="$(stat -c %U ${PWD})"
group="$(stat -c %G ${PWD})"

echo "#################"
echo "Setting Permissions"
echo "#################"



# set basedir of installation, no trailing slash
baseDir=$PWD
publicDir=$PWD/public



echo "current directory: "
echo -e " \t\t\t${baseDir}"
echo "User: "
echo -e " \t\t\t${owner}"
echo "Group: "
echo -e " \t\t\t${group}"



# fix ownership that might have been overwritten by upgrade
echo "##############"
echo "setting owner and group ..."
echo "##############"


#ideal for local server
chown -R $owner:$group ${baseDir}
chown -R root:root ${baseDir}/vendor


#changing storage folder mod
chmod -R ug+rwx storage

echo
echo "[DONE]"
echo
