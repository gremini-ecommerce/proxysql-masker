# proxysql-masker

We use ProxySQL to anonymize data in our developer dumps (mysqldump connects to ProxySQL).

We needed a tool to create and maintain the ProxySQL rules up to date, so we wrote this.

# Usage
php create-rules.php -u user -p password(optional) -d database (optional) -h host(optional) -P port(optional) -s socket(optional) -f file -U ProxySqlUser  
-u: MySql user used to scan the database for tables needed masking.  
-p: MySql password. Defaults to empty password.  
-d: Database name. If no database is given, the script will run for every database found on the system.  
-h: MySql host. Defaults to 127.0.0.1.  
-P: MySql port. Defaults to 3306.  
-s: MySql unix socket.  
-f file: should be a text file with fields that need masking, one per row.  
-U user: the Proxy SQL user that will be inserted into mysql_query_rules.  

Eg: php create-rules.php -u root -h localhost -U dumper -f /tmp/fields.txt > /tmp/proxy-tmp.sql

The script will output the rules for proxysql. In this example, the output is redirected to /tmp/proxy-tmp.sql. **You need to MANUALLY load this file into proxysql**.


# Custom masks

The standard masking is done like this:
CONCAT(LEFT(`fieldName`,2), REPEAT('x',LENGTH(`fieldName`)-4), RIGHT(`fieldName`, 2))

So GreminiEcommerce will be masked as Grxxxxxxxxxxxxce.

However, there are situation where a field has a unique key and this approch will fail. Or maybe a field needs to have a certain format (eg: email). If this is the case, you can use a custom mask. In the fields text file, after the field name use colon sign (:) followed by the mask. Here is an exemple for masking an email field:

customer_email:CONCAT(md5(`customer_email`), '@gremini.com')

So, user@domain.com will be masked as cd2bfcffe5fee4a1149d101994d0987f@gremini.com
