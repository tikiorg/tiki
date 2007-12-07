<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_galleries.php,v 1.57.2.1 2007-12-07 05:56:38 mose Exp $

	require_once('tiki-setup.php');
	include_once('lib/filegals/filegallib.php');
	
	if($prefs['feature_file_galleries'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	  $smarty->display("error.tpl");
	  die;  
	}

	if(!isset($_REQUEST["galleryId"])) {
	  $_REQUEST["galleryId"]=0;
	  $info = '';
	} else {
		$info = $filegallib->get_file_gallery_info($_REQUEST["galleryId"]);
	}

$tikilib->get_perm_object($_REQUEST["galleryId"], 'file gallery', $info, true);

	if ((isset($tiki_p_list_file_galleries) && $tiki_p_list_file_galleries != 'y') || (!isset($tiki_p_list_file_galleries) && $tiki_p_view_file_gallery != 'y')) {
	  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
	  $smarty->display("error.tpl");
	  die;
	}

// Initialize listing fields with default values
$fgal_list_id = $prefs['fgal_list_id'];
$fgal_list_name = $prefs['fgal_list_name'];
$fgal_list_description = $prefs['fgal_list_description'];
$fgal_list_type = $prefs['fgal_list_type'];
$fgal_list_created = $prefs['fgal_list_created'];
$fgal_list_lastmodif = $prefs['fgal_list_lastmodif'];
$fgal_list_user = $prefs['fgal_list_user'];
$fgal_list_files = $prefs['fgal_list_files'];
$fgal_list_hits = $prefs['fgal_list_hits'];
$fgal_list_parent = $prefs['fgal_list_parent'];
	
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
	} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	  global $categlib; include_once('lib/categories/categlib.php');
	  $perms_array = $categlib->get_object_categories_perms($user, 'file gallery', $_REQUEST['galleryId']);
   	  if ($perms_array) {
   	    $is_categorized = TRUE;
    	    foreach ($perms_array as $perm => $value) {
    	      $$perm = $value;
    	    }
   	  } else {
   	    $is_categorized = FALSE;
   	  }
	  if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
	    if (!isset($user)){
	      $smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
	      $smarty->assign('errortitle',tra("Please login"));
	    } else {
	      $smarty->assign('msg',tra("Permission denied you cannot view this page"));
    	    }
	    $smarty->display("error.tpl");
	    die;
	  }
       }
	
	
	if(isset($_REQUEST["find"])) {
	  $find = $_REQUEST["find"];  
	} else {
	  $find = ''; 
	}
	$smarty->assign('find',$find);
	
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
	$smarty->assign('show_creator','y');
	$smarty->assign('show_author','y');
	$smarty->assign('show_dl','y');
	$smarty->assign('max_desc',1024);
	$smarty->assign('show_lockedby', 'y');
	$smarty->assign('show_modified', 'n');
	
	$smarty->assign('maxRows',10);
	$smarty->assign('public','n');
	$smarty->assign('lockable', 'n');
	$smarty->assign('archives', -1);
	$smarty->assign('edited','n');
	$smarty->assign('edit_mode','n');
	$smarty->assign('dup_mode','n');
	$smarty->assign('visible','y');
	$smarty->assign('fgal_type','default');
	$smarty->assign('parentId', -1);
	$smarty->assign('creator', $user);
	$smarty->assign('sortorder', 'created');
	$smarty->assign('sortdirection', 'desc');
	
	// If we are editing an existing gallery prepare smarty variables
	if(isset($_REQUEST["edit_mode"])&& $_REQUEST["edit_mode"]) {
	  // Get information about this galleryID and fill smarty variables
	  $smarty->assign('edit_mode','y');
	  $smarty->assign('edited','y');
	  if($_REQUEST["galleryId"]>0) {
	
	    //$smarty->assign_by_ref('theme',$info["theme"]);
	    $smarty->assign_by_ref('name',$info["name"]);
	    $smarty->assign_by_ref('description',$info["description"]);
	
		$smarty->assign('show_id',$info['show_id']);
		$smarty->assign('show_icon',$info['show_icon']);
		$smarty->assign('show_name',$info['show_name']);
		$smarty->assign('show_size',$info['show_size']);
		$smarty->assign('show_description',$info['show_description']);
		$smarty->assign('show_created',$info['show_created']);
		$smarty->assign('show_creator',$info['show_creator']);
		$smarty->assign('show_author',$info['show_author']);
		$smarty->assign('show_dl',$info['show_dl']);
		$smarty->assign('max_desc',$info['max_desc']);
		$smarty->assign('fgal_type',$info['type']);
		$smarty->assign('show_lockedby',$info['show_lockedby']);
		$smarty->assign('show_modified',$info['show_modified']);
	    $smarty->assign_by_ref('maxRows',$info["maxRows"]);
	    $smarty->assign_by_ref('public',$info["public"]);
		$smarty->assign_by_ref('lockable', $info['lockable']);
		$smarty->assign_by_ref('archives', $info['archives']);
	    $smarty->assign_by_ref('visible',$info["visible"]);
		$smarty->assign_by_ref('parentId',$info['parentId']);
		$smarty->assign_by_ref('creator',$info['user']);
		if ($info['sort_mode']	&& preg_match('/(.*)_(asc|desc)/', $info['sort_mode'], $matches)) {
			$smarty->assign('sortorder',$matches[1]);
			$smarty->assign('sortdirection', $matches[2]);
		} else {
			$smarty->assign('sortorder', 'created');
			$smarty->assign('sortdirection', 'desc');
		}
		if (!empty($info['subgal_conf'])) {
			// Get gallery specific listing fields
			list($fgal_list_id, $fgal_list_name, $fgal_list_description, $fgal_list_type, $fgal_list_created, $fgal_list_lastmodif, $fgal_list_user, $fgal_list_files, $fgal_list_hits, $fgal_list_parent) = split(':',$info['subgal_conf']);
		}
	  }
	} elseif (!empty($_REQUEST['dup_mode'])) {
		$smarty->assign('dup_mode','y');
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
	  $smarty->assign('show_name',isset($_REQUEST['show_name'])?$_REQUEST['show_name']:'a');
	  $smarty->assign('show_size',isset($_REQUEST['show_size'])?'y':'n');
	  $smarty->assign('show_description',isset($_REQUEST['show_description'])?'y':'n');
	  $smarty->assign('show_created',isset($_REQUEST['show_created'])?'y':'n');
	  $smarty->assign('show_creator',isset($_REQUEST['show_creator'])?'y':'n');
	  $smarty->assign('show_author',isset($_REQUEST['show_author'])?'y':'n');
	  $smarty->assign('show_dl',isset($_REQUEST['show_dl'])?'y':'n');
	  $smarty->assign('max_desc',($_REQUEST['max_desc']));
	  $smarty->assign('fgal_type',isset($_REQUEST['type'])? $_REQUEST['type']: '');
	  $smarty->assign('show_lockedby',isset($_REQUEST['show_lockedby'])?'y':'n');
	  $smarty->assign('show_modified',isset($_REQUEST['show_modified'])?'y':'n');
	  $smarty->assign_by_ref('maxRows',$_REQUEST["maxRows"]);
	  $smarty->assign_by_ref('rowImages',$_REQUEST["rowImages"]);
	  $smarty->assign_by_ref('thumbSizeX',$_REQUEST["thumbSizeX"]);
	  $smarty->assign_by_ref('thumbSizeY',$_REQUEST["thumbSizeY"]);
	  $smarty->assign_by_ref('parentId',$_REQUEST['parentId']);
	  $smarty->assign_by_ref('creator',$_REQUEST['creator']);
	  if(isset($_REQUEST["visible"]) && $_REQUEST["visible"]=="on") {
	    $smarty->assign('visible','y');
	    $visible ='y';
	  } else {
	    $visible ='n';
	  }
	  $smarty->assign('visible',$visible);
	  if(isset($_REQUEST["public"]) && $_REQUEST["public"]=="on") {
	    $smarty->assign('public','y');
	    $public ='y';
	  } else {
	    $public ='n';
	  }
	  $smarty->assign('public',$public);
	  if(isset($_REQUEST['lockable']) && $_REQUEST['lockable'] == 'on') {
	    $smarty->assign('lockable','y');
	    $lockable ='y';
	  } else {
	    $lockable ='n';
	  }
	  $smarty->assign('lockable', $lockable);
	  $_REQUEST['archives']=isset($_REQUEST['archives'])? $_REQUEST['archives']: -1;
	  $_REQUEST['show_id']=isset($_REQUEST['show_id'])?'y':'n';
	  $_REQUEST['show_icon']=isset($_REQUEST['show_icon'])?'y':'n';
	  $_REQUEST['show_description']=isset($_REQUEST['show_description'])?'y':'n';
	  $_REQUEST['show_created']=isset($_REQUEST['show_created'])?'y':'n';
	  $_REQUEST['show_creator']=isset($_REQUEST['show_creator'])?'y':'n';
	  $_REQUEST['show_author']=isset($_REQUEST['show_author'])?'y':'n';
	  $_REQUEST['show_dl']=isset($_REQUEST['show_dl'])?'y':'n';
	  $_REQUEST['show_size']=isset($_REQUEST['show_size'])?'y':'n';
	  $_REQUEST['show_name']=isset($_REQUEST['show_name'])?$_REQUEST['show_name']:'a';
	  $_REQUEST['user'] = isset($_REQUEST['user'])?$_REQUEST['user']:(isset($info['user'])?$info['user']:$user);
	  $_REQUEST['show_lockedby']=isset($_REQUEST['show_lockedby'])?'y':'n';
	  $_REQUEST['show_modified']=isset($_REQUEST['show_modified'])?'y':'n';
	  $_REQUEST['sortorder']=isset($_REQUEST['sortorder'])?$_REQUEST['sortorder']:'created';
	  $_REQUEST['sortdirection']=isset($_REQUEST['sortdirection']) && $_REQUEST['sortdirection'] == 'asc'? 'asc':'desc';
	  
	  $_REQUEST['subgal_conf']=implode(':', array(isset($_REQUEST['fgal_list_id'])?'y':'n', isset($_REQUEST['fgal_list_name'])?'y':'n', isset($_REQUEST['fgal_list_description'])?'y':'n', isset($_REQUEST['fgal_list_type'])?'y':'n', isset($_REQUEST['fgal_list_created'])?'y':'n', isset($_REQUEST['fgal_list_lastmodif'])?'y':'n', isset($_REQUEST['fgal_list_user'])?'y':'n', isset($_REQUEST['fgal_list_files'])?'y':'n', isset($_REQUEST['fgal_list_hits'])?'y':'n', isset($_REQUEST['fgal_list_parent'])?'y':'n'));
	  $fgid = $filegallib->replace_file_gallery($_REQUEST["galleryId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST['user'], $_REQUEST["maxRows"], $public, $visible,$_REQUEST['show_id'],$_REQUEST['show_icon'],$_REQUEST['show_name'],$_REQUEST['show_size'],$_REQUEST['show_description'],$_REQUEST['show_created'],$_REQUEST['show_dl'],$_REQUEST['max_desc'],$_REQUEST['fgal_type'], $_REQUEST['parentId'], $lockable, $_REQUEST['show_lockedby'], $_REQUEST['archives'], $_REQUEST['sortorder'].'_'.$_REQUEST['sortdirection'], $_REQUEST['show_modified'], $_REQUEST['show_creator'], $_REQUEST['show_author'], $_REQUEST['subgal_conf']);
	  
		if ($prefs['feature_categories'] == 'y') {
			$cat_type='file gallery';
			$cat_objid = $fgid;
			$cat_desc = substr($_REQUEST["description"],0,$_REQUEST['max_desc']);
			$cat_name = $_REQUEST["name"];
			$cat_href="tiki-list_file_gallery.php?galleryId=".$cat_objid;
			include_once("categorize.php");
			$categlib->build_cache();
		}
	  if (isset($_REQUEST['viewitem'])) {
		  header("location: tiki-list_file_gallery.php?galleryId=$fgid");
		  die;
	  }
	  $smarty->assign('edit_mode','n');
	}
	
	if (!empty($_REQUEST['duplicate']) && !empty($_REQUEST['name']) && !empty($_REQUEST['galleryId'])) {
		$newGalleryId = $filegallib->duplicate_file_gallery($_REQUEST['galleryId'], $_REQUEST['name'], isset($_REQUEST['description'])?$_REQUEST['description']: '' );
	if (isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y') {
		global $categlib; include_once('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('file gallery', $_REQUEST['galleryId']);
		$catObjectId = $categlib->add_categorized_object('file gallery', $newGalleryId, isset($_REQUEST['description'])?$_REQUEST['description']: '', $_REQUEST['name'], "tiki-list_file_gallery.php?galleryId=$newGalleryId");
		foreach($cats as $cat) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if (isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on') {
		global $userlib; include_once('lib/userslib.php');
		$userlib->copy_object_permissions($_REQUEST['galleryId'], $newGalleryId, 'file gallery');
	}  
	$_REQUEST['galleryId'] = $newGalleryId;
	}

	if(!empty($_REQUEST["removegal"])) {
		if (!($info = $filegallib->get_file_gallery_info($_REQUEST['removegal']))) {
			$smarty->assign('msg',tra('Incorrect param'));
			$smarty->display("error.tpl");
			die;
		}
		if ($tiki_p_admin_file_galleries != 'y' && (!$user || $info["user"] != $user)) {
	       $smarty->assign('msg',tra("Permission denied you cannot remove this gallery"));
	       $smarty->display("error.tpl");
	       die;
		}
		$area = 'delfilegal';
		if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$filegallib->remove_file_gallery($_REQUEST["removegal"], $_REQUEST['galleryId']);
		} else {
		  key_get($area, tra('Remove file gallery: ').' '.$info['name']);
		}
	}
	if (isset($_REQUEST['batchaction']) &&  $_REQUEST['batchaction'] == 'delsel_x' && isset($_REQUEST['checked'])) {
		check_ticket('fgal');
		if ($tiki_p_admin_file_galleries != 'y') {
			$smarty->assign('msg',tra("Permission denied you cannot remove this gallery"));
			$smarty->display("error.tpl");
			die;  
		}
		foreach($_REQUEST['checked'] as $id) {
			$filegallib->remove_file_gallery($id);
		}
	}
	if (isset($_REQUEST['batchaction']) && $_REQUEST['batchaction'] != 'delsel_x' && isset($_REQUEST['checked']) && isset($_REQUEST['groups'])) {
		check_ticket('fgal');
		if ($tiki_p_admin_file_galleries != 'y' && $tiki_p_assign_perm_file_gallery != 'y') {
			$smarty->assign('msg',tra("Permission denied you cannot assign permissions for this object"));
			$smarty->display("error.tpl");
			die;  
		}
		$perms = $userlib->get_permissions(0, -1, 'permName_asc', '', 'file galleries');
		foreach ($perms['data'] as $perm) {
			if ($_REQUEST['batchaction'] == 'assign_'.$perm['permName']) {
				foreach($_REQUEST['checked'] as $id) {
					foreach ($_REQUEST['groups'] as $group)
						$userlib->assign_object_permission($group, $id, 'file gallery', $perm['permName']);
				}
			}
		}
	}
	if(!isset($_REQUEST["sort_mode"])) {
	  $sort_mode = !empty($prefs['fgal_sort_mode'])? $prefs['fgal_sort_mode']: 'created_desc'; 
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
	    if($userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_assign_perm_file_gallery')) {
	      $galleries["data"][$i]["individual_tiki_p_assign_perm_file_gallery"]='y';
	    } else {
	      $galleries["data"][$i]["individual_tiki_p_assign_perm_file_gallery"]='n';
	    }
	    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$galleries["data"][$i]["galleryId"],'file gallery','tiki_p_admin_file_galleries')) {
	      $galleries["data"][$i]["individual_tiki_p_create_file_galleries"]='y';
	      $galleries["data"][$i]["individual_tiki_p_download_files"]='y';
	      $galleries["data"][$i]["individual_tiki_p_upload_files"]='y';
	      $galleries["data"][$i]["individual_tiki_p_view_file_gallery"]='y';
	      $galleries["data"][$i]["individual_tiki_p_assign_perm_file_gallery"]='y';
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

	if ($offset == 0 && ($maxRecords == -1 || $galleries['cant'] <= $maxRecords)) {
		$smarty->assign_by_ref('allGalleries', $galleries['data']);
	} else {
		$allGalleries = $filegallib->list_file_galleries(0, -1,'name_asc', 'admin');
		$smarty->assign_by_ref('allGalleries', $allGalleries['data']);
	}

	$smarty->assign_by_ref('galleries',$galleries["data"]);

	if ($tiki_p_admin_file_galleries == 'y') {
		$users = $tikilib->list_users(0, -1, 'login_asc', '', false);
		$smarty->assign_by_ref('users', $users['data']);
	}
	if ($tiki_p_admin_file_galleries == 'y' || $tiki_p_assign_perm_file_gallery == 'y') {
		if (!isset($perms)) {
			$perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
		}
		$smarty->assign_by_ref('perms', $perms['data']);
		$groups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		$smarty->assign_by_ref('groups', $groups['data']);
	}
	$options_sortorder = array(tra('Creation Date')=>'created', tra('Name')=>'name', tra('Filename')=>'filename', tra('Size')=>'filesize', tra('Owner') => 'user',tra('Downloads') => 'downloads', tra('ID') => 'fileId');
	$smarty->assign_by_ref('options_sortorder', $options_sortorder);
	
	$cat_type='file gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include_once("categorize_list.php");
	
	$section='file_galleries';
	include_once('tiki-section_options.php');

// Assign listing fields in smarty
$smarty->assign('fgal_list_id', $fgal_list_id);
$smarty->assign('fgal_list_name', $fgal_list_name);
$smarty->assign('fgal_list_description', $fgal_list_description);
$smarty->assign('fgal_list_type', $fgal_list_type);
$smarty->assign('fgal_list_created', $fgal_list_created);
$smarty->assign('fgal_list_lastmodif', $fgal_list_lastmodif);
$smarty->assign('fgal_list_user', $fgal_list_user);
$smarty->assign('fgal_list_files', $fgal_list_files);
$smarty->assign('fgal_list_hits', $fgal_list_hits);
$smarty->assign('fgal_list_parent', $fgal_list_parent);
	
ask_ticket('fgal');
if (isset($_GET['filegals_manager'])) {
  $smarty->assign('filegals_manager','y');
  $smarty->assign('mid','tiki-file_galleries.tpl');
  $smarty->display("tiki_full.tpl");
} else {
	// Display the template
	$smarty->assign('mid','tiki-file_galleries.tpl');
	$smarty->display("tiki.tpl");
}
?>
