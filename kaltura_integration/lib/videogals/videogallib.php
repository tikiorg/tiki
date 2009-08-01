<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/videogals/videogallib.php,v 1.97.2.4 2008-03-06 19:45:42 sampaioprimo Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ("includes.php");

class VideoGalsLib {

	function edit_video($id, $name, $description, $tags) {
		global $prefs;
		$entry = new KalturaEntry();
		$entry->name= $name;
		$entry->description = $description;
		$entry->tags = $tags;
		//print $tags;
		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = $user;
		$kaltura_client = new KalturaClient($kaltura_conf);
		$kres = $kaltura_client->start($kuser, $kaltura_conf->secret,'',"edit:*",'');
		$kaltura_client->setKS($kres["result"]["ks"]);
		$kres= $kaltura_client->updateEntry($kuser,$id,$entry);
		
		//print_r($kres);
		return true;
	}

	function insert_video($galleryId, $entryId, $user) {
		global $prefs;
		$query = "insert into `tiki_videos`(`galleryId`,`entryId`,`creator`,`last_user`) values (?,?,?,?)";
		$result = $this->query($query,array((int)$galleryId,$entryId,$user,$user));
		$res = $this->get_video_from_entry($entryId);
		return $res;
	}

	function delete_video($id) {
		global $prefs;
		global $user;
		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = $user;
		$kaltura_client = new KalturaClient($kaltura_conf);
		$kres = $kaltura_client->startAdminSession($kuser, $kaltura_conf->adminSecret,'1','edit:*',86400);
		$kaltura_client->setKS($kres["result"]["ks"]);
		
		for($i=0; $i < count($id); $i++) {
			$res= $kaltura_client->deleteEntry($kuser,$id[$i]);
		}
		
	}

    function get_gallery($id) {
	$query = "select * from `tiki_galleries_video` where `galleryId`=?";
	$result = $this->query($query,array((int) $id));
	$res = $result->fetchRow();
	return $res;
    }

	function get_gallery_owner($galleryId) {
		$query = "select `user` from `tiki_galleries_video` where `galleryId`=?";

		$user = $this->getOne($query,array((int)$galleryId));
		return $user;
	}

	function get_gallery_from_video($videoid) {
		$query = "select `galleryId` from `tiki_videos` where `videoId`=?";

		$galid = $this->getOne($query,array((int)$videoid));
		return $galid;
	}

	function get_entry_from_video($videoid) {
		$query = "select `entryId` from `tiki_videos` where `videoId`=?";
		$entid = $this->getOne($query,array((int)$videoid));
		return $entid;
	}
	
	function get_video_from_entry($entryid) {
		$query = "select `videoId` from `tiki_videos` where `entryId`=?";
		$vidid = $this->getOne($query,array($entryid));
		return $vidid;
	}
	
	function move_video($vidId, $galId) {
		$query = "update `tiki_videos` set `galleryId`=? where `videoId`=?";

		$result = $this->query($query,array((int)$galId,(int)$vidId));
		return true;
	}

	function get_video_info($id,$client) {

		$kaltura_conf = kaltura_init_config();
		$kuser = new KalturaSessionUser();
		$kuser->userId = $user;
		$kaltura_client = new KalturaClient($kaltura_conf);
		$kres = $kaltura_client->startSession($kuser, $kaltura_conf->secret,false,"edit:*");
		$kaltura_client->setKS($kres["result"]["ks"]);
		$kres= $client->getEntry ( $kuser , $id,1);
		return $kres['result']['entry'];
	}


}
global $dbTiki;
global $videogallib;
$videogallib = new VideoGalsLib();

?>





