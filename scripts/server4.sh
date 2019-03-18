#!/bin/bash

while true; do

    result=$( ps -ef | grep 'server4.js' | grep -v grep )

    if [[ "$result" != "" ]];then
        echo "Running"
    else
        echo "Not Running? Restarting...."
        systemctl restart nodeserver
    fi

    sleep 10

done
