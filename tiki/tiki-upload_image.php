<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/imagegals/imagegallib.php');

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// Now check permissions to access this page
if($tiki_p_upload_images != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-upload_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-upload_image","show_image",$foo["path"]);
$smarty->assign('url_browse',httpPrefix().$foo1);
$smarty->assign('url_show',httpPrefix().$foo2);

$smarty->assign('show','n');
// Process an upload here
if(isset($_REQUEST["upload"])) {
  // Check here if it is an upload or an URL
  $smarty->assign('individual','n');
  if($userlib->object_has_one_permission($_REQUEST["galleryId"],'image gallery')) {
    $smarty->assign('individual','y');
    if($tiki_p_admin != 'y') {
      // Now get all the permissions that are set for this type of permissions 'image gallery'
      $perms = $userlib->get_permissions(0,-1,'permName_desc','','image galleries');
      foreach($perms["data"] as $perm) {
        $permName=$perm["permName"];
        if($userlib->object_has_permission($user,$_REQUEST["galleryId"],'image gallery',$permName)) {
          $$permName = 'y';
          $smarty->assign("$permName",'y');
        } else {
          $$permName = 'n';
          $smarty->assign("$permName",'n');
        }
      }
    }
  }
  if($tiki_p_admin_galleries == 'y') {
    $tiki_p_view_image_gallery = 'y';
    $tiki_p_upload_images = 'y';
    $tiki_p_create_galleries = 'y';
  }

  if($tiki_p_upload_images != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot upload images"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }

  
  $gal_info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
  if($gal_info["thumbSizeX"]==0) $gal_info["thumbSizeX"]=80;
  if($gal_info["thumbSizeY"]==0) $gal_info["thumbSizeY"]=80;  
  // Check the user to be admin or owner or the gallery is public
  if($tiki_p_admin_galleries!='y' && (!$user || $user!=$gal_info["user"]) && $gal_info["public"]!='y') {
    $smarty->assign('msg',tra("Permission denied you can upload images but not to this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
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
      $filename = $pinfo["basename"];
      $size = strlen($data);
    } else {
      $error_msg=tra("Cannot get image from URL");
    }
  } else {
    // We process here file uploads
    if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    
       if(!empty($gal_match_regex)) {
         if(!preg_match("/$gal_match_regex/",$_FILES['userfile1']['name'],$reqs)) {
           $smarty->assign('msg',tra('Invalid imagename (using filters for filenames)'));
           $smarty->display("styles/$style_base/error.tpl");
           die;  	
         }
       }
       if(!empty($gal_nmatch_regex)) {
          if(preg_match("/$gal_nmatch_regex/",$_FILES['userfile1']['name'],$reqs)) {
           $smarty->assign('msg',tra('Invalid imagename (using filters for filenames)'));
           $smarty->display("styles/$style_base/error.tpl");
           die;  	
         }
       }
      $type = $_FILES['userfile1']['type'];
      $size = $_FILES['userfile1']['size'];
      $filename = $_FILES['userfile1']['name'];
      


      // Check for a zip file.....
      // Fixed by Flo
      if(substr($filename,strlen($filename)-3)=='zip') {
        if($tiki_p_batch_upload_images == 'y') {
        if($imagegallib->process_batch_image_upload($_REQUEST["galleryId"],$_FILES['userfile1']['tmp_name'],$user) == 0) {
          $smarty->assign('msg',tra('Error processing zipped image package'));
          $smarty->display("styles/$style_base/error.tpl");
          die;
        }
        
        header("location: tiki-browse_gallery.php?galleryId=".$_REQUEST["galleryId"]);
        die();

        } else {
           $smarty->assign('msg',tra('No permission to upload zipped image packages'));
           $smarty->display("styles/$style_base/error.tpl");
           die;         
        }
      }
   
      $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
      $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
      fclose($fp);
      
    } else {
      $error_msg=tra("cannot process upload");
    }
  }

  $up_thumb = 0;
  // If the thumbnail was uploaded
  if(isset($_FILES['userfile2'])&&is_uploaded_file($_FILES['userfile2']['tmp_name'])) {
      $fp = fopen($_FILES['userfile2']['tmp_name'],"rb");
      $thumb_data = fread($fp,filesize($_FILES['userfile2']['tmp_name']));
      fclose($fp);
      $thumb_type = $_FILES['userfile2']['type'];
      $thumb_size = $_FILES['userfile2']['size'];
      $thumb_name = $_FILES['userfile2']['name'];
      $up_thumb = 1;
  } 
  if(empty($_REQUEST["name"]) && 
  	(!isset($_REQUEST["use_filename"]) || $_REQUEST["use_filename"] == 'off' )) {
    $error_msg=tra("You have to provide a name to the image");
  }
  if($error_msg) {
    $smarty->assign('msg',$error_msg);
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }

  if( isset($_REQUEST["use_filename"]) && $_REQUEST["use_filename"] == 'on' ) {
    $name=$filename;
  } else {
    $name=$_REQUEST["name"];
  }
  if(isset($data)) {
    if(!$up_thumb) {
      if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
        $img = imagecreatefromstring($data);
        $size_x = imagesx($img);
        $size_y = imagesy($img);
        if ($size_x > $size_y)
          $tscale = ((int)$size_x / $gal_info["thumbSizeX"]);
        else
          $tscale = ((int)$size_y / $gal_info["thumbSizeY"]);
        $tw = ((int)($size_x / $tscale));
        $ty = ((int)($size_y / $tscale));
        if (chkgd2()) {
          $t = imagecreatetruecolor($tw,$ty);
          imagecopyresampled($t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
        } else {
          $t = imagecreate($tw,$ty);
          $imagegallib->ImageCopyResampleBicubic( $t, $img, 0,0,0,0, $tw,$ty, $size_x, $size_y);
        }
        // CHECK IF THIS TEMP IS WRITEABLE OR CHANGE THE PATH TO A WRITEABLE DIRECTORY
        //$tmpfname = 'temp.jpg';
        $tmpfname = tempnam ($tmpDir, "FOO").'.jpg';     
        imagejpeg($t,$tmpfname);
        // Now read the information
        $fp = fopen($tmpfname,"rb");
        $t_data = fread($fp, filesize($tmpfname));
        fclose($fp);
        unlink($tmpfname);
        $t_pinfo = pathinfo($tmpfname);
        $t_type = $t_pinfo["extension"];
        $t_type='image/'.$t_type;

        $imageId = $imagegallib->insert_image($_REQUEST["galleryId"],$name,$_REQUEST["description"],$filename, $type, $data, $size, $size_x, $size_y, $user,$t_data,$t_type);
      } else {
        $tmpfname='';
        $imageId = $imagegallib->insert_image($_REQUEST["galleryId"],$name,$_REQUEST["description"],$filename, $type, $data, $size, 0, 0, $user,'','');
      }
    } else {
      if(function_exists("ImageCreateFromString")&&(!strstr($type,"gif"))) {
        $img = imagecreatefromstring($data);
        $size_x = imagesx($img);
        $size_y = imagesy($img);
      } else {
        $size_x = 0;
        $size_y = 0;
      }

      $imageId = $imagegallib->insert_image($_REQUEST["galleryId"],$name,$_REQUEST["description"],$filename, $type, $data, $size, $size_x, $size_y, $user,$thumb_data,$thumb_type);
    }
    if(!$imageId) {
       $smarty->assign('msg',tra('Upload failed'));
       $smarty->display("styles/$style_base/error.tpl");
       die;  	
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
if($tiki_p_admin_galleries != 'y') {
  $galleries = $imagegallib->list_visible_galleries(0,-1,'lastModif_desc', $user,'');
} else {
  $galleries = $imagegallib->list_galleries(0,-1,'lastModif_desc', $user,'');
}
for($i=0;$i<count($galleries["data"]);$i++) {
  if($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"],'image gallery')) {
    $galleries["data"][$i]["individual"]='y';
    
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'image gallery','tiki_p_view_image_gallery')) {
      $galleries["data"][$i]["individual_tiki_p_view_image_gallery"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_view_image_gallery"]='n';
    }
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'image gallery','tiki_p_upload_images')) {
      $galleries["data"][$i]["individual_tiki_p_upload_images"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_upload_images"]='n';
    }
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'image gallery','tiki_p_create_galleries')) {
      $galleries["data"][$i]["individual_tiki_p_create_galleries"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_create_galleries"]='n';
    }
    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'image gallery','tiki_p_admin_galleries')) {
      $galleries["data"][$i]["individual_tiki_p_create_galleries"]='y';
      $galleries["data"][$i]["individual_tiki_p_upload_images"]='y';
      $galleries["data"][$i]["individual_tiki_p_view_image_gallery"]='y';
    } 
    
  } else {
    $galleries["data"][$i]["individual"]='n';
  }
}
$smarty->assign_by_ref('galleries',$galleries["data"]);

$section='galleries';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-upload_image.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
