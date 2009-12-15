<?php
/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/block.display.php,v 1.1 2007-09-06 13:50:20 mose Exp $
 *
 * Smarty plugin to display content only to some groups
 */

function smarty_block_display($params, $content, &$smarty)
{
		global $user, $userlib;
		$groups = split(',',$params['groups']);
		$userGroups = $userlib->get_user_groups($user);

		foreach ($groups as $gr) {
			$gr = trim($gr);
			if (substr($gr,0,1) == '-') {
				$nogr = substr($gr,1);
				if (in_array($gr,$userGroups)) {
					return '';
				}
			}
			if (in_array($gr,$userGroups)) {
				return $content;
			}
		}
}

?>
