<?php # $Header: /cvsroot/tikiwiki/tiki/testrpc.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");

// EDIT FROM THIS LINE
$server_port=80;
$server_uri="localhost";
$server_path="/orion/tiki/xmlrpc.php";
// DON'T EDIT BELOW THIS LINE
$client = new xmlrpc_client("$server_path", "$server_uri", $server_port);
$client->setDebug(1);

$appkey='';
$username='admin';
$password='pepe';
/*
$blogs=new xmlrpcmsg('blogger.newPost',array(new xmlrpcval($appkey,"string"),
                                          new xmlrpcval("1","string"),
                                          new xmlrpcval($username,"string"),
                                          new xmlrpcval($password,"string"),
                                          new xmlrpcval("pepe","string"),
                                          new xmlrpcval(0,"boolean"),
                                          ));
*/                            

$blogs=new xmlrpcmsg('blogger.getRecentPosts',array(new xmlrpcval($appkey,"string"),
                                          new xmlrpcval(1),
                                          new xmlrpcval($username,"string"),
                                          new xmlrpcval($password,"string"),
                                          new xmlrpcval(10)
                                          ));
              
$result=$client->send($blogs);

if(!$result) {
  $errorMsg='Cannot send message to server maybe the server is down';
} else {
  if(!$result->faultCode()) {
    $blogs=xmlrpc_decode($result->value());
    print_r($blogs);
  } else {
    $errorMsg=$result->faultstring();
    print("Error: $errioMsg<br/>");
  }
}


?>