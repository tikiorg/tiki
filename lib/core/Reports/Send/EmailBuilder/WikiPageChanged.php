<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_page_changed events 
 */
class Reports_Send_EmailBuilder_WikiPageChanged extends Reports_Send_EmailBuilder_Abstract {
	public function getTitle()
	{
		return tr('Wiki pages updated:');
	}
	
	public function getOutput(array $change)
	{
		global $base_url;
		
		$output = "<u>".$change['data']['editUser']."</u> ".tra("edited the wikipage")." <a href=\"{$base_url}tiki-index.php?page=".$change['data']['pageName']."\">".$change['data']['pageName']."</a> (<a href=\"{$base_url}tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=".($change['data']['oldVer']+1)."&oldver=".$change['data']['oldVer']."\">".tra("this history")."</a>, <a href=\"{$base_url}tiki-pagehistory.php?page=".$change['data']['pageName']."&diff_style=sidediff&compare=Compare&newver=0&oldver=".$change['data']['oldVer']."\">".tra("all history")."</a>)";
				
		return $output;
	}
}