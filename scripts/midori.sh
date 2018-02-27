#!/bin/bash

while true; do

    var1=$( free -m |awk 'NR == 3 {print $3}' )
    var2=$( ps aux | grep 'midori -e Fullscreen' | grep -v grep | awk '{print $2}' )

    echo "swap used: $var1"
    #echo "midori pid: $var2"

    if [ $var1 -gt 1024 ]; then
         echo "Midori has exceeded the allowed memory... killing pid $var2"
         kill -9 $var2
         break
    fi

    sleep 10

done

