<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// For documentation how to use this file please see the comment at the end of this file

//this script may only be included - so its better to die if called  directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


require_once('lib/perspectivelib.php');
require_once('lib/categories/categlib.php');

class AreasLib extends CategLib
{

	function HandleObjectCategories($objectCategoryIds){
    	global $prefs, $perspectivelib;  
    
   	if (!empty($objectCategoryIds)){
	        foreach($objectCategoryIds as $categId){
			// If parent category has ID equal to value of $prefs['areas_root']
			if ($this->get_category_parent($categId) == $prefs['areas_root']){
				$foundPerspective = $this->get_perspective_by_categid($categId); 
				// If the found perspective is different than the current perspective, update it.
		                if ($foundPerspective != $_SESSION['current_perspective']) {
					$perspectivelib->set_perspective($foundPerspective);
					//Reroute browser back to calling script after we have applied our hack.
					header("Location: ". $_SERVER['REQUEST_URI']);	
				}
			}else{ // If parent category id does not equal $prefs['areas_root'] set the default perspective (0)
				if ($foundPerspective != $_SESSION['current_perspective']) {
				$perspectivelib->set_perspective(0);
				//Reroute browser back to calling script after we have applied our hack.
				header("Location: ". $_SERVER['REQUEST_URI']);	
				}
			}
       		}
	}
/*    else if ($_SESSION['current_perspective'] !== 0)     // decomment this violates the category jail 
    {
        $perspectivelib->set_perspective(0);
        
    }*/
	}

/*
 for the difference between should and is, first retrieve all perspectives given for any reason
 and then choose one in the function below, namely the first.
*/
	function get_perspectives_by_categid($categId){
		$result = $this->query( "SELECT `categId`, `perspectives` FROM tiki_areas WHERE categId = ?", array($categId));
		while($row = $result->fetchRow()) return unserialize($row['perspectives']);
		return false;
	}
/*
 pick up the first or only perspective assigned to category with id categId
 returns false if there is no entry for this category and returns 0 if it has no perspective
*/
	function get_perspective_by_categid($categId){
		$persp = $this->get_perspectives_by_categid($categId);
		if($persp===false) return false;
		if(count($persp)==0) return 0;
		return $persp[0];
	}

	function update_areas(){
		global $prefs;
		$areas = array();
		$descendants = $this->get_category_descendants($prefs['areas_root']);
		if( is_array($descendants) ){
		foreach($descendants as $item)
			$areas[$item] = array();	// it only should be just one perspective assigned
		$result = $this->query( "SELECT `perspectiveId`, `pref`, `value` FROM tiki_perspective_preferences WHERE pref = 'category_jail'", array());

	        if($result !== false){
			while( $row = $result->fetchRow() ) {
				$categs = unserialize( $row['value'] );			
				foreach($categs as $item) if(array_key_exists($item, $areas)) $areas[$item][] = $row['perspectiveId'];
	 		}

		foreach($areas as $key=>$item){
			$result = $this->query("SELECT `categId`, `perspectives` FROM tiki_areas WHERE categId = ".$key );
			if(count($result->fetchRow())){
				$result = $this->query("UPDATE tiki_areas SET perspectives = ? WHERE categId = ?",array(serialize($item), $key));
			}else{ 
				$result = $this->query("INSERT INTO tiki_areas (categId, perspectives) VALUES(?,?)", array($key, serialize($item)));}
			}
		}
		return true;
		}else return false;
	}
} // class end
$areaslib = new AreasLib();
global $areaslib;

/*-----------------------------------------------
+++ Description of Perspective Binder / Areas +++ 
-------------------------------------------------

This file is a hack to make it possible to divide a Tiki-Website in different individual * areas * by using categories and perspectives.

Most configurations are to be done in this file, which requires ftp-access to the Tiki root.
This is not a long term solution and only a workaround until this feature will be integrated natively in Tiki as "Areas".
Thus in the future all configurations will be done in the Tiki admin dialogues.
This file is just a first development preview based on a code, Jesper Merbt has written for a company project, but it is already possible for you, to use it semi-productive, if you need this function.

Whilst the "Workspaces" function makes complete sets of content-objects only visible for certain groups and leaves all contained content visible in all perspectives (for permitted users), the "Areas" function structures content related to the context and makes it visible only in one specific perspective (for permitted users). The "Areas" feature is independant from membership in groups and is usable also for anonymous non-registered visitors.

----------------------
+++ Configurations +++
----------------------

This feature is integrated in the tiki structure. The following steps, in order of the old configuration steps, describe what was done on the way and where to set the necessary parameters. The end user might look on steps 3 and 5. There is an admin panel for this feature.

Step 1 of 5still
-----------

This file lib/perspective_binder.php is included in the file tiki-setup. php after categories were setup, if feature_areas and feature_perspectives are set to 'y', since this depends on both features.

In a Tiki 6.3 this line could be included for ex. under line 131 of tiki-setup.php
In a Tiki 7.1 this line could be included for ex. under line 137 of tiki-setup.php .

Step 2 of 5
-----------

The database query was tikified via the TikiDb class. The query takes place in get_perspectives_by_categid and was put into lib/perspectivelib.php along to the function set_perspective. set_perspective is used by tiki-switch_perspective.php, too.

Step 3 of 5
-----------

In Tiki you need to setup a structure of categories:
A basic category must be a "top category" with no parent.
You can name it for example "Areas"
Then you create one or several child categories of this category, wich you can name "Area1", "Area2", "Area3", etc.

The id of the parent category you can type in the text field areas root id in the category admin panel.

Step 4 of 5
-----------

Please activate "categories used in templates" in your Tiki installation: 
Admin->Categories check tick box "categories used in templates.

To satisfy this step feature_areas is set to depend on categories_used_in_tpl.

Step 5 of 5
-----------

Setup one perspective for each of the categories in the areas-structure.
Assign one category Id of this structure to the category jail of the related perspective, in the way that each perspective Id=X has one category Id=Y in its jail to bind exact one category and one perspective together.


--------------------------
+++ using the feature: +++
--------------------------

Once you proceeded all steps, you can assign objects like wikipages to ONE of the areas-categories and it will always be shown in the related perspective.

Thus you can built up "areas" as "subwebsites" and the (categorised) content, you call in the browser, will always be visible in the "environment" of theme, modules, etc., that you defined for the related perspective.

If you do not assign a content object to one of the areas-categories, it will still be visible in every perspective.

You can still assign objects to several categories, but please only assign to one of the areas-category structure, to make Tiki not confused what area an object is assigned to. You CAN assign objects only to one single area, OR leave it visible in all perspectives like it is the default. You CANNOT assign objects to several areas.

Now you can combine very specific content, that mainly makes sense in a specific context and is always automatically adressed in the related perspective, together with global content like imprints or general information, wich remains accessible in all perspectives across the whole Tiki website.

Examples of usage would be:

* Project related content, that only should be visible in the perspective and context of the specific project.
* Subwebsites of local groups 
a) that should not appear on the national website
b) whith content that automatically should be adressed to the right local website without cryptical urls, even if the same Tiki installation is shared for several groups
* News Websites with specific regional or local related content and common content in the same Tiki installation
* List may be continued over time

--------------------------
+++ End of Description +++
------------------------*/
