#!/usr/bin/env bash


function getPid
{
    ps ax | grep vendor/bin/selenium.jar | grep -v grep | awk {'print $1'}
}

function getPgid
{
    ps x -o pgid,cmd | grep vendor/bin/selenium.jar | grep -v grep | awk {'print $1'}
}


case $1 in
  status*)
    getPid
    ;;
  start*)
    nohup java -Dwebdriver.chrome.driver="vendor/bin/chromedriver" -jar vendor/bin/selenium.jar > /tmp/selenium.out 2>&1&
    if [ "`getPid`" == "" ]; then
        echo "Failed stop start selenium"
    else
      echo "Selenium is running"
    fi 
    ;;
  stop*)
   	if [ "`getPgid`" != "" ]; then
      kill -9 -`getPgid`
      echo "Selenium instance was killed"
    else
      echo "No selenium instance found"
    fi
    ;;
  listen*)
  	tail -f -n 100 /tmp/selenium.out
  	;;
  *)
    echo 'Wait, what?';
    ;;
esac