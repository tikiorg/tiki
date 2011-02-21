<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * \brief Smarty plugin to display content only to some groups, friends or combination of all per specified user(s)
 * (if user is not specified, current user is used)
 * ex.: {display groups='Anonymous,-Registered,foo' friends=$f_42[ error='You may not see this item']}$f_1...$f_9///else///Become friend with $_42 first{/display}
 */

// this script may only be included - so it's better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  die;
}

function smarty_block_display($params, $content, &$smarty)
{
	global $prefs, $user, $userlib;
	
	$ok = true;
	if (!empty($params['groups'])) {
		$groups = explode(',',$params['groups']);
		$userGroups = $userlib->get_user_groups($user);
	}
	#$users = explode(',',$params['users']); // TODO users param support
	if (!empty($params['friends']) && $prefs['feature_friends'] == 'y') {
		$friends = explode(',', $params['friends']);
	}

	$content = explode('///else///', $content);
	
	if (!empty($params['error'])) {
		$errmsg = $params['error'];
	} elseif (empty($params['error']) && isset($friends)) {
		$errmsg = tra('You are not in group of friends to have the content of this block displayed for you');
	} elseif (empty($params['error']) && isset($groups)) {
		$errmsg = '';
	} else {
		$errmsg = tra('Smarty block.display.php: Missing error param');
	}
	
	$anon = false; // see the workaround to exclude Registered below

		foreach ($groups as $gr) {
			$gr = trim($gr);
			if ($gr == 'Anonymous') $anon = true;
			if (substr($gr,0,1) == '-') {
				$nogr = substr($gr,1);
				if ((in_array($nogr,$userGroups) && $nogr != 'Registered') or (in_array($nogr,$userGroups) && $nogr == 'Registered' && $anon == true)) {
					// workaround to display to Anonymous only if Registered excluded (because Registered includes Anonymous always)
					$ok = false;
					$anon = false;
				}
			} elseif (!in_array($gr,$userGroups) && $anon == false) {
				$ok = false;
			} else {
				$ok = true;
			}
		}
	
	/* now we check friends (if any) */
	if (!empty($friends)) {
		foreach ($friends as $friend) {
			if ($userlib->verify_friendship($user, $friend)) {
				$ok = true;
				break;
			} else {
				$ok = false;
			}
		}
	}
	/* is it ok ? */
	if (!$ok) {
		if (isset($content[1])) {
			return $content[1];
		} else {
			return $errmsg;
		}
	} else {
		return $content[0];
	}

}
