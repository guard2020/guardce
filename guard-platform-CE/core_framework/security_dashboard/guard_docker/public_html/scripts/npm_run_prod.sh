#!/bin/bash

###################################
owner="$(stat -c %U ${PWD})"
group="$(stat -c %G ${PWD})"

echo "#################"
echo "Setting Permissions"
echo "#################"
read -e -p  "Please enter access user (system user): " -i $owner owner
read -e -p  "Please enter access group (system group): " -i $group group


# set basedir of installation, no trailing slash
baseDir=$PWD
publicDir=$PWD/public



echo "#####################"
echo "Please confirm variables below"
echo "#####################"
echo "current directory: "
echo -e " \t\t\t${baseDir}"
echo "User: "
echo -e " \t\t\t${owner}"
echo "Group: "
echo -e " \t\t\t${group}"

read -e -p "Shall we proceed? " -i "yes" proceed
if [ ! $proceed == "yes" ]; then
    echo "Exiting ... Please try again"
    exit
fi

# fix ownership that might have been overwritten by upgrade
echo "##############"
echo "setting owner and group ..."
echo "##############"

#ideal for production server
chown -R root:root ${baseDir}/
chown -R $owner:$group ${publicDir}/
chown -R $owner:$group ${PWD}/storage

#changing storage folder mod
chmod -R ug+rwx storage

echo
echo "[DONE]"
echo
