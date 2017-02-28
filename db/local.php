<?php
$db_tiki='mysql';
$dbversion_tiki='8.0';
$host_tiki='';
$user_tiki='';
$pass_tiki='';
$dbs_tiki='';
$client_charset='utf8';

foreach ($_SERVER as $key => $value) {
    if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
        continue;
    }
    
    $host_tiki = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
    $dbs_tiki = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
    $user_tiki = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
    $pass_tiki = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
}
