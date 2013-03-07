#!/bin/sh
# this script is used to create new email aliases depending on groups in tiki
# it's very experimental, please do not use if you can't understand what it does exactly
# by reading the source code

# it uses postfix, with a special alias file declared in main.cf
# virtual_maps = hash:/etc/postfix/tikiwiki
# and it runs by crontab

# cusytomize those values for your context
DBHOST="localhost"
DBNAME="tikiwiki"
DBUSER="root"
DBPASS=""
GROUP="Developers"
WORKDIR="/tmp"
DOMAIN="tikiwiki.org"
# that file contains the result of the db extraction
CONFFILE="/etc/postfix/tikiwiki"
# that file contains some emails that are not extracted from the db
BASEFILE="/etc/postfix/tikiwiki_base"
# end of customize

MYSQL="/usr/bin/mysql"
MDOPTIONS="-h$DBHOST -u$DBUSER -p$DBPASS -s"
# that request exports all email accounts from $GROUP
REQUEST="select login,email from users_users left join users_usergroups on users_users.userId=users_usergroups.userId where groupname='$GROUP' and email !='' order by login"
# that request exports all email accounts if email is public
REQUESTOPT="select uu.login,uu.email from users_users uu left join users_usergroups ug on uu.userId=ug.userId left join tiki_user_preferences tup on tup.user=uu.login where
ug.groupname='$GROUP' and uu.email!='' and tup.prefName='email is public' and tup.value!='n' and tup.value!='no' order by login"
OLDIR=`pwd`

cd $WORKDIR
rm -f usermails 2&> /dev/null

# here you can replace $REQUESTOPT by $REQUEST at will
# think to custom the domain name
$MYSQL $MDOPTIONS -e "$REQUESTOPT" $DBNAME | awk '{print $1"@\$DOMAIN "$2}' > usermails

rm -f $CONFFILE 
cat $BASEFILE usermails > $CONFFILE
rm -f usermails
/usr/sbin/postmap $CONFFILE

cd $OLDIR

