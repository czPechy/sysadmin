#!/bin/bash
read -p "Printer name: " name
read -p "Description: " description
read -p "Destination IP: " ip

echo "Summary: "
echo ""
echo "Printer: $name"
echo "Description: Účtenková tiskárna $description"
echo "Destination: socket://$ip:9100"
echo ""

read -p "Correct? [y/n]" -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]
then
    	/usr/sbin/lpadmin -p "$name" -D "Účtenková tiskárna $description" -E -v "socket://$ip:9100" -m star/tsp143.ppd -o printer-is-shared=true -o Media=om_x-72-mmy-2000-mm_71.97x1999.54mm
fi
