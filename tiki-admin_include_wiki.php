<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_wiki.php,v 1.15 2004-03-25 06:19:08 dabright Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["dump"])) {
	check_ticket('admin-inc-wiki');
	include ("lib/tar.class.php");

	error_reporting (E_ERROR | E_WARNING);
	$adminlib->dump();
}

// Included for the forum dropdown
include_once ("lib/commentslib.php");

if (isset($_REQUEST["createtag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if ($adminlib->tag_exists($_REQUEST["tagname"])) {
		$smarty->assign('msg', tra("Tag already exists"));

		$smarty->display("error.tpl");
		die;
	}

	$adminlib->create_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["restoretag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if (!$adminlib->tag_exists($_REQUEST["tagname"])) {
		$smarty->assign('msg', tra("Tag not found"));

		$smarty->display("error.tpl");
		die;
	}

	$adminlib->restore_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["removetag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	$adminlib->remove_tag($_REQUEST["tagname"]);
}

if (isset($_REQUEST["wikiprefs"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["wiki_comments_per_page"])) {
		$tikilib->set_preference("wiki_comments_per_page", $_REQUEST["wiki_comments_per_page"]);

		$smarty->assign('wiki_comments_per_page', $_REQUEST["wiki_comments_per_page"]);
	}

	if (isset($_REQUEST["wiki_comments_default_ordering"])) {
		$tikilib->set_preference("wiki_comments_default_ordering", $_REQUEST["wiki_comments_default_ordering"]);

		$smarty->assign('wiki_comments_default_ordering', $_REQUEST["wiki_comments_default_ordering"]);
	}
}

if (isset($_REQUEST["rmvunusedpic"])) {
	check_ticket('admin-inc-wiki');
	$adminlib->remove_unused_pictures();
}

if (isset($_REQUEST["setwikihome"])) {
	check_ticket('admin-inc-wiki');
	$tikilib->set_preference('wikiHomePage', $_REQUEST["wikiHomePage"]);

	$smarty->assign('wikiHomePage', $_REQUEST["wikiHomePage"]);
}

if (isset($_REQUEST["wikidiscussprefs"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["feature_wiki_discuss"])) {
		$tikilib->set_preference('feature_wiki_discuss', 'y');

		$smarty->assign('feature_wiki_discuss', 'y');
	} else {
		$tikilib->set_preference("feature_wiki_discuss", 'n');

		$smarty->assign('feature_wiki_discuss', 'n');
	}

	if (isset($_REQUEST["wiki_forum"])) {
		$tikilib->set_preference('wiki_forum', $_REQUEST["wiki_forum"]);

		$smarty->assign('wiki_forum', $_REQUEST["wiki_forum"]);
		$wiki_forum_id = $tikilib->getOne("select forumID from tiki_forums where name='" . $_REQUEST["wiki_forum"] . "'");
		$tikilib->set_preference('wiki_forum_id', $wiki_forum_id);
		$smarty->assign('wiki_forum_id', $wiki_forum_id);
	}
}

if (isset($_REQUEST["setwikiregex"])) {
	check_ticket('admin-inc-wiki');
	$tikilib->set_preference('wiki_page_regex', $_REQUEST["wiki_page_regex"]);

	$smarty->assign('wiki_page_regex', $_REQUEST["wiki_page_regex"]);
}

if (isset($_REQUEST['wikilistprefs'])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST['wiki_list_name'])) {
		$tikilib->set_preference('wiki_list_name', 'y');
	} else {
		$tikilib->set_preference('wiki_list_name', 'n');
	}

	$smarty->assign('wiki_list_name', isset($_REQUEST['wiki_list_name']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_hits'])) {
		$tikilib->set_preference('wiki_list_hits', 'y');
	} else {
		$tikilib->set_preference('wiki_list_hits', 'n');
	}

	$smarty->assign('wiki_list_hits', isset($_REQUEST['wiki_list_hits']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_lastmodif'])) {
		$tikilib->set_preference('wiki_list_lastmodif', 'y');
	} else {
		$tikilib->set_preference('wiki_list_lastmodif', 'n');
	}

	$smarty->assign('wiki_list_lastmodif', isset($_REQUEST['wiki_list_lastmodif']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_creator'])) {
		$tikilib->set_preference('wiki_list_creator', 'y');
	} else {
		$tikilib->set_preference('wiki_list_creator', 'n');
	}

	$smarty->assign('wiki_list_creator', isset($_REQUEST['wiki_list_creator']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_user'])) {
		$tikilib->set_preference('wiki_list_user', 'y');
	} else {
		$tikilib->set_preference('wiki_list_user', 'n');
	}

	$smarty->assign('wiki_list_user', isset($_REQUEST['wiki_list_user']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_lastver'])) {
		$tikilib->set_preference('wiki_list_lastver', 'y');
	} else {
		$tikilib->set_preference('wiki_list_lastver', 'n');
	}

	$smarty->assign('wiki_list_lastver', isset($_REQUEST['wiki_list_lastver']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_comment'])) {
		$tikilib->set_preference('wiki_list_comment', 'y');
	} else {
		$tikilib->set_preference('wiki_list_comment', 'n');
	}

	$smarty->assign('wiki_list_comment', isset($_REQUEST['wiki_list_comment']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_status'])) {
		$tikilib->set_preference('wiki_list_status', 'y');
	} else {
		$tikilib->set_preference('wiki_list_status', 'n');
	}

	$smarty->assign('wiki_list_status', isset($_REQUEST['wiki_list_status']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_versions'])) {
		$tikilib->set_preference('wiki_list_versions', 'y');
	} else {
		$tikilib->set_preference('wiki_list_versions', 'n');
	}

	$smarty->assign('wiki_list_versions', isset($_REQUEST['wiki_list_versions']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_links'])) {
		$tikilib->set_preference('wiki_list_links', 'y');
	} else {
		$tikilib->set_preference('wiki_list_links', 'n');
	}

	$smarty->assign('wiki_list_links', isset($_REQUEST['wiki_list_links']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_backlinks'])) {
		$tikilib->set_preference('wiki_list_backlinks', 'y');
	} else {
		$tikilib->set_preference('wiki_list_backlinks', 'n');
	}

	$smarty->assign('wiki_list_backlinks', isset($_REQUEST['wiki_list_backlinks']) ? 'y' : 'n');

	if (isset($_REQUEST['wiki_list_size'])) {
		$tikilib->set_preference('wiki_list_size', 'y');
	} else {
		$tikilib->set_preference('wiki_list_size', 'n');
	}

	$smarty->assign('wiki_list_size', isset($_REQUEST['wiki_list_size']) ? 'y' : 'n');
}

if (isset($_REQUEST["wikifeatures"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["feature_lastChanges"]) && $_REQUEST["feature_lastChanges"] == "on") {
		$tikilib->set_preference("feature_lastChanges", 'y');

		$smarty->assign("feature_lastChanges", 'y');
	} else {
		$tikilib->set_preference("feature_lastChanges", 'n');

		$smarty->assign("feature_lastChanges", 'n');
	}

	if (isset($_REQUEST["feature_wiki_comments"]) && $_REQUEST["feature_wiki_comments"] == "on") {
		$tikilib->set_preference("feature_wiki_comments", 'y');

		$smarty->assign("feature_wiki_comments", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_comments", 'n');

		$smarty->assign("feature_wiki_comments", 'n');
	}

	if (isset($_REQUEST["feature_wiki_pictures"]) && $_REQUEST["feature_wiki_pictures"] == "on") {
		$tikilib->set_preference("feature_wiki_pictures", 'y');

		$smarty->assign("feature_wiki_pictures", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_pictures", 'n');

		$smarty->assign("feature_wiki_pictures", 'n');
	}

	if (isset($_REQUEST["feature_wiki_description"]) && $_REQUEST["feature_wiki_description"] == "on") {
		$tikilib->set_preference("feature_wiki_description", 'y');

		$smarty->assign("feature_wiki_description", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_description", 'n');

		$smarty->assign("feature_wiki_description", 'n');
	}

	if (isset($_REQUEST["wiki_spellcheck"]) && $_REQUEST["wiki_spellcheck"] == "on") {
		$tikilib->set_preference("wiki_spellcheck", 'y');

		$smarty->assign("wiki_spellcheck", 'y');
	} else {
		$tikilib->set_preference("wiki_spellcheck", 'n');

		$smarty->assign("wiki_spellcheck", 'n');
	}

	if (isset($_REQUEST["feature_warn_on_edit"]) && $_REQUEST["feature_warn_on_edit"] == "on") {
		$tikilib->set_preference("feature_warn_on_edit", 'y');

		$smarty->assign("feature_warn_on_edit", 'y');
	} else {
		$tikilib->set_preference("feature_warn_on_edit", 'n');

		$smarty->assign("feature_warn_on_edit", 'n');
	}

	if (isset($_REQUEST["feature_page_title"]) && $_REQUEST["feature_page_title"] == "on") {
		$tikilib->set_preference("feature_page_title", 'y');

		$smarty->assign("feature_page_title", 'y');
	} else {
		$tikilib->set_preference("feature_page_title", 'n');

		$smarty->assign("feature_page_title", 'n');
	}

	if (isset($_REQUEST["feature_wikiwords"]) && $_REQUEST["feature_wikiwords"] == "on") {
		$tikilib->set_preference("feature_wikiwords", 'y');

		$smarty->assign('feature_wikiwords', 'y');
	} else {
		$tikilib->set_preference("feature_wikiwords", 'n');

		$smarty->assign('feature_wikiwords', 'n');
	}
	
	if(isset($_REQUEST["feature_wiki_plurals"]) && $_REQUEST["feature_wiki_plurals"]=="on") {
		$tikilib->set_preference("feature_wiki_plurals",'y');
		
		$smarty->assign("feature_wiki_plurals",'y');
	} else {
		$tikilib->set_preference("feature_wiki_plurals",'n');
		
		$smarty->assign("feature_wiki_plurals",'n');
	}

	if(isset($_REQUEST["feature_wiki_paragraph_formatting"]) && $_REQUEST["feature_wiki_paragraph_formatting"]=="on") {
		$tikilib->set_preference("feature_wiki_paragraph_formatting",'y');
		
		$smarty->assign("feature_wiki_paragraph_formatting",'y');
	} else {
		$tikilib->set_preference("feature_wiki_paragraph_formatting",'n');
		
		$smarty->assign("feature_wiki_paragraph_formatting",'n');
	}

	$tikilib->set_preference('wiki_cache', $_REQUEST["wiki_cache"]);
	$smarty->assign('wiki_cache', $_REQUEST["wiki_cache"]);

	$tikilib->set_preference("warn_on_edit_time", $_REQUEST["warn_on_edit_time"]);
	$smarty->assign('warn_on_edit_time', $_REQUEST["warn_on_edit_time"]);

	if (isset($_REQUEST["feature_dump"]) && $_REQUEST["feature_dump"] == "on") {
		$tikilib->set_preference("feature_dump", 'y');

		$smarty->assign("feature_dump", 'y');
	} else {
		$tikilib->set_preference("feature_dump", 'n');

		$smarty->assign("feature_dump", 'n');
	}

	if (isset($_REQUEST["feature_wiki_rankings"]) && $_REQUEST["feature_wiki_rankings"] == "on") {
		$tikilib->set_preference("feature_wiki_rankings", 'y');

		$smarty->assign("feature_wiki_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_rankings", 'n');

		$smarty->assign("feature_wiki_rankings", 'n');
	}

	if (isset($_REQUEST["feature_wiki_footnotes"]) && $_REQUEST["feature_wiki_footnotes"] == "on") {
		$tikilib->set_preference("feature_wiki_footnotes", 'y');

		$smarty->assign("feature_wiki_footnotes", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_footnotes", 'n');

		$smarty->assign("feature_wiki_footnotes", 'n');
	}

	if (isset($_REQUEST["feature_wiki_monosp"]) && $_REQUEST["feature_wiki_monosp"] == "on") {
		$tikilib->set_preference("feature_wiki_monosp", 'y');

		$smarty->assign("feature_wiki_monosp", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_monosp", 'n');

		$smarty->assign("feature_wiki_monosp", 'n');
	}

	/*  
	  if(isset($_REQUEST["feature_wiki_notepad"]) && $_REQUEST["feature_wiki_notepad"]=="on") {
		$tikilib->set_preference("feature_wiki_notepad",'y'); 
		$smarty->assign("feature_wiki_notepad",'y');
	  } else {
		$tikilib->set_preference("feature_wiki_notepad",'n');
		$smarty->assign("feature_wiki_notepad",'n');
	  }
	*/
	if (isset($_REQUEST["feature_wiki_usrlock"]) && $_REQUEST["feature_wiki_usrlock"] == "on") {
		$tikilib->set_preference("feature_wiki_usrlock", 'y');

		$smarty->assign("feature_wiki_usrlock", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_usrlock", 'n');

		$smarty->assign("feature_wiki_usrlock", 'n');
	}

	$tikilib->set_preference('feature_wiki_tables', $_REQUEST['feature_wiki_tables']);
	$smarty->assign('feature_wiki_tables', $_REQUEST['feature_wiki_tables']);

	if (isset($_REQUEST["feature_wiki_undo"]) && $_REQUEST["feature_wiki_undo"] == "on") {
		$tikilib->set_preference("feature_wiki_undo", 'y');

		$smarty->assign("feature_wiki_undo", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_undo", 'n');

		$smarty->assign("feature_wiki_undo", 'n');
	}
	
	if (isset($_REQUEST["feature_wiki_icache"]) && $_REQUEST["feature_wiki_icache"] == "on") {
		$tikilib->set_preference("feature_wiki_icache", 'y');

		$smarty->assign("feature_wiki_icache", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_icache", 'n');

		$smarty->assign("feature_wiki_icache", 'n');
	}


	if (isset($_REQUEST["wiki_creator_admin"]) && $_REQUEST["wiki_creator_admin"] == "on") {
		$tikilib->set_preference("wiki_creator_admin", 'y');

		$smarty->assign("wiki_creator_admin", 'y');
	} else {
		$tikilib->set_preference("wiki_creator_admin", 'n');

		$smarty->assign("wiki_creator_admin", 'n');
	}

	if (isset($_REQUEST["feature_wiki_templates"]) && $_REQUEST["feature_wiki_templates"] == "on") {
		$tikilib->set_preference("feature_wiki_templates", 'y');

		$smarty->assign("feature_wiki_templates", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_templates", 'n');

		$smarty->assign("feature_wiki_templates", 'n');
	}

	if (isset($_REQUEST["feature_wiki_multiprint"]) && $_REQUEST["feature_wiki_multiprint"] == "on") {
		$tikilib->set_preference("feature_wiki_multiprint", 'y');

		$smarty->assign("feature_wiki_multiprint", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_multiprint", 'n');

		$smarty->assign("feature_wiki_multiprint", 'n');
	}

	if (isset($_REQUEST["feature_wiki_pdf"]) && $_REQUEST["feature_wiki_pdf"] == "on") {
		$tikilib->set_preference("feature_wiki_pdf", 'y');

		$smarty->assign("feature_wiki_pdf", 'y');
	} else {
		$tikilib->set_preference("feature_wiki_pdf", 'n');

		$smarty->assign("feature_wiki_pdf", 'n');
	}

	if (isset($_REQUEST["feature_ranking"]) && $_REQUEST["feature_ranking"] == "on") {
		$tikilib->set_preference("feature_ranking", 'y');

		$smarty->assign("feature_ranking", 'y');
	} else {
		$tikilib->set_preference("feature_ranking", 'n');

		$smarty->assign("feature_ranking", 'n');
	}

	if (isset($_REQUEST["feature_listPages"]) && $_REQUEST["feature_listPages"] == "on") {
		$tikilib->set_preference("feature_listPages", 'y');

		$smarty->assign("feature_listPages", 'y');
	} else {
		$tikilib->set_preference("feature_listPages", 'n');

		$smarty->assign("feature_listPages", 'n');
	}

	if (isset($_REQUEST["feature_history"]) && $_REQUEST["feature_history"] == "on") {
		$tikilib->set_preference("feature_history", 'y');

		$smarty->assign("feature_history", 'y');
	} else {
		$tikilib->set_preference("feature_history", 'n');

		$smarty->assign("feature_history", 'n');
	}

	if (isset($_REQUEST["feature_mailin"]) && $_REQUEST["feature_mailin"] == "on") {
		$tikilib->set_preference("feature_mailin", 'y');

		$smarty->assign("feature_mailin", 'y');
	} else {
		$tikilib->set_preference("feature_mailin", 'n');

		$smarty->assign("feature_mailin", 'n');
	}

	if (isset($_REQUEST["feature_sandbox"]) && $_REQUEST["feature_sandbox"] == "on") {
		$tikilib->set_preference("feature_sandbox", 'y');

		$smarty->assign("feature_sandbox", 'y');
	} else {
		$tikilib->set_preference("feature_sandbox", 'n');

		$smarty->assign("feature_sandbox", 'n');
	}

	if (isset($_REQUEST["feature_backlinks"]) && $_REQUEST["feature_backlinks"] == "on") {
		$tikilib->set_preference("feature_backlinks", 'y');

		$smarty->assign("feature_backlinks", 'y');
	} else {
		$tikilib->set_preference("feature_backlinks", 'n');

		$smarty->assign("feature_backlinks", 'n');
	}

	if (isset($_REQUEST["feature_likePages"]) && $_REQUEST["feature_likePages"] == "on") {
		$tikilib->set_preference("feature_likePages", 'y');

		$smarty->assign("feature_likePages", 'y');
	} else {
		$tikilib->set_preference("feature_likePages", 'n');

		$smarty->assign("feature_likePages", 'n');
	}

	if (isset($_REQUEST["feature_userVersions"]) && $_REQUEST["feature_userVersions"] == "on") {
		$tikilib->set_preference("feature_userVersions", 'y');

		$smarty->assign("feature_userVersions", 'y');
	} else {
		$tikilib->set_preference("feature_userVersions", 'n');

		$smarty->assign("feature_userVersions", 'n');
	}

	if (isset($_REQUEST["wiki_uses_slides"]) && $_REQUEST["wiki_uses_slides"] == "on") {
		$tikilib->set_preference("wiki_uses_slides", 'y');
		$smarty->assign("wiki_uses_slides", 'y');
	} else {
		$tikilib->set_preference("wiki_uses_slides", 'n');
		$smarty->assign("wiki_uses_slides", 'n');
	}

}

if (isset($_REQUEST["wikisetprefs"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["maxVersions"])) {
		$tikilib->set_preference("maxVersions", $_REQUEST["maxVersions"]);
	}

	if (isset($_REQUEST["keep_versions"])) {
		$tikilib->set_preference("keep_versions", $_REQUEST["keep_versions"]);

		$smarty->assign('keep_versions', $_REQUEST["keep_versions"]);
	}
}

if (isset($_REQUEST["wikisetcopyright"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["wiki_feature_copyrights"]) && $_REQUEST["wiki_feature_copyrights"] == "on") {
		$tikilib->set_preference("wiki_feature_copyrights", 'y');

		$smarty->assign("wiki_feature_copyrights", 'y');
	} else {
		$tikilib->set_preference("wiki_feature_copyrights", 'n');

		$smarty->assign("wiki_feature_copyrights", 'n');
	}

	if (isset($_REQUEST["wikiLicensePage"])) {
		$tikilib->set_preference("wikiLicensePage", $_REQUEST["wikiLicensePage"]);

		$smarty->assign('wikiLicensePage', $_REQUEST["wikiLicensePage"]);
	}

	if (isset($_REQUEST["wikiSubmitNotice"])) {
		$tikilib->set_preference("wikiSubmitNotice", $_REQUEST["wikiSubmitNotice"]);

		$smarty->assign('wikiSubmitNotice', $_REQUEST["wikiSubmitNotice"]);
	}
}

if(isset($_REQUEST["wikisetwatch"])) {
	check_ticket('admin-inc-wiki');
  if(isset($_REQUEST["wiki_watch_author"]) && $_REQUEST["wiki_watch_author"]=="on") {
    $tikilib->set_preference("wiki_watch_author",'y'); 
    $smarty->assign("wiki_watch_author",'y');
  } else {
    $tikilib->set_preference("wiki_watch_author",'n');
    $smarty->assign("wiki_watch_author",'n');
  }

  if(isset($_REQUEST["wiki_watch_comments"]) && $_REQUEST["wiki_watch_comments"]=="on") {
    $tikilib->set_preference("wiki_watch_comments",'y'); 
    $smarty->assign("wiki_watch_comments",'y');
  } else {
    $tikilib->set_preference("wiki_watch_comments",'n');
    $smarty->assign("wiki_watch_comments",'n');
  }

  if(isset($_REQUEST["wiki_watch_editor"]) && $_REQUEST["wiki_watch_editor"]=="on") {
    $tikilib->set_preference("wiki_watch_editor",'y'); 
    $smarty->assign("wiki_watch_editor",'y');
  } else {
    $tikilib->set_preference("wiki_watch_editor",'n');
    $smarty->assign("wiki_watch_editor",'n');
  }
}

if ($feature_forums == 'y') {
	$commentslib = new Comments($dbTiki);
	$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
	$smarty->assign_by_ref("all_forums", $all_forums["data"]);
}

$tags = $adminlib->get_tags();
$smarty->assign_by_ref("tags", $tags);

$smarty->assign("maxVersions", $tikilib->get_preference("maxVersions", 0));
$smarty->assign("keep_versions", $tikilib->get_preference("keep_versions", 1));
ask_ticket('admin-inc-wiki');
?>
