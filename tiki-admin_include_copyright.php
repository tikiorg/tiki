<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_copyright.php,v 1.4.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.


if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if ( $prefs['feature_copyright'] != 'y' )
{
	$smarty->assign('msg', tra("This feature is disabled").": feature_copyright");
        $smarty->display("error.tpl");
        die;

}
if (isset($_REQUEST["setcopyright"])) {
        check_ticket('admin-inc-copyright');
        if (isset($_REQUEST["wiki_feature_copyrights"]) && $_REQUEST["wiki_feature_copyrights"] == "on") {
                $tikilib->set_preference("wiki_feature_copyrights", 'y');
        } else {
                $tikilib->set_preference("wiki_feature_copyrights", 'n');
        }
       if (isset($_REQUEST["blogues_feature_copyrights"]) && $_REQUEST["blogues_feature_copyrights"] == "on") {
                $tikilib->set_preference("blogues_feature_copyrights", 'y');
        } else {
                $tikilib->set_preference("blogues_feature_copyrights", 'n');
        }
 	if (isset($_REQUEST["faqs_feature_copyrights"]) && $_REQUEST["faqs_feature_copyrights"] == "on") {
                $tikilib->set_preference("faqs_feature_copyrights", 'y');
        } else {
                $tikilib->set_preference("faqs_feature_copyrights", 'n');
        }
       if (isset($_REQUEST["articles_feature_copyrights"]) && $_REQUEST["articles_feature_copyrights"] == "on") {
                $tikilib->set_preference("articles_feature_copyrights", 'y');
        } else {
                $tikilib->set_preference("articles_feature_copyrights", 'n');
        }

        if (isset($_REQUEST["wikiLicensePage"])) {
                $tikilib->set_preference("wikiLicensePage", $_REQUEST["wikiLicensePage"]);
        }

        if (isset($_REQUEST["wikiSubmitNotice"])) {
                $tikilib->set_preference("wikiSubmitNotice", $_REQUEST["wikiSubmitNotice"]);
        }
}



ask_ticket('admin-inc-copyright');
?>
