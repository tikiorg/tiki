<?php
// Initialization
require_once('tiki-setup_base.php');


require ("lib/webmail/mimeDecode.php");
require ("lib/webmail/pop3.php");


$current=$tikilib->get_current_webmail_account($user);
$pop3=new POP3($current["pop"],$current["username"],$current["pass"]);
$pop3->Open();
$message = $pop3->GetMessage($_REQUEST["msgid"]) ;
$smarty->assign('msgid',$_REQUEST["msgid"]);
$full = $message["full"];
$pop3->Close();
$params = array(
                                        'input'          => $full,
                                        'crlf'           => "\r\n",
                                        'include_bodies' => TRUE,
                                        'decode_headers' => TRUE,
                                        'decode_bodies'  => TRUE
                                        );

$output = Mail_mimeDecode::decode($params);
$part = $output->parts[$_REQUEST["getpart"]];
$type = $part->headers["content-type"];
$content = $part->body;
$names=split(';',$part->headers["content-disposition"]);
$names=split('=',$names[1]);
$file=$names[1];

header("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header( "Content-Disposition: inline; filename=$file" );
echo "$content";    
?>