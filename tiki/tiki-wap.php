<?php
   require_once('tiki-setup.php');
   require("lib/hawhaw/hawhaw.inc");
   error_reporting(E_WARNING);
   $DemoPage = new HAW_deck("Tiki", HAW_ALIGN_CENTER);
   $title = new HAW_text("Tiki", HAW_TEXTFORMAT_BOLD);
   $DemoPage->add_text($title);
   //$link1 = new HAW_link("Forum","forum.php");
   $link2 = new HAW_link("Wiki","tiki-index.php?mode=wap");
   $linkset = new HAW_linkset();
   //$linkset->add_link($link1);
   $linkset->add_link($link2);
   $DemoPage->add_linkset($linkset);
   $DemoPage->create_page();
?>
