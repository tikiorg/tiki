<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-batch_upload.php,v 1.2 2005-03-12 16:48:58 mose Exp $

require_once ('tiki-setup.php');
include_once ('lib/imagegals/imagegallib.php');

if ($feature_galleries != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");
	$smarty->display("error.tpl");
	die;
}

if ($feature_gal_batch != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_gal_batch");
	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_batch_upload_image_dir != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot use the batch directory loading"));
	$smarty->display("error.tpl");
	die;
}

// scan directory 
//$tiki_batch_images_dir = "/home/tom/images/";
if (!isset($gal_batch_dir) or !is_dir($gal_batch_dir)) {
	$msg = tra("Incorrect directory chosen for batch upload of images.")."<br />"; 
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ").'<a href="tiki-admin.php?page=gal">'.tra('Image Galleries Admin Panel').'</a>.';
	} else {
		$msg.= tra("Please contact the website administrator.");
	}
	$smarty->assign('msg', $msg); 
	$smarty->display("error.tpl");
	die;
} else {
	$imgdir = $gal_batch_dir;
}

$a_img = $imgstring = $feedback = array();
$allowed_types = array('.png','.jpg','.gif'); // list of filetypes you want to show
$dimg = opendir($imgdir);
while($imgfile = readdir($dimg)) {
	if (in_array(strtolower(substr($imgfile,-4)),$allowed_types)) {
		$a_img[] = $imgfile;
	}
}
sort($a_img);
$totimg = count($a_img); // total image number
$totalsize = 0;

for($x=0; $x < $totimg; $x++) {
	$size = getimagesize($imgdir.'/'.$a_img[$x]);
	$filesize = filesize($imgdir.'/'.$a_img[$x]);	
	$halfwidth = ceil($size[0]/2);
	$halfheight = ceil($size[1]/2);
	$imgstring[$x][0]= $a_img[$x];
	$imgstring[$x][1]= $size[0];
	$imgstring[$x][2]= $size[1];
	$imgstring[$x][3]= $filesize;
	$imgstring[$x][4]= strtolower(substr($a_img[$x],-3));
	$totalsize+= $filesize;
}
	
$smarty->assign('totimg',$totimg);
$smarty->assign('totalsize',$totalsize);
$smarty->assign('imgstring',$imgstring);
$smarty->assign('imgdir',$imgdir);

if (isset($_REQUEST["batch_upload"]) and isset($_REQUEST['imgs']) and is_array($_REQUEST['imgs'])) {
	$imgs = $_REQUEST['imgs'];
	$totimgs = count($imgs);
	for($x=0; $x < $totimgs; $x++) {
		$filepath = $imgdir.'/'.$imgs[$x];
		$size = getimagesize($filepath);
		$filesize = filesize($filepath);
		$type = strtolower(substr($a_img[$x],-3));
		$data = '';
		$fp = fopen($filepath,'r');
		while (!feof($fp)) {
			$data .= fread($fp,1024);
		}
		fclose($fp);
		$imageId = $imagegallib->insert_image($_REQUEST["galleryId"], $imgs[$x],'', $imgs[$x], $type, $data, $filesize, $size[0], $size[1], $user, '', '');
		if (!$imageId) {
			$feedback[] = "!!!". sprintf(tra('Image %s upload failed.'),$imgs[$x]);
		} else {
			$feedback[] = sprintf(tra('Image %s uploaded successfully.'),$imgs[$x]);
			if (@ unlink($filepath)) {
				$feedback[] = sprintf(tra('Image %s removed from Batch directory.'),$imgs[$x]);
			} else {
				$feedback[] = "!!! ". sprintf(tra('Impossible to remove image %s from Batch directory.'),$imgs[$x]);
			}
		}
	}
}
$smarty->assign('feedback', $feedback);

// Get the list of galleries to display the select box in the template
if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
} else {
	$smarty->assign('galleryId', '');
}

if ($tiki_p_admin_galleries != 'y') {
	$galleries = $imagegallib->list_visible_galleries(0, -1, 'lastModif_desc', $user, '');
} else {
	$galleries = $imagegallib->list_galleries(0, -1, 'lastModif_desc', $user, '');
}

$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'image gallery')) {
		$galleries["data"][$i]["individual"] = 'y';
		$galleries["data"][$i]["individual_tiki_p_batch_upload_image_dir"] = 'n';
		if ($tiki_p_admin == 'y' 
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_batch_upload_image_dir')
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_admin_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_batch_upload_image_dir"] = 'y';
		}
	} else {
		$galleries["data"][$i]["individual"] = 'n';
	}
}

$smarty->assign_by_ref('galleries', $galleries["data"]);

$section = 'galleries';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-batch_upload.tpl');
$smarty->display("tiki.tpl");

?>
