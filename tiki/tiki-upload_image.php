<?php
// Initialization
require_once('tiki-setup.php');

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// Now check permissions to access this page

if($tiki_p_upload_images != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display('error.tpl');
  die;  
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-upload_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-upload_image","show_image",$foo["path"]);
$smarty->assign('url_browse',$_SERVER["SERVER_NAME"].$foo1);
$smarty->assign('url_show',$_SERVER["SERVER_NAME"].$foo2);



$smarty->assign('show','n');
// Process an upload here
if(isset($_REQUEST["upload"])) {
  // Check here if it is an upload or an URL
  $gal_info = $tikilib->get_gallery($_REQUEST["galleryId"]);
  if($gal_info["thumbSizeX"]==0) $gal_info["thumbSizeX"]=80;
  if($gal_info["thumbSizeY"]==0) $gal_info["thumbSizeY"]=80;  
  // Check the user to be admin or owner or the gallery is public
  if($user!='admin' && $user!=$gal_info["user"] && $gal_info["public"]!='y') {
    $smarty->assign('msg',tra("Permission denied you can upload images but not to this gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $error_msg='';
  if(!empty($_REQUEST["url"])) {
    // Get the image from a URL
    $fp = fopen($_REQUEST["url"],"r");
    if($fp) {
      $data = fread($fp, 1000000);
      fclose($fp);
      $url_info = parse_url($_REQUEST["url"]);
      $pinfo = pathinfo($url_info["path"]);
      $type = "image/".$pinfo["extension"];
      $name = $pinfo["basename"];
      $size = strlen($data);
    } else {
      $error_msg=tra("Cannot get image from URL");
    }
  } else {
    // We process here file uploads
    if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
      $fp = fopen($_FILES['userfile1']['tmp_name'],"r");
      $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
      fclose($fp);
      $type = $_FILES['userfile1']['type'];
      $size = $_FILES['userfile1']['size'];
      $name = $_FILES['userfile1']['name'];
    } else {
      $error_msg=tra("cannot process upload");
    }
  }
  if(empty($_REQUEST["name"])) {
    $error_msg=tra("You have to provide a name to the image");
  }
  if($error_msg) {
    $smarty->assign('msg',$error_msg);
    $smarty->display('error.tpl');
    die;  
  }
  if(isset($data)) {
    if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
      $img = imagecreatefromstring($data);
      $size_x = imagesx($img);
      $size_y = imagesy($img);
      // Create thumbnail here 
      // Use the gallery preferences to get the data
      $t = imagecreate($gal_info["thumbSizeX"],$gal_info["thumbSizeY"]);
      $tikilib->ImageCopyResampleBicubic( $t, $img, 0,0,0,0, $gal_info["thumbSizeX"],$gal_info["thumbSizeY"], $size_x, $size_y);
      // CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
      //$tmpfname = 'temp.jpg';
      $tmpfname = tempnam ("/tmp", "FOO").'.jpg';     
      imagejpeg($t,$tmpfname);
      // Now read the information
      $fp = fopen($tmpfname,"r");
      $t_data = fread($fp, filesize($tmpfname));
      fclose($fp);
      unlink($tmpfname);
      $t_pinfo = pathinfo($tmpfname);
      $t_type = $t_pinfo["extension"];
      $t_type='image/'.$t_type;
      $imageId = $tikilib->insert_image($_REQUEST["galleryId"],$_REQUEST["name"],$_REQUEST["description"],$name, $type, $data, $size, $size_x, $size_y, $user,$t_data,$t_type);
    } else {
      $tmpfname='';
      $imageId = $tikilib->insert_image($_REQUEST["galleryId"],$_REQUEST["name"],$_REQUEST["description"],$name, $type, $data, $size, 0, 0, $user,'','');
    }
    $smarty->assign_by_ref('imageId',$imageId);
    // Now that the image was inserted we can display the image here.
    $smarty->assign('show','y');
    $smarty->assign_by_ref('tmpfname',$tmpfname);
    $smarty->assign_by_ref('fname',$_REQUEST["url"]);
  }
}

// Get the list of galleries to display the select box in the template
if(isset($_REQUEST["galleryId"])) {
  $smarty->assign_by_ref('galleryId',$_REQUEST["galleryId"]);
} else {
  $smarty->assign('galleryId','');
}
$galleries = $tikilib->list_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign_by_ref('galleries',$galleries["data"]);

// Display the template
$smarty->assign('mid','tiki-upload_image.tpl');
$smarty->display('tiki.tpl');
?>
