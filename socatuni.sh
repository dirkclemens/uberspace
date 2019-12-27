#!/bin/sh

# These environment variables are sometimes needed by the running daemons
export USER=mbx
export HOME=/home/mbx

# Include the user-specific profile
source $HOME/.bash_profile

#######################################################
#
# hier die ipv6 aus der linux cli > "ip a" nehmen
# ssh mbx@arend.uberspace.de -p 47820 - 4782x
#
#######################################################

if [ $# -eq 0 ]
then
	echo "two parameters needed\n"
	echo "socat mbxport brixport\n"
       	echo "e.g. socat 47820 3000\n"
	exit
fi

mbxport=$1
brixport=$2 

if [ -z "$mbxport" ]
then
   	echo "no port on this maschine defined"
       	exit
fi

if [ -z "$brixport" ]
then
       	echo "no port on brix defined"
       	exit
fi

ip6="$(</home/mbx/html/ip6.txt)"

if [ -z "$ip6" ]
then
        echo "no ipV6 defined"
        exit
fi

#echo "ipv6: $ip6"
echo "executing socat TCP4-LISTEN:$mbxport,fork TCP6:[$ip6]:$brixport"
exec /home/mbx/bin/socat TCP4-LISTEN:$mbxport,fork TCP6:[$ip6]:$brixport 2>&1

