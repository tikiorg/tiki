<?php
   require_once('tiki-setup.php');
   include_once("lib/hawhaw/hawtikilib.php");
/*
   require("lib/hawhaw/hawhaw.inc");
   require("lib/hawhaw/hawiki_cfg.inc");
   require("lib/hawhaw/hawiki.inc");
   error_reporting(E_ALL & ~E_NOTICE);
*/   
   $TikiPage = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
   $title = new HAW_text(HAWIKI_TITLE, HAW_TEXTFORMAT_BOLD);
   $TikiPage->add_text($title);

   $linkset = new HAW_linkset();

   //$forum = new HAW_link("Forum","forum.php");
   //$linkset->add_link($forum);

   $wiki = new HAW_link(hawtra("Wiki"),"tiki-index.php?mode=mobile");
   if($feature_wiki == 'y') {
     $linkset->add_link($wiki);
   }

   $blogs = new HAW_link(hawtra("Blogs"),"tiki-list_blogs.php?mode=mobile");
   if($feature_blogs == 'y') {
     $linkset->add_link($blogs);
   }

   $about = new HAW_link(hawtra("About"),"tiki-index.php?page=AboutHawiki&mode=mobile");
   $linkset->add_link($about);

   $TikiPage->add_linkset($linkset);
   $TikiPage->create_page();
?>
