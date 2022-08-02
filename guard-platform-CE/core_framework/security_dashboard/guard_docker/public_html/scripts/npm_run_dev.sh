#!/bin/bash


echo "#################"
echo "running npm run dev ..."
echo "#################"

npm run dev
scriptDir=$PWD/scripts

source ${scriptDir}/set_perms_local.sh
