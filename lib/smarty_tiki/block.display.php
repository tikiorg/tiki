<?php
/**
 * $Id: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/block.display.php,v 1.1 2007-09-06 13:50:20 mose Exp $
 *
 * \brief Smarty plugin to display content only to some groups, friends or combination of all per specified user(s)
 * (if user is not specified, current user is used)
 * ex.: {display groups=Anonymous,-Registered,foo}...{/display}
 */

function smarty_block_display($params, $content, &$smarty)
{
	global $prefs, $user, $userlib;
	
	$groups = explode(',',$params['groups']);
	#$users = explode(',',$params['users']); // TODO users param support
	if (!empty($params['friends']) && $prefs['feature_friends'] == 'y') {
		$friends = explode(',', $params['friends']);
	}

	$content = explode('///else///', $content);
	
	if (!empty($params['error'])) {
		$errmsg = $params['error'];
	} elseif (empty($params['error']) && isset($friends)) {
		$errmsg = tra('You are not in group of friends to have this displayed for you');
	} elseif (empty($params['error']) && isset($groups)) {
		$errmsg = '';
	} else {
		$errmsg = tra('Smarty block.display.php: Missing error param');
	}
	$ok = false;
	$anon = false; // see the workaround to exclude Registered below
	
	$userGroups = $userlib->get_user_groups($user);

		foreach ($groups as $gr) {
			$gr = trim($gr);
			if ($gr == 'Anonymous') $anon = true;
			if (substr($gr,0,1) == '-') {
				$nogr = substr($gr,1);
				if (in_array($nogr,$userGroups && $nogr != 'Registered') or (in_array($nogr,$userGroups) && $nogr == 'Registered' && $anon == true)) {
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
