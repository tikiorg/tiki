<html>
<head>
<title>PHP Port Connection Test</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">

<p>This page simply attempts to connect to several well known servers. It only makes an initial
connection and then closes it, it doesn't send / receive any data. If they succeed, then your copy of
PHP is correctly configured to allow outgoing connections, and to use whatever proxies are
necessary. If they fail, then either all the hosts tested are down (not very likely) or your copy
of PHP is set to deny outgoing connections, or a proxy is in the way and PHP isn't configured to
handle it.</p> 

<p>Please don't ask that I help you configure your installation of PHP. I simply
don't have the time. Sorry - nothing personal :-)</p>

<hr>
<?


$Connections = array("www.weather.com","www.yahoo.com","www.msn.com","www.cnet.com","www.aol.com");
$Port = 80; // http
$Timeout = 30; // seconds


$Failed = 0; $Success = 0;

foreach ($Connections as $Connection) {

  print "<p>Attempting to connect to <a href=\"http://$Connection\" target=\"_blank\">$Connection</a>...<br>";
  
  $socket = fsockopen ($Connection, $Port, $errno, $errstr, $Timeout);
  if ($socket == false) {
    $Failed++;
    print "<font color=red><b>Connection FAILED!</b></font><br>";
    print "Error $errno: $errstr";
    if ($errno == 0) {
      print "<br>The PHP manual says 'error 0 is an indication that the error occurred before the connect() call. This is most likely due to a problem initializing the socket.' (eg: you're not connected to the Net, invalid hostname, etc)<br>";
    }
    print "</p>\n";
  } else {
    $Success++;
    print "<font color=green>Success!</font></p>\n";
    fclose ($socket);
  }

}



?>
<hr>
<p><? print $Failed; ?> failed connections<br>
<? print $Success; ?> connections succeeded</p>
</body>
</html>
