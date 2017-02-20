#!/usr/bin/env bash

if [ "$1" != "" ]; then
    IDA_RSA_FILE_PATH="$1"
else
    IDA_RSA_FILE_PATH="${HOME}/.ssh/id_rsa"
fi

if  [ ! -e $IDA_RSA_FILE_PATH ];
    then echo "key file "$IDA_RSA_FILE_PATH" did not exist, please specify correct file path."; exit;
fi


echo "using $IDA_RSA_FILE_PATH as key file"

containerName="api-app"
hostUserId=$(id -u)
dockerUser=www-data

uid=$(id -u)
if [ $uid -gt 100000 ]; then
	uid=1000
fi

sed "s/\$USER_ID/$uid/g" ./docker/Dockerfile.dist > ./docker/Dockerfile


#stop potentionally running app
docker-compose stop

##build and launch containers
docker-compose build
docker-compose up -d

##log into the container
docker exec -it --user www-data api-app bash
docker-compose stop