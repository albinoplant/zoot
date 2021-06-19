#!/bin/bash

declare -a projects=('zoot')

# add domain to hosts file
for project in "${projects[@]}";
do
    entry="127.0.0.1 ${project}.local"
    grep -qx "$entry" /etc/hosts || echo "$entry" >> /etc/hosts
done

# set alias to container bash
touch ~/.zshrc
alias='alias docker-server="docker exec -it server bash"'
grep -qx "$alias" ~/.zshrc || echo "$alias" >> ~/.zshrc

# run containers
docker-compose up -d