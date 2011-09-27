<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// For documentation how to use this file please see the comment at the end of this file
global $prefs;  
$sagsSideCatId = $prefs['areas_root']; // ID of the parent category that indicates the current page is assigned to an "Area"

HandleObjectCategories($objectCategoryIds);

function HandleObjectCategories($objectCategoryIds)
{
    
    global $categlib;
    global $sagsSideCatId;
    
    if (!empty($objectCategoryIds))
    {
        foreach($objectCategoryIds as $categId)
        {
             $foundPerspective = GetPerspectiveId($categId);   // place this line here so the var is defined in the else case
            if ($categlib->get_category_parent($categId) == $sagsSideCatId) // If parent category has ID equal to value of $sagsSideCatId
            {
            
                if ($foundPerspective != $_SESSION['current_perspective']) // If the found perspective is different than the current perspective, update it.
                {
                    SetPerspectiveId($foundPerspective);
                }
            }
            else // If parent category id does not equal $sagsSideCatId set the default perspective (0)
            {
                if ($foundPerspective != $_SESSION['current_perspective'])
                {
                    SetPerspectiveId(0);
                }
            }
        
        }
    }
    else if ($_SESSION['current_perspective'] !== 0)
    {
        SetPerspectiveId(0);
        
    }
}

function GetPerspectiveId($categoryId)
{
global $tikilib;
	try {
	$selectSql = "SELECT 
				`perspectiveId`,
       				`pref`,
       				`value`
				FROM `tiki_perspective_preferences`
				WHERE pref = 'category_jail'";
		$result = $tikilib->query($selectSql, array(), -1, 0);
//	$statement = $DAL->prepare($selectSql);
//	$statement->execute();
	
            while ($row = $result->fetchRow()) 
               {
            		$categories = unserialize($row["value"]); // categories are stored as serialized PHP in the database.
                        
                            if (in_array($categoryId, $categories))
                            {
				 return $row["perspectiveId"];
                    	}
                }
       	return 0;
	}
        catch (Exception $ex)
	{
		print_r($ex);
	}
}
function SetPerspectiveId($perspectiveId)
{	
    // Set the perspective in session var so it will last across several pages. (URL Parameter "perspectiveId" is only temporary across diff. pages.)
    $_SESSION['current_perspective'] = $perspectiveId;

    //Reroute browser back to calling script after we have applied our hack.
    header("Location: ". $_SERVER['REQUEST_URI']);	
}

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

Please make sure, that on your webserver PDO is active - In this early stage this feature will only work with PDO and not with different database drivers.

Step 1 of 6‪still
-----------

This file tiki-perspective_binder.php must be saved in the tiki root folder.
Include this file in the file tiki-setup. php * after the categories lib has been loaded * with the following line:

//	include_once ('tiki-perspective_binder.php');
-> deleting the " // " AFTER all other necessary configurations will activate the tiki-perspective_binder.php

In a Tiki 6.3 this line could be included for ex. under line 131 of tiki-setup.php
In a Tiki 7.1 this line could be included for ex. under line 137 of tiki-setup.php .

Step 2 of 6
-----------

Then you have to get the database connection datas from db/local.php and fill them in above in THIS file in line 49 and line 55 this way:
line 49: 	$DAL = new PDO('mysql:host=LOCALHOSTADRESS;dbname=DATABASENAME', 'DATABASEUSERNAME', 'DATABASEPASSWORD');
line 55:	FROM `DATABASENAME`.`tiki_perspective_preferences`

Step 3 of 6
-----------

In Tiki you need to setup a structure of categories:
A basic category must be a "top category" with no parent.
You can name it for example "Areas"
Then you create one or several child categories of this category, wich you can name "Area1", "Area2", "Area3", etc.

Step 4 of 6
-----------

Please activate "categories used in templates" in your Tiki installation: 
Admin->Categories check tick box "categories used in templates

Step 5 of 6
-----------

Setup one perspective for each of the categories in the areas-structure.
Assign one category Id of this structure to the category jail of the related perspective, in the way that each perspective Id=X has one category Id=Y in its jail to bind exact one category and one perspective together.

Step 6 of 6
-----------

Go back to tiki-setup.php and uncomment line 138 by deleting the two slashes " // ".

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
