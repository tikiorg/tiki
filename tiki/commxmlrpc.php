<?
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
include_once('lib/userslib.php');
include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");



$tikilib = new Tikilib($dbTiki);
$userlib = new Userslib($dbTiki);


if($tikilib->get_preference("feature_comm",'n') != 'y') {
  die;  
}


$map = array (
        "sendPage" => array( "function" => "sendPage")
);

$s=new xmlrpc_server( $map );

/* Validates the user and returns user information */
function sendPage($params) {
 // Get the page and store it in received_pages
 global $tikilib,$userlib;
 $pp=$params->getParam(0); $site=$pp->scalarval();
 $pp=$params->getParam(1); $username=$pp->scalarval();
 $pp=$params->getParam(2); $password=$pp->scalarval();
 $pp=$params->getParam(3); $pageName=$pp->scalarval();
 $pp=$params->getParam(4); $data=$pp->scalarval();
 $pp=$params->getParam(5); $comment=$pp->scalarval();
 
 // 
 if(!$userlib->validate_user($username,$password)) {
   return new xmlrpcresp(0, 101, "Invalid username or password");
 }
 
 // Verify if the user has tiki_p_sendme_pages
 if(!$userlib->user_has_permission($username,'tiki_p_sendme_pages')) {
    return new xmlrpcresp(0, 101, "Permissions denied user $username cannot send pages to this site");
 }  
 // Store the page in the tiki_received_pages_table
 $data = base64_decode($data);
 
 $tikilib->receive_page($pageName,$data,$comment,$site,$username);
 /*
 if () {                                 
     return new xmlrpcresp(new xmlrpcval(1,"boolean"));
 } else {
    return new xmlrpcresp(0, 101, "Invalid username or password");
 } 
 */
 return new xmlrpcresp(new xmlrpcval(1,"boolean"));
}
 
?>