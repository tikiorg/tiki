<?php
/** 
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
* 
* WebDAV access to Tiki resources
* 
*  */

require_once "lib/pear/HTTP/WebDAV/Server.php";
require_once ('tiki-setup.php');

class TikiDAV_Server extends HTTP_WebDAV_Server {

	function TikiDAV_Server() {
		HTTP_WebDAV_Server :: HTTP_WebDAV_Server();
		$this->http_auth_realm = "TikiWiki WebDav";
	}

	/**
	 * Serve a webdav request
	 *
	 * @access public
	 * @param  string  
	 */
	function ServeRequest($base = false) {
		parent :: ServeRequest();
	}

	/**
	 * User authentication
	 *
	 * @access private
	 * @param  string  HTTP Authentication type (Basic, Digest, ...)
	 * @param  string  Username
	 * @param  string  Password
	 * @return bool    true on successful authentication
	 */
	function check_auth($type, $user, $pass) {
		$this->logger("check_auth ".$user." ".$type." ".$_REQUEST['REQUEST_METHOD']);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();
		return $webdavlib->check_auth($user, $pass);
			
			//$_SESSION["webdavSession"] = "admin";
			//return true;
	}

	/**
	 * @access private
	 * @param array Tiki object information data structure 
	 * @return array WebDAV object information data structure
	 */
	function toDAVMetaInfo($tikiMetaInfo) {
		//$this->logger("DAVMETA:".print_r($tikiMetaInfo,true));
		$info = array ();
		$info["path"] = $tikiMetaInfo["path"];
		$info["name"] = $tikiMetaInfo["name"];
		$info["new"] = $tikiMetaInfo["new"];
		$info["mimetype"] = $tikiMetaInfo["mimetype"];
		$info["props"] = array ();
		$info["props"][] = $this->mkprop("displayname", strtoupper($tikiMetaInfo['displayname']));
		$info["props"][] = $this->mkprop("creationdate", $tikiMetaInfo['creationdate']);
		$info["props"][] = $this->mkprop("getlastmodified", $tikiMetaInfo['getlastmodified']);

		$info["props"][] = $this->mkprop("resourcetype", $tikiMetaInfo['resourcetype']);
		$info["props"][] = $this->mkprop("getcontenttype", $tikiMetaInfo['getcontenttype']);
		$info["props"][] = $this->mkprop("getcontentlength", $tikiMetaInfo['getcontentlength']);
		$info["props"][] = $this->mkprop("TIKI:", "tikiType", $tikiMetaInfo["tikiType"]);
		$info["props"][] = $this->mkprop("TIKI:", "parentId", $tikiMetaInfo["parentId"]);
		$info["props"][] = $this->mkprop("TIKI:", "id", $tikiMetaInfo["id"]);
		$info["props"][] = $this->mkprop("TIKI:", "description", $tikiMetaInfo["description"]);
		return $info;
	}
 
	/**
	 * PROPFIND method handler
	 *
	 * @param  array  general parameter passing array
	 * @param  array  return array for file properties
	 * @return bool   true on success
	 */
	function PROPFIND(& $options, & $files) {
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		// prepare property array
		$files["files"] = array ();

		if ($options["path"] != "/" && substr($options["path"], -1) == "/") {
			$options["path"] = substr($options["path"], 0, -1);
		}

		$this->logger("PROPFIND:".$options["path"]);

		//$objMI = $webdavlib->getObjectMetaInfo($options["path"],$_SESSION["webdavSession"]);
		$objMI = $webdavlib->propfind($options["path"],$_SESSION["webdavSession"]);

		if (isset ($objMI) && $objMI!="") {
			$files["files"][] = $this->toDAVMetaInfo($objMI);
		} else {
			return "404 Not found";
		}

		if ($objMI["resourcetype"] == "collection" && !empty ($options["depth"])) { // get collection child objects

			$tikiChilds = $webdavlib->getCollectionChilds($options["path"],$_SESSION["webdavSession"],$_SESSION["webdavSession"]);

			foreach ($tikiChilds as $child) {
				$files["files"][] = $this->toDAVMetaInfo($child);
			}
		}

		return true;
	}

	/**
	 * GET method handler
	 * 
	 * @param  array  parameter passing array
	 * @return bool   true on success
	 */
	function GET(& $options) {
		$this->logger("GET:".$options["path"]);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();
		// prepare property array
		$files["files"] = array ();

		if ($options["path"] != "/" && substr($options["path"], -1) == "/") {
			$options["path"] = substr($options["path"], 0, -1);
		}

		$version = "current";
		$rendition = "default";
		if (isset ($_REQUEST["version"]))
			$version = $_REQUEST["version"];
		if (isset ($_REQUEST["rendition"])){
			$rendition = $_REQUEST["rendition"];
			$this->logger("RENDITION:".$_REQUEST["rendition"]);
		}		
		$objMI = $webdavlib->getObjectData($options["path"], $rendition,$_SESSION["webdavSession"]);

		if (!isset ($objMI) || $objMI == FALSE) {
			$this->logger("GET ERROR 404:".$options["path"]);
			return "404 Not found";
		}

		$options['mimetype'] = $objMI['mimetype'];
		$options['mtime'] = $objMI["getlastmodified"];
		$options['size'] = strlen($objMI["data"]);
		$options['data'] = $objMI["data"];
		return true;
	}

	/**
	 * PUT method handler
	 * 
	 * @param  array  parameter passing array
	 * @return bool   true on success
	 */
	function PUT(& $options) {
	
		$this->logger("PUT:".$options["path"]);

		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		// prepare property array
		$files["files"] = array ();

		if ($options["path"] != "/" && substr($options["path"], -1) == "/") {
			$options["path"] = substr($options["path"], 0, -1);
		}
/*
		$objMI = $webdavlib->getObjectData($options["path"]);

		if (isset ($objMI) && $objMI["tikiType"] == "category") { //is a collection
			return "409 Conflict";
		}
*/
		$objMI = $webdavlib->put($options["path"], $options["stream"],$_SESSION["webdavSession"]);
		if ($objMI == FALSE) { //cant edit
			$this->logger("PUT ERROR 409:".$options["path"]);
			return "409 Conflict";
		}
		$options["new"] = $objMI["new"];

		return ($options["new"] ? "201 Created" : "204 No Content");
	}

	/**
	 * MKCOL method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function MKCOL($options) {
		if (!isset($_SESSION["webdavSession"]) || $_SESSION["webdavSession"] == "")
			return false;
		
		$this->logger("MKCOL:".$options["path"]);

		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		$newCollection = $webdavlib->mkcol($options["path"],$_SESSION["webdavSession"]);

		if (isset ($newCollection) && $newCollection!=FALSE) {
			return ("201 Created");
		} else {
			$this->logger("MKCOL ERROR 409:".$options["path"]);
			return "409 Conflict";
		}
	}

	/**
	 * DELETE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function delete($options) {
		if (!isset($_SESSION["webdavSession"]) || $_SESSION["webdavSession"]=="")
			return false;
		
		$this->logger("DELETE:".$options["path"]);

		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		if (!$webdavlib->delete($options["path"],$_SESSION["webdavSession"])) {
			return "404 Not found";
		}

		return "204 No Content";
	}

	/**
	 * MOVE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function move($options) {
		if (!isset($_SESSION["webdavSession"]) || $_SESSION["webdavSession"] == "")
			return false;
		
		$this->logger("MOVE:".$options["path"]);

		if (!empty ($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
			return "415 Unsupported media type";
		}

		// no copying to different WebDAV Servers yet
		if (isset ($options["dest_url"])) {
			return "502 bad gateway";
		}

		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		$movedObj = $webdavlib->move($options["path"], $options["dest"],$_SESSION["webdavSession"],TRUE);
		if (isset ($movedObj) && $movedObj) {
			return "201 Created";
		} else {
			return "404 Not found";
		}
	}

	/**
	 * COPY method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function copy($options, $del = false) {		
		if (!isset($_SESSION["webdavSession"]) || $_SESSION["webdavSession"] == "")
			return false;
		
		$this->logger("COPY:".$options["path"]);

		if (!empty ($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
			return "415 Unsupported media type";
		}

		// no copying to different WebDAV Servers yet
		if (isset ($options["dest_url"])) {
			return "502 bad gateway";
		}

		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		$movedObj = $webdavlib->copy($options["path"], $options["dest"],$_SESSION["webdavSession"]);
		if (isset ($movedObj) && $movedObj) {
			return "201 Created";
		} else {
			return "404 Not found";
		}
	}


	/**
	 * PROPPATCH method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function proppatch(& $options) {
		$this->logger("PROPPATCH:".$options["path"]);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();
		return $webdavlib->proppatch($options["path"], $options["props"]);
	}

	/**
	 * LOCK method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function lock(& $options) {
		$this->logger("LOCK:".$options["path"]);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		if (isset ($options["update"])) { // Lock Update
			$options["timeout"] = time() + 600; // default 10 minutes
			return $webdavlib->lockUpdate($options["update"], $options["path"], $options["timeout"]);

		} else { // create new lock

			$options["timeout"] = time() + 600; // default 10 minutes							
			return $webdavlib->lock($options["locktoken"], $options["path"], $_SESSION["webdavSession"], $options["timeout"], $options["scope"]);
		}
	}

	/**
	 * UNLOCK method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function unlock(& $options) {
		$this->logger("UNLOCK:".$options["path"]);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		return $webdavlib->unlock($options['path'], $options['token']) ? "200 OK" : "409 Conflict";
	}

	/**
	 * checkLock() helper
	 *
	 * @param  string resource path to check for locks
	 * @return bool   true on success
	 */
	function checkLock($path) {
		log("CHECKLOCK:".$path);
		require_once "./lib/tikidav/repositorylib.php";
		$webdavlib = new RepositoryLib();

		return $webdavlib->checkLock($path);
	}

	function logger($msg) {
		$gestor = fopen("./temp/log.txt", "a");
		fwrite($gestor, "[".date("d/m/y H:i")."] [".$_REQUEST['REMOTE_ADDR']."] [".$msg."] [".$_REQUEST['HTTP_USER_AGENT']."]\n");
	}
}
?>