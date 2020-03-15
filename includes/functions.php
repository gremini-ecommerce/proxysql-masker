<?php

function printHelp()
{
    echo "USAGE: php create-rules.php -u user -p password(optional) -h host(optional) -P port(optional) -s socket(optional) -f file -U ProxySqlUser\n";
    echo "-u: MySql user used to scan the database for tables needed masking.\n";
    echo "-p: MySql password. Defaults to empty password.\n";
    echo "-h: MySql host. Defaults to 127.0.0.1.\n";
    echo "-P: MySql port. Defaults to 3306.\n";
    echo "-s: MySql unix socket.\n";
    echo "-f file: should be a text file with fields that need masking, one per row.\n";
    echo "-U user: the Proxy SQL user that will be inserted into mysql_query_rules.\n";
}
