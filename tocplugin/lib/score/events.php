<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Each element of $events is an array with (dependent features, category, event name, translated description, default score, default expiration)
$events = array(
array("","General","login",tra("Log In"),2,0), // tiki-login.php
//array("","General","login_remain",tra("Stay logged"),2,60),

array("","General","profile_see",tra("See other users' profiles"),2,0), // tiki-user_information.php
array("","General","profile_is_seen",tra("Have your profile seen"),1,0), // tiki-user_information.php

array("feature_friends","General","friend_new",tra("Make friends"),10,0), // userslib.php
array("feature_messages","General","message_receive",tra("Receive a message"),1,0), // messu-compose.php
array("feature_messages","General","message_send",tra("Send a message"),2,0), // messu-compose.php

array("feature_articles","Articles","article_read",tra("Read an article"),2,0), // tikilib.php
array("feature_articles","Articles","article_new",tra("Publish an article"),20,0), 
array("feature_articles","Articles","article_is_read",tra("Have your article read"),1,0), // tikilib.php

array("feature_file_galleries","File Galleries","fgallery_new",tra("Create new file gallery"),10,0),  // filegallib.php
array("feature_file_galleries","File Galleries","fgallery_new_file",tra("Upload a new file to a gallery"),10,0),  // filegallib.php
array("feature_file_galleries","File Galleries","fgallery_download",tra("Download another user's file"),5,0),  // tikilib.php
array("feature_file_galleries","File Galleries","fgallery_is_downloaded",tra("Have your file downloaded"),5,0), // tikilib.php

array("feature_galleries","Image galleries","igallery_new",tra("Create a new image gallery"),10,0), // imagegallib.php
array("feature_galleries","Image galleries","igallery_new_img",tra("Upload a new image to a gallery"),6,0), // imagegallib.php
array("feature_galleries","Image galleries","igallery_see",tra("View another user's gallery"),4,0),  // imagegallib.php
array("feature_galleries","Image galleries","igallery_see_img",tra("View another user's images"),3,0), // imagegallib.php
array("feature_galleries","Image galleries","igallery_seen",tra("Have your gallery viewed by another user"),2,0), // imagegallib.php
array("feature_galleries","Image galleries","igallery_img_seen",tra("Have your image viewed"),1,0), // imagegallib.php

array("feature_blogs","Blogs","blog_new",tra("Create a new blog"),20,0), // bloglib.php
array("feature_blogs","Blogs","blog_post",tra("Post in a blog"),5,0), // bloglib.php
array("feature_blogs","Blogs","blog_read",tra("Read another user's blog"),2,0), // tikilib.php
array("feature_blogs","Blogs","blog_is_read",tra("Have your blog read"),3,0), // tikilib.php

array("feature_wiki","Wiki","wiki_new",tra("Create a new wiki page"),10,0), // tikilib.php
array("feature_wiki","Wiki","wiki_edit",tra("Edit an existing page"),5,0), // tikilib.php
array("feature_wiki","Wiki","wiki_attach_file",tra("Attach a file"),3,0), // wikilib.php

array("feature_forums","Forums","forum_topic_post",tra("Create a new forum topic"),10,0), // comments.php
array("feature_forums","Forums","forum_topic_reply",tra("Reply to a forum topic"),5,0), // comments.php
array("feature_forums","Forums","forum_post_read",tra("Read a forum post"),1,0), // comments.php
array("feature_forums","Forums","forum_post_is_read",tra("Have your forum post read"),2,0), // comments.php

array("feature_trackers","Trackers","trackeritem_create",tra("Create a new tracker item"),10,0), // trackerlib.php
array("feature_trackers","Trackers","trackeritem_edit",tra("Edit a tracker item"),5,0), // trackerlib.php
array("feature_trackers","Trackers","trackeritem_read",tra("View a tracker item"),1,0), // trackerlib.php
array("feature_trackers","Trackers","trackeritem_is_read",tra("Have your tracker item viewed"),2,0), // trackerlib.php
array("feature_trackers","Trackers","tracker_field_entered",tra("Tracker field content added"),10,0), // trackerlib.php

array("user_favorites","Ratings and Favorites","item_favorited",tra("Favorite an item"),1,0),
array("user_favorites","Ratings and Favorites","item_is_favorited",tra("Have your item favorited"),10,0), 
array("article_user_rating","Ratings and Favorites","item_is_rated",tra("Have your item rated (per rating)"),10,0), 
array("article_user_rating","Ratings and Favorites","item_is_unrated",tra("Have your item unrated (per rating)"),-10,0), 
array("wikiplugin_avatar","Ratings and Favorites","avatar_added",tra("Add a profile photo"),10,0)
);
