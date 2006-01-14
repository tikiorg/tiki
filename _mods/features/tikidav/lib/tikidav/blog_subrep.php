<?php
/** 
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
* 
* blog repository for TikiDav
* 
*  */
require_once("tiki-setup.php");
global $dbTiki;
require_once ('lib/blogs/bloglib.php');

class blogSubrep{

function getObjectMetaInfo($blogObjMI,$relativepath,$user){
	global $dbTiki;
	
	if (!strrchr($relativepath,"~")) //if the name of the post dont have the id, return
		return;
	$objId = substr($relativepath, 1, -strlen(stristr($relativepath, "~")));
	$bloglib2 = new BlogLib($dbTiki);
	$data = $bloglib2->get_post($objId);
	if (!isset($data))
		return;
	else
		return $this->createRepositoryObject($data,$blogObjMI["id"],$blogObjMI["path"]);
	
}

function getCollectionChilds($blogObjMI,$path, $user) {
	global $dbTiki;
	
    $bloglib2 = new BlogLib($dbTiki);
	$listpages = $bloglib2->list_blog_posts($blogObjMI["id"], 0, -1, "created_desc", '', '');
	$childs = array();
	foreach ($listpages["data"] as $key => $blogpost) {
		$childs[] = $this->createRepositoryObject($blogpost,$blogObjMI["id"],$path);
	}
	return $childs;
}

function put($path,$collection,$objMeta,$data,$user,$rendition,$putfiles){
	global $dbTiki;
	$bloglib2 = new BlogLib($dbTiki);
	
	if (isset($objMeta)) {
	//TODO: Read the blog post for the trackback
		$bloglib2->update_post($objMeta["id"], $collection["id"], $data, $user, $objMeta["description"], "trackback");
	} else {
		$postid = $bloglib2->blog_post($collection["id"], $data, $user, substr(strrchr($path,"/"),1), "");
	}
	return TRUE;
}

function createRepositoryObject($blogpost,$parentId,$path) {
		$info = array ();
		$info["id"] = $blogpost["postId"];
		$info["uid"] = "blogpost_".$blogpost["postId"];
		$info["parentId"] = $parentId;
		$info["path"] = $path."/".$blogpost["postId"]."~".$blogpost["title"];
		$info["name"] = $blogpost["postId"]."~".$blogpost["title"];

		$info['description'] = $blogpost["title"];
		$info["tikiType"] = "blog post";
		$info["subrepType"] = "blog";
		$info["displayname"] = "/".$blogpost["postId"]."~".$blogpost["title"];
		$info["creationdate"] = $blogpost["created"];
		$info["getlastmodified"] = $blogpost["created"];
		
		$info["resourcetype"] = "";
		$info["getcontenttype"] = "text/plain";
		$info["mimetype"] = "text/plain; charset=\"utf-8\"";
		
		$info["getcontentlength"] = strlen($blogpost["data"]);
		$info["new"] = FALSE;
		$info["data"] = $blogpost["data"];
		$info["mimetype"] = "text/plain; charset=\"utf-8\"";
		return $info;
	}


}
?>
