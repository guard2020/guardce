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
storageLink=${baseDir}/public/storage
publicDir=$PWD/public
scriptDir=$PWD/scripts


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
if [[ ! $proceed == "yes" ]]; then
    echo "Exiting ... Please try again"
    exit
fi

echo "####################"
echo "installing packages ..."
echo "####################"
composer install


#.htaccess file check
htaccessFile=${baseDir}/.htaccess
if [[ ! -f  "$htaccessFile" ]];then
    #.htaccess file not existing, create it?
    read -e -p "Create .htaccess file? " -i "yes" reply
    if [[  $reply == "yes" ]];
    then
        echo "##############"
        echo "creating .htaccess file ..."
        echo "##############"
        cp ${scriptDir}/root_access.txt $htaccessFile

    fi
fi

#.htaccess in public directory
publicHtaccess=${publicDir}/.htaccess
if [[ ! -f  "$publicHtaccess" ]];then
    #.htaccess file not existing, cerate it?
    read -e -p "Create public/.htaccess file? " -i "yes" reply
    if [[  $reply == "yes" ]];
    then
        echo "##############"
        echo "creating public/.htaccess file ..."
        echo "##############"
        cp ${scriptDir}/public_access.txt $publicHtaccess
    fi
fi

#npm install check
npmDir=${baseDir}/node_modules
if [[ ! -d  "$npmDir" ]];then
    #.htaccess file not existing, cerate it?
    read -e -p "Run npm install? " -i "yes" reply
    if [[  $reply == "yes" ]];
    then
        echo "##############"
        echo "installing npm ..."
        echo "##############"
        npm install --unsafe-perm node-sass
        npm install --save-dev gulp
        npm install gulp-postcss autoprefixer gulp-jshint jshint gulp-sass gulp-clean-css gulp-concat gulp-uglify gulp-rename gulp-rtlcss --save-dev
        npm run dev
    fi
fi


#storage link
if [[ ( -L "${storageLink}" ) && ( -d "${storageLink}" ) ]];
then
        echo "storage link exists..."
        echo "unlinking it..."
        unlink ${baseDir}/public/storage
fi



echo "##############"
echo "creating the storage link ..."
echo "##############"

#removing storage link if already existing
if [ -d "$storageLink" ]; then
        /bin/rm ${storageLink}
fi


php artisan storage:link
# setting ownership of storage link
chown -h ${owner}:${group} ${storageLink}


set -e



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
echo "########################"
echo "!!! don't forget to:"
echo -e "\t\t - create .env file"
echo -e "\t\t - update DB configuration"
echo -e "\t\t - then run: php artisan key:generate"
echo
echo "########################"