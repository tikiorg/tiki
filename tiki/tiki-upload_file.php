<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/filegals/filegallib.php');


if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Now check permissions to access this page
if($tiki_p_upload_files != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-upload_file","tiki-download_file",$foo["path"]);
$smarty->assign('url_browse',httpPrefix().$foo1);

if(!isset($_REQUEST["description"])) $_REQUEST["description"]='';


$smarty->assign('show','n');
// Process an upload here
if(isset($_REQUEST["upload"])) {
  // Check here if it is an upload or an URL
  $smarty->assign('individual','n');
  if($userlib->object_has_one_permission($_REQUEST["galleryId"],'file gallery')) {
    $smarty->assign('individual','y');
    if($tiki_p_admin != 'y') {
      // Now get all the permissions that are set for this type of permissions 'file gallery'
      $perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
      foreach($perms["data"] as $perm) {
        $permName=$perm["permName"];
        if($userlib->object_has_permission($user,$_REQUEST["galleryId"],'file gallery',$permName)) {
          $$permName = 'y';
          $smarty->assign("$permName",'y');
        } else {
          $$permName = 'n';
          $smarty->assign("$permName",'n');
        }
      }
    }
  }
  if($tiki_p_admin_file_galleries == 'y') {
      $tiki_p_upload_filesimages = 'y';
  }

  if($tiki_p_upload_files != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
  }
  
  $gal_info = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
  // Check the user to be admin or owner or the gallery is public
  if($tiki_p_admin_file_galleries!='y' && (!$user || $user!=$gal_info["user"]) && $gal_info["public"]!='y') {
    $smarty->assign('msg',tra("Permission denied you can upload files but not to this file gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  $error_msg='';
  /*
  if(!empty($_REQUEST["url"])) {
    // Get the file from a URL
     $data='';
     $fp = @fopen($file,"rb");
     if($fp) {
       while(!feof($fp))
       {
         $data.= fread($fp,1024);
       }
       fclose($fp);
       $url_info = parse_url($_REQUEST["url"]);
       $pinfo = pathinfo($url_info["path"]);
       $name = $pinfo["basename"];
       $size = strlen($data);
      } else {
        $error_msg=tra("Cannot get file from URL");
      }
   } else {
   */
     // We process here file uploads
     if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
       // Check the name
       if(!empty($fgal_match_regex)) {
         if(!preg_match("/$fgal_match_regex/",$_FILES['userfile1']['name'],$reqs)) {
           $smarty->assign('msg',tra('Invalid filename (using filters for filenames)'));
           $smarty->display("styles/$style_base/error.tpl");
           die;  	
         }
       }
       if(!empty($fgal_nmatch_regex)) {
          if(preg_match("/$fgal_nmatch_regex/",$_FILES['userfile1']['name'],$reqs)) {
           $smarty->assign('msg',tra('Invalid filename (using filters for filenames)'));
           $smarty->display("styles/$style_base/error.tpl");
           die;  	
         }
       }
       $name = $_FILES['userfile1']['name'];
       if(isset($_REQUEST["isbatch"])&&$_REQUEST["isbatch"]=='on' && substr($name,strlen($name)-3)=='zip') {
        if($tiki_p_batch_upload_files == 'y') {
        $filegallib->process_batch_file_upload($_REQUEST["galleryId"],$_FILES['userfile1']['tmp_name'],$user,$_REQUEST["description"]);
        header("location: tiki-list_file_gallery.php?galleryId=".$_REQUEST["galleryId"]);
        } else {
           $smarty->assign('msg',tra('No permission to upload zipped image packages'));
           $smarty->display("styles/$style_base/error.tpl");
           die;  	
        }
      }
       
       $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
       $data = '';
       $fhash='';
       if($fgal_use_db == 'n') {
         $fhash = md5($name = $_FILES['userfile1']['name']);    
         $fhash = md5(uniqid($fhash));
         @$fw = fopen($fgal_use_dir.$fhash,"w");
         if(!$fw) {
           $smarty->assign('msg',tra('Cannot write to this file:').$fhash);
           $smarty->display("styles/$style_base/error.tpl");
           die;  
         }
       }
       while(!feof($fp)) {
         if($fgal_use_db == 'y') {
           $data .= fread($fp,8192*16);
         } else {
           $data = fread($fp,8192*16);
           fwrite($fw,$data);
         }
       }
       fclose($fp);
       if($fgal_use_db == 'n') {
         fclose($fw);
         $data='';
       }
       $size = $_FILES['userfile1']['size'];
       $name = $_FILES['userfile1']['name'];
       $type = $_FILES['userfile1']['type'];
     } else {
       $error_msg=tra("cannot process upload");
    }
  /*}*/
  /* Commented by LeChucDaPirate on May 2, 2003
     This field is not required anymore 
  if(empty($_REQUEST["name"])) {
    $error_msg=tra("You have to provide a name to the file");
  }
  */
  if($error_msg) {
    $smarty->assign('msg',$error_msg);
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  if($fgal_use_db == 'y') {
  if(!isset($data) || strlen($data)<1) {
     $smarty->assign('msg',tra('Upload was not successful'));
     $smarty->display("styles/$style_base/error.tpl");
     die;  
  }
  }
  if(isset($data)) {
      $smarty->assign('upload_name',$name);
      $smarty->assign('upload_size',$size);
      $fileId = $filegallib->insert_file($_REQUEST["galleryId"],$_REQUEST["name"],$_REQUEST["description"],$name, $data, $size, $type, $user,$fhash);
      if(!$fileId) {
	     $smarty->assign('msg',tra('Upload was not successful (maybe a duplicate file)'));
    	 $smarty->display("styles/$style_base/error.tpl");
     	die;  
      }
      $smarty->assign_by_ref('fileId',$fileId);
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
if($tiki_p_admin_file_galleries != 'y') {
  $galleries = $tikilib->list_visible_file_galleries(0,-1,'lastModif_desc', $user,'');
} else {
  $galleries = $filegallib->list_file_galleries(0,-1,'lastModif_desc', $user,'');
}
for($i=0;$i<count($galleries["data"]);$i++) {
  if($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"],'file gallery')) {
    $galleries["data"][$i]["individual"]='y';
    
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_view_file_gallery')) {
      $galleries["data"][$i]["individual_tiki_p_view_file_gallery"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_view_file_gallery"]='n';
    }
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_upload_files')) {
      $galleries["data"][$i]["individual_tiki_p_upload_files"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_upload_files"]='n';
    }
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_download_files')) {
      $galleries["data"][$i]["individual_tiki_p_download_files"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_download_files"]='n';
    }
    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_create_file_galleries')) {
      $galleries["data"][$i]["individual_tiki_p_create_file_galleries"]='y';
    } else {
      $galleries["data"][$i]["individual_tiki_p_create_file_galleries"]='n';
    }
    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_admin_file_galleries')) {
      $galleries["data"][$i]["individual_tiki_p_create_file_galleries"]='y';
      $galleries["data"][$i]["individual_tiki_p_download_files"]='y';
      $galleries["data"][$i]["individual_tiki_p_upload_files"]='y';
      $galleries["data"][$i]["individual_tiki_p_view_file_gallery"]='y';
    } 
    
  } else {
    $galleries["data"][$i]["individual"]='n';
  }
}

$smarty->assign_by_ref('galleries',$galleries["data"]);

$section='file_galleries';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-upload_file.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
