<?php
	// Initialization
	require_once('tiki-setup.php');
	include_once('lib/filegals/filegallib.php');
	
	if($feature_file_galleries != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	  $smarty->display("error.tpl");
	  die;  
	}
	
	if($tiki_p_view_file_gallery != 'y') {
	  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
	  $smarty->display("error.tpl");
	  die;
	}
	
	if(!isset($_REQUEST["galleryId"])) {
	  $_REQUEST["galleryId"]=0;
	}
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
	
	
	if(isset($_REQUEST["find"])) {
	  $find = $_REQUEST["find"];  
	} else {
	  $find = ''; 
	}
	$smarty->assign('find',$find);
	
	if(!isset($_REQUEST["galleryId"])) {
	  $_REQUEST["galleryId"] = 0;
	}
	$smarty->assign('galleryId',$_REQUEST["galleryId"]);
	
	$foo = parse_url($_SERVER["REQUEST_URI"]);
	$foo["path"]=str_replace("tiki-file_galleries","tiki-list_file_gallery",$foo["path"]);
	$smarty->assign('url',$tikilib->httpPrefix().$foo["path"]);
	
	// Init smarty variables to blank values
	//$smarty->assign('theme','');
	$smarty->assign('name','');
	$smarty->assign('description','');
	
	$smarty->assign('show_id','y');
	$smarty->assign('show_icon','y');
	$smarty->assign('show_name','a');
	$smarty->assign('show_size','y');
	$smarty->assign('show_description','y');
	$smarty->assign('show_created','y');
	$smarty->assign('show_dl','y');
	$smarty->assign('max_desc',1024);
	
	$smarty->assign('maxRows',10);
	$smarty->assign('public','n');
	$smarty->assign('edited','n');
	$smarty->assign('edit_mode','n');
	$smarty->assign('visible','y');
	
	// If we are editing an existing gallery prepare smarty variables
	if(isset($_REQUEST["edit_mode"])&&$_REQUEST["edit_mode"]) {
	  // Get information about this galleryID and fill smarty variables
	  $smarty->assign('edit_mode','y');
	  $smarty->assign('edited','y');
	  if($_REQUEST["galleryId"]>0) {
	    $info = $filegallib->get_file_gallery_info($_REQUEST["galleryId"]);
	
	    //$smarty->assign_by_ref('theme',$info["theme"]);
	    $smarty->assign_by_ref('name',$info["name"]);
	    $smarty->assign_by_ref('description',$info["description"]);
	
		$smarty->assign('show_id',$info['show_id']);
		$smarty->assign('show_icon',$info['show_icon']);
		$smarty->assign('show_name',$info['show_name']);
		$smarty->assign('show_size',$info['show_size']);
		$smarty->assign('show_description',$info['show_description']);
		$smarty->assign('show_created',$info['show_created']);
		$smarty->assign('show_dl',$info['show_dl']);
		$smarty->assign('max_desc',$info['max_desc']);
	
	
	    $smarty->assign_by_ref('maxRows',$info["maxRows"]);
	    $smarty->assign_by_ref('public',$info["public"]);
	    $smarty->assign_by_ref('visible',$info["visible"]);
	  }
	}
	
	
	// Process the insertion or modification of a gallery here
	if(isset($_REQUEST["edit"])) {
		check_ticket('fgal');
	  // Saving information
	  // If the user is not gallery admin
	  if($tiki_p_admin_file_galleries != 'y') {
	    if($tiki_p_create_file_galleries != 'y') {
	      // If you can't create a gallery then you can't edit a gallery because you can't have a gallery
	      $smarty->assign('msg',tra("Permission denied you cannot create galleries and so you cant edit them"));
	      $smarty->display("error.tpl");
	      die;  
	    }
	    // If the user can create a gallery then check if he can edit THIS gallery
	    if($_REQUEST["galleryId"]>0) {
	      $info = $filegallib->get_file_gallery_info($_REQUEST["galleryId"]);
	      if(!$user || $info["user"]!=$user) {
	        $smarty->assign('msg',tra("Permission denied you cannot edit this gallery"));
	        $smarty->display("error.tpl");
	        die;  
	      }
	    }
	  }
	  // Everything is ok so we proceed to edit the gallery
	  $smarty->assign('edit_mode','y');
	  //$smarty->assign_by_ref('theme',$_REQUEST["theme"]);
	  $smarty->assign_by_ref('name',$_REQUEST["name"]);
	  $smarty->assign_by_ref('description',$_REQUEST["description"]);
	  
	  $smarty->assign('show_id',isset($_REQUEST['show_id'])?'y':'n');
	  $smarty->assign('show_icon',isset($_REQUEST['show_icon'])?'y':'n');
	  $smarty->assign('show_name',($_REQUEST['show_name']));
	  $smarty->assign('show_size',isset($_REQUEST['show_size'])?'y':'n');
	  $smarty->assign('show_description',isset($_REQUEST['show_description'])?'y':'n');
	  $smarty->assign('show_created',isset($_REQUEST['show_created'])?'y':'n');
	  $smarty->assign('show_dl',isset($_REQUEST['show_dl'])?'y':'n');
	  $smarty->assign('max_desc',($_REQUEST['max_desc']));
	
	  
	  $smarty->assign_by_ref('maxRows',$_REQUEST["maxRows"]);
	  $smarty->assign_by_ref('rowImages',$_REQUEST["rowImages"]);
	  $smarty->assign_by_ref('thumbSizeX',$_REQUEST["thumbSizeX"]);
	  $smarty->assign_by_ref('thumbSizeY',$_REQUEST["thumbSizeY"]);
	  if(isset($_REQUEST["visible"]) && $_REQUEST["visible"]=="on") {
	    $smarty->assign('visible','y');
	    $visible ='y';
	  } else {
	    $visible ='n';
	  }
	  if(isset($_REQUEST["public"]) && $_REQUEST["public"]=="on") {
	    $smarty->assign('public','y');
	    $public ='y';
	  } else {
	    $public ='n';
	  }
	  $smarty->assign('public',$public);
	  $smarty->assign('visible',$visible);
	  $_REQUEST['show_id']=isset($_REQUEST['show_id'])?'y':'n';
	  $_REQUEST['show_icon']=isset($_REQUEST['show_icon'])?'y':'n';
	  $_REQUEST['show_description']=isset($_REQUEST['show_description'])?'y':'n';
	  $_REQUEST['show_created']=isset($_REQUEST['show_created'])?'y':'n';
	  $_REQUEST['show_dl']=isset($_REQUEST['show_dl'])?'y':'n';
	  $_REQUEST['show_size']=isset($_REQUEST['show_size'])?'y':'n';
	  $fgid = $filegallib->replace_file_gallery($_REQUEST["galleryId"], $_REQUEST["name"], $_REQUEST["description"], $user, $_REQUEST["maxRows"], $public, $visible,$_REQUEST['show_id'],$_REQUEST['show_icon'],$_REQUEST['show_name'],$_REQUEST['show_size'],$_REQUEST['show_description'],$_REQUEST['show_created'],$_REQUEST['show_dl'],$_REQUEST['max_desc']);
	  
	  $cat_type='file gallery';
	  $cat_objid = $fgid;
	  $cat_desc = substr($_REQUEST["description"],0,$_REQUEST['max_desc']);
	  $cat_name = $_REQUEST["name"];
	  $cat_href="tiki-list_file_gallery.php?galleryId=".$cat_objid;
	  include_once("categorize.php");
	  
	  $smarty->assign('edit_mode','n');
	}
	
	
	if(isset($_REQUEST["removegal"])) {
	  if($tiki_p_admin_file_galleries != 'y') {
	     $info = $filegallib->get_file_gallery_info($_REQUEST["removegal"]);
	     if(!$user || $info["user"]!=$user) {
	       $smarty->assign('msg',tra("Permission denied you cannot remove this gallery"));
	       $smarty->display("error.tpl");
	       die;  
	     }
	  }
		$area = 'delfilegal';
		if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$filegallib->remove_file_gallery($_REQUEST["removegal"]);
		} else {
			key_get($area);
		}
	}
	
	if(!isset($_REQUEST["sort_mode"])) {
	  $sort_mode = 'created_desc'; 
	} else {
	  $sort_mode = $_REQUEST["sort_mode"];
	} 
	$smarty->assign_by_ref('sort_mode',$sort_mode);
	
	// If offset is set use it if not then use offset =0
	// use the maxRecords php variable to set the limit
	// if sortMode is not set then use lastModif_desc
	if(!isset($_REQUEST["offset"])) {
	  $offset = 0;
	} else {
	  $offset = $_REQUEST["offset"]; 
	}
	$smarty->assign_by_ref('offset',$offset);
	
	// Get the list of libraries available for this user (or public galleries)
	$galleries = $filegallib->list_file_galleries($offset,$maxRecords,$sort_mode, 'admin',$find);
	// Now traverse the galleries and check if there're individual permissions preventing the
	// user from browsing/editing/removing/listing/uploading to the gallery
	for($i=0;$i<count($galleries["data"]);$i++) {
	  if($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"],'file gallery')) {
	    $galleries["data"][$i]["individual"]='y';
	    
		$galleries["data"][$i]["individual_tiki_p_view_file_gallery"]='y';

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
	
	// If there're more records then assign next_offset
	$cant_pages = ceil($galleries["cant"] / $maxRecords);
	$smarty->assign_by_ref('cant_pages',$cant_pages);
	$smarty->assign('actual_page',1+($offset/$maxRecords));
	
	if($galleries["cant"] > ($offset + $maxRecords)) {
	  $smarty->assign('next_offset',$offset + $maxRecords);
	} else {
	  $smarty->assign('next_offset',-1); 
	}
	// If offset is > 0 then prev_offset
	if($offset>0) {
	  $smarty->assign('prev_offset',$offset - $maxRecords);  
	} else {
	  $smarty->assign('prev_offset',-1); 
	}
	
	$smarty->assign_by_ref('galleries',$galleries["data"]);
	//print_r($galleries["data"]);
	
	$cat_type='file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include_once("categorize_list.php");
	
	$section='file_galleries';
	include_once('tiki-section_options.php');
	
ask_ticket('fgal');
	
	// Display the template
	$smarty->assign('mid','tiki-file_galleries.tpl');
	$smarty->display("tiki.tpl");
?>
