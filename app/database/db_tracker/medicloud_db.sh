#!/bin/sh

echo "ENTER DATA BASE NAME:"
read dbname
echo "ENTER DATABASE USER NAME:"
read dbuser
echo "ENTER DATASE PASSWORD:"
read dbpassword

#dbname="medi_test"
#dbuser="rizvi"
#dbpassword="rizvi"

#Use to export dB
#mysqldump -u $dbuser -p$dbpassword $dbname>$dbname".sql"

#Use Import Db
mysql -u $dbuser -p$dbpassword $dbname<medicloud-09-09-2015.sql
