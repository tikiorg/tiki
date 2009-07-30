<?php
/**
 * wslib.php - TikiWiki CMS/GroupWare
 *
 * This library enables the basic management of workspaces (WS)
 * 
 * @package	lib
 * @author	Benjamin Palacios Gonzalo (mangapower) <mangapowerx@gmail.com>
 * @author	Aldo Borrero Gonzalez (axold) <axold07@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//Controlling Access
require_once 'tiki-setup.php';
//$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//Rest of Imports
include_once 'lib/categories/categlib.php';

/**
 * wslib - The Workspaces Library for TikiWiki
 * TODO: Refine documentation!!!
 *
 * @category	TikiWiki
 * @package	lib/workspaces
 * @version	$Id
 */
class wslib extends CategLib 
{
    /** Stores the $prefs['ws_container'] for avoid to check it in every function */
    private $ws_container;

    /** Stores this objectype, this is WS */
    private $objectType;

    /** Constructor, give the dbtiki to its parent, this is Categlib */
    public function __construct()
    {
	global $prefs;

	$this->ws_container = (int) $prefs['ws_container'];
	$this->objectType = 'ws';
    }

    /** Initialize the Workspaces in TikiWiki setting a container in the category table and return its ID
     *
     * @return The ws_container ID if it wasn't set before the ws_container, if not, null
     */
    public function init_ws()
    {
	if (!$this->ws_container)
	{
	    global $prefs, $tikilib;
	    $id = parent::add_category(0, '', 'Workspaces Container');
	    $tikilib->set_preference('ws_container', $id);
	    $this->ws_container = (int) $prefs['ws_container'];
	    return $id;
	}
    }

    /** Get the ws_container stored in $prefs. This function is used to avoid certain calls to the $prefs array.
     *
     * @return The ws container id stored in the var $prefs
     */
    public function get_ws_container()
    {
	return $this->ws_container;
    }
    
    /** Create a new WS with one group inside it with the associated perm 'tiki_p_ws_view'.
     *
     * @param $name Name of the Workspace
     * @param $parentWS Name of the ParentWS, if ParentWS is null, its default value will be ws_container
     * @param $groups An associative array of groups in the form array("groupName" => (string) name, "groupDescription" => (string) description,
     * "noCreateNewGroup" => boolean, "additionalPerms" => array("additionalperm1", "additionalperm2", ...))
     * @param $additionalPerms Associative array for giving more perms than the default perm 'tiki_p_ws_view'
     * @return The ID of the WS
     */
    public function create_ws ($name, $groups, $parentWS = null, $description = '')
    {
    	if (!$parentWS)	$parentWS = 0;
    	
	$query = "insert into `tiki_categories`(`name`,`description`,`parentId`,`hits`,`rootCategId`) values(?,?,?,?,?)";
	$this->query($query, array($name, $description, (int) $parentWS, 0, $this->ws_container));
	
	$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=? and `rootCategId`=?";
	$ws_id = $this->getOne($query, array($name, (int) $parentWS, $this->ws_container));
	
	foreach ($groups as $group)
	{
		$groupName = $group["groupName"];
		$groupDescription = $group["groupDescription"];
		$noCreateNewGroup = $group["noCreateNewGroup"];
		$additionalPerms = $group["additionalPerms"];
	
		if ($noCreateNewGroup)
		{
		    $this->set_permissions_for_group_in_ws ($ws_id, $groupName, array('tiki_p_ws_view'));
		    if ($additionalPerms != null)
			$this->set_permissions_for_group_in_ws($ws_id, $groupName, $additionalPerms);
		}
		else
		    $this->add_ws_group ($ws_id, $name, $groupName, $groupDescription, $additionalPerms);
	}
		
	return $ws_id;
    }

    /** Add new group to a WS
     *
     * @param $id_ws The WS id you want to add the group
     * @param $wsName The name of the WS, it can be null
     * @param $nameGroup The name of the group you want to create
     * @param $additionalPerms Associative array for giving more perms than the default perm 'tiki_p_ws_view'
     * @return If the WS was sucesfully created true, if not false.
     */
    public function add_ws_group ($ws_id, $wsName = null, $nameGroup, $description, $additionalPerms = null) 
    {
	global $userlib; require_once 'lib/userslib.php';

	if (!$wsName) $wsName = $this->get_ws_name($ws_id);

	$groupName = $nameGroup;//$this->generate_ws_group_name ($id_ws, $wsName, $nameGroup); //With this you can create two groups with same name in different ws

	if ($userlib->add_group($groupName, $description)) 
	{
    	    // It's given the tiki_p_ws_view permission to the selected group in the new ws
	    $this->set_permissions_for_group_in_ws($ws_id,$groupName,array('tiki_p_ws_view'));
	
    	    // It's given additional admin permissions to the group in the new ws
	    if ($additionalPerms != null)
		$this->set_permissions_for_group_in_ws($ws_id,$groupName,$additionalPerms);

	    return true;
	}
	else
	    return false;
    }

    /** Generate a group name specially created for WS. With this we can avoid the problems related to have two groups with the same name
     * NOTE: For now is OK, if the future implementation of groups change, this would change!
     *
     * @param $id_ws The WS id
     * @param $wsname The WS name
     * @param $nameGroup The group name
     * @return A string with this format: $id_ws::$wsName::$nameGroup
     */
    public function generate_ws_group_name ($ws_id, $wsName, $nameGroup)
    {
	return $name = ((string) $ws_id)."::".$wsName."::".$nameGroup;
    }

    /** Parse a group name with the form $ws_id<:>$wsName<:>$nameGroup
     * Allowed characters in $ws_id are 0-9 with a variable length of 1 to 11 digits
     * Allowed characters in $wsName are 0-9, A-Z, a-z, whitespace, -, < and >
     * Allowed characters in $nameGroup are the same as above
     * TODO: Work in progress, needs to be checked if works properly (I'm newbie to regex world :P)
     * Yeah, I know, this exp is far from being perfect, but for testing purposes is OK
     *
     * @param $groupName The name of the group you want to parse
     * @param $groupValues The values given in a reference array when you apply the function
     * @return An array with the values in each position. If the parse was not succesful return false
     */
    public function parse_ws_group_name ($groupName, &$groupValues)
    {
	return preg_match("%\b([\d]{1,11})\b::\b([\w\-<>\s]+)\b::\b([\w\-<>\s]+[^:]{2})\b%", $groupName, $groupValues);
    }
    
    /** Change a WS name and description
     * 
     * @param $ws_id The WS id you want to update
     * @param $wsName The new name for the WS
     * @param $wsDesc The new description for the WS
     * @return true
     */
    public function update_ws_data ($ws_id, $wsName, $wsDesc)
    {
    	$query = "update `tiki_categories` set `name`=?, `description`=? where `categId` = ?";
    	$bindvars = array($wsName, $wsDesc, $ws_id);
    	$this->query($query, $bindvars);
    	return true; 
    }
	
    /** Remove a WS and it childs
     * 
     * @param $ws_id The WS id you want to delete
     * @return true
     */
    public function remove_ws ($ws_id)
    {	
    	// Remove the WS groups
	$listWSGroups = $this->list_groups_that_can_access_in_ws ($ws_id);
	foreach ($listWSGroups as $group)
		$this->remove_group_from_ws ($ws_id,$group["groupName"]);
	
	// Remove the WS objects
	$listWSObjects = $this->list_ws_objects($ws_id);
	foreach ($listWSObjects as $object)
		$this->remove_object_from_ws ($ws_id,$object["objectId"],$object["itemId"],$object["type"]);
    	
    	// Remove perms assigned to the WS
    	$hashWS = md5($this->objectType . strtolower($ws_id));
	$query = "delete from `users_objectpermissions` where `objectType` = ? and `objectId` = ?";
	$this->query($query, array($this->objectType, $hashWS), -1, -1, false);
		
	// Remove WS recursively
	$wsChilds = $this->get_ws_childs ($ws_id);
	foreach ($wsChilds as $child)
		$this->remove_ws($child);

	return parent::remove_category($ws_id);
    }


    /** Remove all WS including the Workspaces container. It's a destructive function, so use with caution
     * 
     * @return True
     */
    public function remove_all_ws ()
    {
    	// First, delete all WS parents
    	$query = "select `categId` from `tiki_categories` where `parentId`=0 and `rootCategId`=?";
    	$bindvars = array($this->ws_container);
    	$result = $this->query($query,$bindvars); 
    	while ($ret = $result->fetchRow())
		$this->remove_ws($ret["categId"]);
    	
	// In the end, delete the WS Container
	$this->remove_ws($this->ws_container);
	
	return true;
    }
    
    /** Remove a group from a WS
     * 
     * @param $ws_id The id of the WS where the group will be removed from
     * @param $groupName The name of the group you want to remove
     * @return true
     */
    public function remove_group_from_ws ($ws_id,$groupName)
    {
	// Check if the group is included in other WS
    	$query = "select count(*) from `users_objectpermissions`
			   where `groupName` = ? and `permName` = 'tiki_p_ws_view'";
	$result = $this->getOne($query, array($groupName));
    	// If the group only had access to the current WS
    	if (($result == 1) && !($group == 'Anonymous' || $group == 'Registered' || $group == 'Admin'))
    	{
		// Delete the group
		global $userlib; require_once 'lib/userslib.php';
		$userlib->remove_group($groupName);		
    	}    	
    	// If the group has access to other WS
    	else
    	{
    	    	$hashWS = md5($this->objectType . strtolower($ws_id));
    	  
    		// Delete all perms added for the WS related to this group
    		$query = "delete from `users_objectpermissions`
				   where `groupName` = ? and `objectId` = ?";
		$this->query($query, array($groupName, $hashWS), -1, -1, false);
		
		// Get the objects that only belongs to the WS
		$query = "select * from `tiki_objects` t0, `tiki_category_objects` t1 
				   where t0.`objectId` = t1.`catObjectId` and t1.`categId`=? 
				   and not exists (select * from `tiki_category_objects` t2 
				   where t1.`catObjectId`=t2.`catObjectId` and not t2.`categId`=?)";
		$result = $this->query($query, array($ws_id, $ws_id));
		while ($ret = $result->fetchRow())
	    		$listWSUniqueObjects[] = $ret;
	    	// For every unique object delete the object pems related to the group
    		foreach ($listWSUniqueObjects as $ws_object)
    		{
    			$hashObject = $hashObject = md5($ws_object["type"] . strtolower($ws_object["itemId"] ));
    			$query = "delete from `users_objectpermissions` where `groupName` = ? and `objectType` = ? and `objectId` = ?";
    			$this->query($query,array($groupName,$ws_object["type"],$hashObject));
    		}
    	}
    	
    	return true;
    }
	
    /** Add a object to a WS (it can be a wiki page, file gal, etc)
     *
     * @param $ws_id The id of the WS you want to add a object
     * @param $itemId The id of the item (in wikis it's equal to its name)
     * @param $type The type of the object
     * @return -
     */
    public function add_ws_object ($ws_id, $itemId, $type, $name = '', $description = '', $href = '')
    {
	$id = parent::add_categorized_object($type, $itemId, $description, $name, $href);
	parent::categorize($id,$ws_id);
	return true;
    } 

    /** Create a new object (it can be a wiki page, file gal, etc)
     *
     * @param $ws_id The id of the WS you want to add a object
     * @param $itemId The id of the item (in wikis it's equal to its name)
     * @param $type The type of the object
     * @return -
     */    
    public function create_ws_object ($ws_id, $name, $type, $description='', $params = array())
    {
    	global $user;
    	switch ($type)
    	{
    		case 'category':
		{
			parent::add_category($ws_id, $name, $description);
			break;			
		}
		case 'wiki page':
		case 'wikipage':
		case 'wiki_page':
		{
			global $tikilib;
			$tikilib->create_page($name, 0, '', date("U"), $description, $user, $_SERVER["REMOTE_ADDR"], $description);
			$itemId = $name;
			$href = "tiki-index.php?page=".urlencode($name);
			break;
		}
		case 'tracker':
		{
			global $trklib;
			include_once ('lib/trackers/trackerlib.php');
			$tracker_options["showCreated"] = 'y';
			$tracker_options["showStatus"] = 'y';
			$tracker_options["showStatusAdminOnly"] = 'y';
			$tracker_options["simpleEmail"] = 'n';
			$tracker_options["outboundEmail"] = '';
			$tracker_options["newItemStatus"] = 'y';
			$tracker_options["useRatings"] = 'y';
			$tracker_options["showRatings"] = 'y';
			$tracker_options["useComments"] = 'y';
			$tracker_options["showComments"] = 'y';
			$tracker_options["useAttachments"] = 'y';
			$tracker_options["showAttachments"] = 'y';
			$tracker_options["showLastModif"] = 'y';
			$tracker_options["defaultOrderDir"] = 'asc';
			$tracker_options["newItemStatus"] = '';
			$tracker_options["modItemStatus"] = '';
			$tracker_options["defaultOrderKey"] = '';
			$tracker_options["writerCanModify"] = 'y';
			$tracker_options["writerGroupCanModify"] = 'n';
			$tracker_options["defaultStatus"] = 'o';
			$itemId = $trklib->replace_tracker(null, $name, $description,$tracker_options); 
			$href = "tiki-view_tracker.php?trackerId=".$itemId;
			break;
		}
		case 'quiz':
		{
			global $quizlib;
			include_once ('lib/quizzes/quizlib.php');
			$itemId = $quizlib->replace_quiz(null, $name, $description, 'n', 'n', 'y', 'n', 'n', 'n', 10, 'y', 60 * 60, date("U"), date("U"), '');
			$href = "./tiki-take_quiz.php?quizId=".$itemId;
			break;
		}
		case 'article':
		{
			global $artlib;
			include_once ('lib/articles/artlib.php');
			$itemId = $artlib->replace_submission($name, $user, null, 'n', '', 0, '', '', $description, '', date("U"), date("U"), $user, 0, 0, 0, null, '', $description, '', '', '', '', 'n');
			$href = "./tiki-read_article.php?articleId=".$itemId;
			break;
		}
		case 'faq':
		{
			global $faqlib;
			include_once ('lib/faqs/faqlib.php');
			$itemId = $faqlib->replace_faq(null, $name, $description, 'y');
			$href = "./tiki-view_faq.php?faqId=".$itemId;
			break;
		}
		case 'blog':
		{
			global $bloglib;
			include_once ('lib/blogs/bloglib.php');
			$itemId = $bloglib->replace_blog($name, $description, $user, 'y', 10, '', '', 'y', 'y', 'y', 'y');
			$href =  "tiki-view_blog.php?blogId=".$itemId;			
			break;
		}
		case 'gallery':
		case 'gal':
		case 'image gallery':
		{
			global $imagegallib;
			include_once ("lib/imagegals/imagegallib.php");
			$itemId = $imagegallib->replace_gallery(null, $name, $description, '', $user, 5, 5, 80, 80, 'y', 'y', 'created', 'desc', 'first', -1, 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'n', 'o', 'n');
			$href = "./tiki-browse_gallery.php?galleryId=".$itemId;
			$type = "image gallery";
			break;
		}
		case 'file_gallery':
		case 'file gallery':
		case 'fgal':
		{
			global $filegallib;
			include_once ('lib/filegals/filegallib.php');
			$fgal_info = array();
			$fgal_info['galleryId'] = null;
			$fgal_info['name'] = $name;
			$fgal_info['desc'] = $description;
			$fgal_info['user'] = 'admin';
			$fgal_info['maxRows'] = 15;
			$fgal_info['public'] = "y";
			$itemId = $filegallib->replace_file_gallery($fgal_info);
			$href = "./tiki-list_file_gallery.php?galleryId=".$itemId;
			$type = "file gallery";			
			break;
		}
		case 'forum':
		{
			global $commentslib;
			include_once ("lib/commentslib.php");
			$itemId = $commentslib->replace_forum(null, $name, $description, 'n', 120, $user);
			$href = "./tiki-view_forum.php?forumId=".$itemId;
			break;
		}
		case 'calendar':
		{
			global $calendarlib;
			include_once ('lib/calendar/calendarlib.php');
			$customflags["customlanguages"] = 'n';
			$customflags["customlocations"] = 'y';
			$customflags["customparticipants"] = 'y';
			$customflags["customcategories"] = 'y';
			$customflags["custompriorities"] = 'y';
			$customflags["customsubscription"] = 'n';
			$customflags["personal"] = "n";
			$itemId = $calendarlib->set_calendar(null, $user, $name, $description, $customflags); 
			$href = "tiki-calendar.php?calendarId=".$itemId."&calIds[]=".$itemId."&viewmode=month";
			break;
		}
		case 'sheet':
		{
			global $sheetlib;
			include_once ('lib/sheet/grid.php');
			$itemId = $sheetlib->replace_sheet(null, $name, $description, $user );
			$sheetlib->replace_layout($itemId, 'default', 0, 0 );
			$href = "tiki-view_sheets.php?sheetId=".$itemId;	
			break;
		}
		case 'survey':
		{
			global $srvlib;
			include_once ('lib/surveys/surveylib.php');
			$itemId = $srvlib->replace_survey(null, $name, $description, "o");
			$href = "tiki-take_survey.php?surveyId=".$itemId;
			break;
		}
		
    	}
    	
	return $this->add_ws_object ($ws_id, $itemId, $type, $name, $description, $href);
    }
    
    /** Check if an object belong to a WS or not
     *
     * @param $objectId The id of the object
     * @return true if the object belong at least to a WS
     */
    public function is_object_in_ws($objectId)
    {
	$query = "select count(t0.`categId`) from `tiki_categories` t0, `tiki_category_objects` t1 
			   where t1.`catObjectId` = ? and t1.`categId`= t0.`categId` 
			   and t0.`rootCategId`=?";
	$bindvars = array($objectId, $this->ws_container);
	$num = $this->getOne($query,$bindvars);
	return ($num >= 1);
    }
	
    /** Remove an object inside in a WS
     *
     * @param $ws_id The id of the WS
     * @param $ws_ObjectId The id of the object you want to delete
     * @param $itemId The id of the item you want to delete
     * @param $type The type of the object you want to delete
     * @return true
     */
    public function remove_object_from_ws ($ws_id,$objectId,$itemId,$type)
    {
       	parent::remove_object_from_category($objectId, $ws_id);
    	$hashObject = md5($type . strtolower($itemId));
    	
    	if ($type == "category")
    	{
    		parent::remove_category($objectId);
		return true;
    	}	
    	
    	if (!$this->is_object_in_ws($objectId))
    	{
		// Delete all the object perms related to the object
		$query = "delete from `users_objectpermissions` where `objectType` = ? and `objectId` = ?";
		$this->query($query, array($type, $hashObject), -1, -1, false);
    		
    		// Delete the object from Tiki (TBD)
    		switch ($type)
    		{
			case 'wiki page':
			case 'wikipage':
			case 'wiki_page':
			{
				global $tikilib;
				$tikilib->remove_all_versions($itemId);
				break;
			}
			case 'tracker':
			{
				global $trklib;
				include_once ('lib/trackers/trackerlib.php');
				$trklib->remove_tracker($itemId);
				break;
			}
			case 'quiz':
			{
				global $quizlib;
				include_once ('lib/quizzes/quizlib.php');
				$quizlib->remove_quiz($itemId) ;
				break;
			}
			case 'article':
			{
				global $artlib;
				include_once ('lib/articles/artlib.php');
				$artlib->remove_submission($itemId);
				break;
			}
			case 'faq':
			{
				global $faqlib;
				include_once ('lib/faqs/faqlib.php');
				$faqlib->remove_faq($itemId);
				break;
			}
			case 'blog':
			{
				global $bloglib;
				include_once ('lib/blogs/bloglib.php');
				$bloglib->remove_blog($itemId);
				break;
			}
			case 'gallery':
			case 'gal':
			case 'image gallery':
			{
				global $imagegallib;
				include_once ("lib/imagegals/imagegallib.php");
				$imagegallib->remove_gallery($itemId);
				break;
			}
			case 'file_gallery':
			case 'file gallery':
			case 'fgal':
			{
				global $filegallib;
				include_once ('lib/filegals/filegallib.php');
				$filegallib->remove_file_gallery($itemId);
				break;
			}
			case 'forum':
			{
				global $commentslib;
				include_once ("lib/commentslib.php");
				$commentslib->remove_forum($itemId);
				break;
			}
			case 'calendar':
			{
				global $calendarlib;
				include_once ('lib/calendar/calendarlib.php');
				$calendarlib->drop_calendar($itemId);
				break;
			}
			case 'sheet':
			{
				global $sheetlib;
				include_once ('lib/sheet/grid.php');
				$sheetlib->remove_sheet($itemId);
				break;
			}
			case 'survey':
			{
				global $srvlib;
				include_once ('lib/surveys/surveylib.php');
				$srvlib->remove_survey($itemId);
				break;
			}
    		}
    	}
    	else
    	{
    		// Get the groups that only have access to the WS in which the object is stored.
    		$hashWS = md5($this->objectType . strtolower($ws_id));
    		$query = "select `groupName` from `users_objectpermissions` t1 
    				where `permName`='tiki_p_ws_view' and `objectId`=? and 
    				not exists (select * from `users_objectpermissions` t2 
    				where t1.`groupName`=t2.`groupName` 
    				and `permName`='tiki_p_ws_view' 
    				and not `objectId`=?)";
    		$result = $this->query($query,array($hashWS,$hashWS));
    		
    		while ($ret = $result->fetchRow())
	    		$listWSUniqueGroups[] = $ret;
	    		
	    	// For every unique group delete the object perms related to the object
    		foreach ($listWSUniqueGroups as $group)
    		{
    			$query = "delete from `users_objectpermissions` where `groupName` = ? and `objectType` = ? and `objectId` = ?";
    			$this->query($query,array($group["groupName"],$type,$hashObject));
    		}
    	}
    	
	return true;
    }

    /** Get the WS id
     *
     * @param $name The name of WS you want to retrieve
     * @param $parentWS The id of the WS parent you want to search. If null, value ws_container will use instead
     * @return WS id if there are any
     */
    public function get_ws_id($name, $parentWS)
    {
	$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=? and `rootCategId`=?";
	$bindvars = array($name, $parentWS, $this->ws_container);

	return $this->getOne($query, $bindvars);
    }

    /** Get a WS name by its id
     *
     * @param $ws_id The id of the WS you want to retrieve the name
     * @param $parentWS The id of the WS parent you want to search. If null, value ws_container will use instead
     * @return An array with all the names of WS you want to search
     */
    public function get_ws_name($ws_id)
    {
	$query = "select `name` from `tiki_categories` where `categId`=?";
	$bindvars = array($ws_id);

	return $this->getOne($query, $bindvars);
    }
    
    public function get_ws_description($ws_id)
    {
	$query = "select `description` from `tiki_categories` where `categId`=?";
	$bindvars = array($ws_id);

	return $this->getOne($query, $bindvars);
    }
	
    /** Give a set of permissions to a group for a specific WS (view, addresources, addgroups,...)
     *
     * @param $ws_id The id of the WS
     * @param $groupName The name of the group you want to set perms
     * @param $permList An associative array for enable or disable perms
     */
    public function set_permissions_for_group_in_ws ($ws_id,$groupName,$permList)
    {
	$hashWS = md5($this->objectType . strtolower($ws_id));

	foreach ($permList as $permName)
	{
	    // If already exists, overwrite 
	    $query = "delete from `users_objectpermissions`
		where `groupName` = ? and
		`permName` = ? and
		`objectId` = ?";
	    $this->query($query, array($groupName, $permName,$hashWS), -1, -1, false);
	
	    $query = "insert into `users_objectpermissions`(`groupName`,
		`objectId`, `objectType`, `permName`)
		values(?, ?, ?, ?)";		
	    $this->query($query, array($groupName, $hashWS, $this->objectType, $permName));
	}	

	return true;
    }
    
    /** Give a set of permissions to a set of groups for a specific object (view, edit, comment...)
     *
     * @param $ws_id The id of the WS
     * @param $groupSet an asociative array in the form array("groupName" => (string) name,  "permList" => array("perm1", "perm2", ...) 
     */
    public function set_permissions_for_groups_in_object ($itemId, $objectType, $groupSet)
    {
    	global $userlib; require_once 'lib/userslib.php';
    	
	foreach ($groupSet as $group)
	{
		$groupName = $group["groupName"];
		$permList = $group["permList"];
		foreach ($permList as $permName)
		    $userlib->assign_object_permission($groupName, $itemId, $objectType, $permName);
	}	
	return true;
    }
	
    /** List the groups that have access to a WS
     *
     * @param $ws_id The id of the WS
     * @return A list of the groups that have access to the given WS
     */
    public function list_groups_that_can_access_in_ws ($ws_id, $maxRecords = -1, $offset = -1)
    {    	
	$hashWS = md5($this->objectType . strtolower($ws_id));

	$query = "select t0.* from `users_groups` t0, `users_objectpermissions` t1 where
			   t1.`objectId`=? and t1.`permName`='tiki_p_ws_view' 
			   and t1.`groupName` = t0.`groupName`";
	$bindvars = array($hashWS);
	$result = $this->query($query,$bindvars,$maxRecords,$offset);

	while ($ret = $result->fetchRow())
	    $listWSGroups[] = $ret;

	return $listWSGroups;
    }
    	
    /** List all WS that a group have access
     *
     * @param $groupName The name of the group
     * @return An associative array with the WS that the group have access
     */
    public function list_ws_that_can_be_accessed_by_group ($groupName, $maxRecords = -1, $offset = -1)
    {	
	$query = "select `objectId` from `users_objectpermissions` where (`groupName`=? and `permName`='tiki_p_ws_view') ";
	$bindvars = array($groupName);
	$result = $this->query($query,$bindvars,$maxRecords,$offset);

	while ($res = $result->fetchRow())
	    $groupWS[] = $res["objectId"];
		
	$idws = $this->ws_container;
	$query = "select * from `tiki_categories` where `rootCategId`= $idws";
	$bindvars = array();
	$listWS = $this->query($query,$bindvars);
		
	while ($res = $listWS->fetchRow()) 
	{
	    $ws_id = $res["categId"];
	    $hashWS = md5($this->objectType . strtolower($ws_id));

	    if (in_array($hashWS,$groupWS))
	    {
	    	$wspath = $this->get_category_path($res["categId"]);
		$tepath = array();	
		foreach ($wspath as $ws) {
			$tepath[] = $ws['name'];
		}
		$wspath = implode("::",$tepath);
		$wspathforsort = implode("!!",$tepath);
		$res["wspath"] = $wspath;
		$res["deep"] = count($tepath);
		
		$listGroupWS["$wspathforsort"] = $res;
	    }
	}
	
	ksort($listGroupWS);

	return $listGroupWS;
    }

    /** List all WS stored in TikiWiki
     *
     * TODO Find a better way to explain
     */
    public function list_all_ws ($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid)
    {
		return parent::list_all_categories ($offset, $maxRecords, $sort_mode, $find, $type, $objid, true);
    }
	
    /** List all WS that a user have access
     *
     * @param $user The name of the user
     * @return An associative array with the WS that the user has access
     */
	public function list_ws_that_user_have_access ($user, $maxRecords = -1, $offset = -1)
	{
		require_once('lib/userslib.php');
		global $userlib;
		
		$ws = array();		
		
		$query = "select distinct t3.`objectId` from `users_objectpermissions` t3, `users_usergroups` t2, `users_users` t1
				   where t1.`login` = ? 
				   and (t1.`userId` = t2.`userId`) 
				   and (t2.`groupName` = t3.`groupName`) 
				   and t3.`permName` = 'tiki_p_ws_view'";
		$result = $this->query($query,array($user), $maxRecords, $offset);
		while ($res = $result->fetchRow())
			$userWSHashes[] = $res["objectId"];
		
		$idws = $this->ws_container;
		$query = "select * from `tiki_categories` where `rootCategId`= $idws";
		$bindvars = array();
		$listWS = $this->query($query,$bindvars);
		
		while ($res = $listWS->fetchRow()) 
		{
		    $ws_id = $res["categId"];
		    $hashWS = md5($this->objectType . strtolower($ws_id));
	
		    if (in_array($hashWS,$userWSHashes))
		    {
		    	$wspath = $this->get_category_path($res["categId"]);
			$tepath = array();	
			foreach ($wspath as $ws)
			{
				$tepath[] = $ws['name'];
			}
			$wspath = implode("::",$tepath);
			$wspathforsort = implode("!!",$tepath);
			$res["wspath"] = $wspath;
			$res["deep"] = count($tepath);
			$res["href"] = "tiki-user_ws.php?showWS=".$ws_id;
			
			$listUserWS["$wspathforsort"] = $res;
		    }
		}

		ksort($listUserWS);

		return $listUserWS;
	}
	
    /** List the objects stored in a workspace
     *
     * @param $ws_id The id of the WS
     * @return An associative array of objects related to a single WS
     */
    public function list_ws_objects ($ws_id, $maxRecords = -1, $offset = -1)
    {
	$query = "select * from `tiki_objects` t0, `tiki_category_objects` t1 
			   where t1.`categId`=? and t1.`catObjectId`=t0.`objectId`";
	$bindvars = array($ws_id);
	$result = $this->query($query,$bindvars, $maxRecords, $offset);
	while ($res = $result->fetchRow())
	{
		$valforsort = $res["type"]."!!".$res["itemId"];
		$listWSObjects["$valforsort"] = $res;
	}
	ksort($listWSObjects);
	return $listWSObjects;
    }

    /** Get the stored perms for a object for a specific user
     *
     * @param $objId The object you want to check
     * @param $objectType The type of the object
     * @param $user The name of the user
     * @return An array with the objects perms related to a object for a user
     */
    public function get_object_perms_for_user ($objId, $objectType, $user)
    {
	$objectId = md5($objectType . strtolower($objId));
	$query = "select distinct t3.`permName` from `users_objectpermissions` t3, `users_usergroups` t2, `users_users` t1
				   where t1.`login` = ? 
				   and (t1.`userId` = t2.`userId`) 
				   and (t2.`groupName` = t3.`groupName`) 
				   and t3.`objectId`=? 
				   and t3.`objectType`=?";
	$bindvars = array($user, $objectId, $objectType);
	$result = $this->query($query,$bindvars);
	while ($res = $result->fetchRow())
		$objectPermsUser[] = $res["permName"];

	return $objectPermsUser;
    }
	
    /** List the objects stored in a workspace for a specific user
     *
     * @param $ws_id The id of the WS
     * @param $user The username
     * @return Associative array with the objects that a user have access from a WS
     */
    public function list_ws_objects_for_user ($ws_id, $user, $maxRecords = -1, $offset = -1)
    {
	require_once('lib/userslib.php');
	global $userlib; global $objectlib;
		
	$listWSObjects = $this->list_ws_objects($ws_id, $maxRecords, $offset);
		
	foreach ($listWSObjects as $object)
	{
		$objectType = $object["type"];
		$objId = $object["itemId"];
		$viewPerm = parent::get_needed_perm($objectType, "view");
		
		$objectPermsUser = $this->get_object_perms_for_user ($objId, $objectType, $user);
		if (in_array($viewPerm,$objectPermsUser))
			$object["userCanView"] = "y";
		else
			$object["userCanView"] = "n";
				
		$listWSObjectsUser[] = $object;		
	} 

	return $listWSObjectsUser;
    }
	
    /** List the objects stored in a workspace for a specific user
     *
     * @param $ws_id The id of the WS
     * @return Associative array with the WS childs
     */	
     public function get_ws_childs ($ws_id)
     {
     	$query = "select `categId` from `tiki_categories` where `parentId`= ?";
     	$bindvars = array($ws_id);
     	$result = $this->query($query,$bindvars);
	while ($res = $result->fetchRow())
		$wsChilds[] = $res["categId"];

     	return $wsChilds;
     }	
     
      /** Add a user in a group
     *
     * @param $user The id of the user
     * @param $groupName The name of the group
     * @return -
     */	
     public function add_user_to_ws_group ($user, $groupName)
     {
     	global $userlib; require_once 'lib/userslib.php';
     	$userlib->assign_user_to_group($user, $groupName);
     }
     
      /** Count the number of WS stored in Tiki or the number of WS that a user have access
     *
     * @param $user The id of the user (if not set, then get the WS stored in Tiki)
     * @return $cant the number of WS stored in Tiki or the number of WS that a user have access
     */	
     public function count_ws ($userName = null)
     {
     	if ($userName)
     	{
     		$query_cant = "select count(distinct t3.`objectId`) from `users_objectpermissions` t3, `users_usergroups` t2, `users_users` t1
					     where t1.`login` = ? 
					     and (t1.`userId` = t2.`userId`) 
					     and (t2.`groupName` = t3.`groupName`) 
					     and t3.`permName` = 'tiki_p_ws_view'";
		$bindvals = array($userName);
     	}
     	else
     	{
     		$query_cant = "select count(*) from `tiki_categories` where `rootCategId` = ? ";
     		$bindvals = array($this->ws_container);
     	}
	return $this->getOne($query_cant,$bindvals);
     }
     
     /** Count the number of objects stored in a WS
     *
     * @param $ws_id The id of the ws
     * @return $cant the number of objects stored in the WS
     */	
     public function count_objects_in_ws ($ws_id)
     {
     	$query_cant = "select count(*) from `tiki_category_objects` where `categId` = ?";
     	$bindvals = array($ws_id);
     	
     	return $this->getOne($query_cant,$bindvals);
     }
     
     /** Count the number of groups that have access in a WS
     *
     * @param $ws_id The id of the ws
     * @return $cant the number of objects stored in the WS
     */	
     public function count_groups_in_ws ($ws_id)
     {
     	$hashWS = md5($this->objectType . strtolower($ws_id));

	$query_cant = "select count(*) from `users_objectpermissions` where 
	    `objectId`=? and `permName`='tiki_p_ws_view'";
	$bindvals = array($hashWS);
     	
     	return $this->getOne($query_cant,$bindvals);
     }
     
     /** Return the admin permissions available for a group in a WS
     *
     * @param $ws_id The id of the ws
     * @return $cant the number of objects stored in the WS
     */	
     public function get_ws_adminperms ()
     {
     	$query = "SELECT * FROM `users_permissions` where `type`='ws' and `level`='admin'";
     	$bindvals = array();
     	$result = $this->query($query,$bindvals);
     	
     	while ($res = $result->fetchRow())
		$wsPerms[] = $res;
	return $wsPerms;
     }
}

$wslib = new wslib();
