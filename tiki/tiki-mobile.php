<?php
   require_once('tiki-setup.php');
   require("lib/hawhaw/hawhaw.inc");
   error_reporting(E_ALL & ~E_NOTICE);
   $TikiPage = new HAW_deck("Tiki", HAW_ALIGN_CENTER);
   $title = new HAW_text("Tiki", HAW_TEXTFORMAT_BOLD);
   $TikiPage->add_text($title);
   //$link1 = new HAW_link("Forum","forum.php");
   $link2 = new HAW_link("Wiki","tiki-index.php?mode=mobile");
   $link3 = new HAW_link("Blogs","tiki-list_blogs.php?mode=mobile");
   $linkset = new HAW_linkset();
   //$linkset->add_link($link1);
   if($feature_wiki == 'y') {
     $linkset->add_link($link2);
   }
   if($feature_blogs == 'y') {
     $linkset->add_link($link3);
   }
   $TikiPage->add_linkset($linkset);
   $TikiPage->create_page();
?>
