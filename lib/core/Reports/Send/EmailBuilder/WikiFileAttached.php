<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Class for wiki_file_attached events 
 */
class Reports_Send_EmailBuilder_WikiFileAttached extends Reports_Send_EmailBuilder_Abstract {
	public function getTitle()
	{
		return tr('New attachments in wiki pages:');
	}
	
	public function getOutput(array $change)
	{
		global $base_url;

		$output = "<u>".$change['user']."</u> ".tra('uploaded the file') . " <a href=\"{$base_url}tiki-download_wiki_attachment.php?attId=".$change['data']['attId']."\">".$change['data']['filename']."</a> ".tra("onto")." <a href=\"{$base_url}tiki-index.php?page=".$change['data']['pageName']."\">".$change['data']['pageName']."</a>.";
				
		return $output;
	}
}