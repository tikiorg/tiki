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
        "sendPage" => array( "function" => "sendPage"),
        "sendArticle" => array("function" => "sendArticle")
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

function sendArticle($params) {
 // Get the page and store it in received_pages
 global $tikilib,$userlib;
 $pp=$params->getParam(0); $site=$pp->scalarval();
 $pp=$params->getParam(1); $username=$pp->scalarval();
 $pp=$params->getParam(2); $password=$pp->scalarval();
 $pp=$params->getParam(3); $title=$pp->scalarval();
 $pp=$params->getParam(4); $authorName=$pp->scalarval();
 $pp=$params->getParam(5); $size=$pp->scalarval();
 $pp=$params->getParam(6); $use_image=$pp->scalarval();
 $pp=$params->getParam(7); $image_name=$pp->scalarval();
 $pp=$params->getParam(8); $image_type=$pp->scalarval();
 $pp=$params->getParam(9); $image_size=$pp->scalarval();
 $pp=$params->getParam(10);$image_x =$pp->scalarval();
 $pp=$params->getParam(11);$image_y =$pp->scalarval();
 $pp=$params->getParam(12);$image_data =$pp->scalarval();
 $pp=$params->getParam(13);$publishDate =$pp->scalarval();
 $pp=$params->getParam(14);$created =$pp->scalarval();
 $pp=$params->getParam(15);$heading =$pp->scalarval();
 $pp=$params->getParam(16);$body =$pp->scalarval();
 $pp=$params->getParam(17);$hash =$pp->scalarval();
 $pp=$params->getParam(18);$author =$pp->scalarval();
 $pp=$params->getParam(19);$type =$pp->scalarval(); 
 $pp=$params->getParam(20);$rating =$pp->scalarval(); 
 // 
 if(!$userlib->validate_user($username,$password)) {
   return new xmlrpcresp(0, 101, "Invalid username or password");
 }
 
 // Verify if the user has tiki_p_sendme_pages
 if(!$userlib->user_has_permission($username,'tiki_p_sendme_articles')) {
    return new xmlrpcresp(0, 101, "Permissions denied user $username cannot send articles to this site");
 }  
 // Store the page in the tiki_received_pages_table
 $title = base64_decode($title);
 $authorName = base64_decode($authorName);
 $image_data = base64_decode($image_data);
 $heading = base64_decode($heading);
 $body = base64_decode($body);
  
 $tikilib->receive_article($site,$username,$title,$authorName,$size,$use_image,$image_name,$image_type,$image_size,$image_x,$image_y,$image_data,$publishDate,$created,$heading,$body,$hash,$author,$type,$rating);
 
 return new xmlrpcresp(new xmlrpcval(1,"boolean"));
}

 
?>