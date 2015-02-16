<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_Controller
{
	function action_list_users($input)
	{
		$groupIds = $input->groupIds->int();
		$offset = $input->offset->int();
		$maxRecords = $input->maxRecords->int();

		$groupFilter = '';

		if (is_array($groupIds)) {
			$table = TikiDb::get()->table('users_groups');
			$groupFilter = $table->fetchColumn(
				'groupName',
				array(
					'id' => $table->in($groupIds),
				)
			);
		}

		$result = TikiLib::lib('user')->get_users($offset, $maxRecords, 'login_asc', '', '', false, $groupFilter);

		return array(
			'result' => $result['data'],
			'count' => $result['cant'],
		);
	}

	function action_register($input)
	{
		global $https_mode, $prefs;
		if (!$https_mode && $prefs['https_login'] == 'required') {
			return array('result' => json_encode(array(tr("secure connection required"))));
		}

		$name = $input->name->string();
		$pass = $input->pass->string();
		$passAgain = $input->passAgain->string();
		$captcha = $input->captcha->arra();
		$antibotcode = $input->antibotcode->string();
		$email = $input->email->string();

		$regResult =  TikiLib::lib('registration')->register_new_user(
			array(
				'name' => $name,
				'pass' => $pass,
				'passAgain' => $passAgain,
				'captcha' => $captcha,
				'antibotcode' => $antibotcode,
				'email' => $email,
			)
		);

		return array(
            'result' => $regResult,
        );
	}

	/**
	 * Show user info popup
	 *
	 * @param $input JitFilter (username)
	 * @return array
	 */
	function action_info($input) {
		global $prefs, $user;

		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$sociallib = TikiLib::lib('social');

		$result = array(
			'fullname' => '',
			'gender' => '',
			'starHtml' => '',
			'country' => '',
			'distance' => '',
			'email' => '',
			'lastSeen' => '',
			'avatarHtml' => '',
			'error' => '',
			'shared_groups' => '',
		);

		if ($prefs['feature_community_mouseover'] == 'y' &&
				$userlib->get_user_preference($user, 'show_mouseover_user_info', 'y') == 'y' || $prefs['feature_friends'] == 'y') {

			$other_user = $input->username->email();
			$result['other_user'] = $other_user;

			if ($userlib->user_exists($other_user) &&
					($tikilib->get_user_preference($other_user, 'user_information', 'public') === 'public' || $user == $other_user || $prefs['feature_friends'] == 'y')) {

				$info = $userlib->get_user_info($other_user);

				$result['add_friend_button'] = '';
				$result['friendship'] = array();

				if ($prefs['feature_friends'] === 'y' && $user) {

					$friendship = array();

					if ($prefs['social_network_type'] === 'friend') {

						$friend = $this->isFriend($sociallib->listFriends($user), $other_user);
						if ($friend) {
							$friendship[] = array(
								'type' => 'friend',
								'label' => tra('Friend'),
								'remove' => tra('Remove Friend'),
							);
						} else {
							$result['add_friend_button'] = tra('Add Friend');
						}
					} else {
						$follower = $this->isFriend($sociallib->listFollowers($user), $other_user);
						$following = $this->isFriend($sociallib->listFollowers($other_user), $user);

						if ($follower) {
							$friendship[] = array(
								'type' => 'follower',
								'label' => tra('Following you'),
							);
							if ($prefs['social_network_type'] === 'follow_approval') {
								$friendship[count($friendship) - 1]['remove'] = tra('Remove Follower');
							}
						}
						if ($following) {
							$friendship[] = array(
								'type' => 'following',
								'label' => tra('You are following'),
								'remove' => tra('Stop Following'),
							);
						} else {
							$result['add_friend_button'] = tra('Follow');
						}
					}
					$incoming = $this->isFriend($sociallib->listIncomingRequests($user), $other_user);
					if ($incoming) {
						$friendship[] = array(
							'type' => 'incoming',
							'label' => tra('Awaiting your approval'),
							'remove' => tra('Refuse Request'),
							'add' => tra('Accept &amp; Add'),
						);
						if ($prefs['social_network_type'] === 'follow_approval') {
							$friendship[count($friendship) - 1]['approve'] = tra('Accept Request');
						}
						$result['add_friend_button'] = '';
					}
					$outgoing = $this->isFriend($sociallib->listOutgoingRequests($user), $other_user);
					if ($outgoing) {
						$friendship[] = array(
							'type' => 'outgoing',
							'label' => tra('Waiting for approval'),
							'remove' => tra('Cancel Request'),
						);
						$result['add_friend_button'] = '';
					}

					$result['friendship'] = $friendship;

					if ($user === $other_user) {
						$result['add_friend_button'] = '';	// can't befriend yourself
					}
				}

				if ($prefs['feature_community_mouseover_name'] == 'y') {
					$result['fullname'] = $userlib->clean_user($other_user);
				} else {
					$result['fullname'] = $other_user;
				}

				if ($prefs['feature_community_mouseover_gender'] == 'y' && $prefs['feature_community_gender'] == 'y') {
					$result['gender'] = $userlib->get_user_preference($other_user, 'gender');
					if ($result['gender'] == tr('Hidden')) {
						$result['gender'] = '';
					}
				}

				if ($prefs['feature_score'] == 'y') {
					if ($prefs['feature_community_mouseover_score'] == 'y' &&
							!empty($info['score']) && $other_user !== 'admin' && $other_user !== 'system' && $other_user !== 'Anonymous') {
						$result['starHtml'] = $tikilib->get_star($info['score']);
					} else {
						$result['starHtml'] = '';
					}
				}

				if ($prefs['feature_community_mouseover_country'] == 'y') {
					$result['country'] = $tikilib->get_user_preference($other_user, 'country', '');
					if ($result['country'] == tr('Other')) {
						$result['country'] = '';
					}
				}

				if ($prefs['feature_community_mouseover_distance'] == 'y') {
					$distance = TikiLib::lib('userprefs')->get_userdistance($other_user, $user);
					if ($distance) {
						$result['distance'] = $distance . ' '.tra('km');
					}
				}

				if ($prefs['feature_community_mouseover_email'] == 'y') {
					$email_isPublic = $tikilib->get_user_preference($other_user, 'email is public');
					if ($email_isPublic != 'n') {
						include_once ('lib/userprefs/scrambleEmail.php');
						$result['email'] = scrambleEmail($info['email'], $email_isPublic);
					//} elseif ($friend) {
					//	$result['email'] = $info['email']; // should friends see each other's emails whatever the settings? I doubt it (jb)
					}
				}

				if ($prefs['feature_community_mouseover_lastlogin'] == 'y') {
					$result['lastSeen'] = $info['currentLogin'] ? $info['currentLogin'] : $info['lastLogin'];
				}


				if ($prefs['feature_community_mouseover_picture'] == 'y') {
					$result['avatarHtml'] = $tikilib->get_user_avatar($other_user);
				}

				if ($user !== $other_user) { // should have a new pref?
					$theirGroups = TikiLib::lib('user')->get_user_groups($other_user);
					$myGroups = TikiLib::lib('user')->get_user_groups($user);
					$choiceGroups = TikiLib::lib('user')->get_groups_userchoice();
					$sharedGroups = array_intersect($theirGroups, $myGroups, $choiceGroups);

					$result['shared_groups'] = implode(', ', $sharedGroups);
				}
			}

		} else {
			$result['error'] = tra("You cannot see this user's data.");
			if ($user) {
				$result['error'] .= '<br>' .
					tra('You need to set your own info to be shown on mouseover.') . '<br>' .
					'<a href="tiki-user_preferences.php?cookietab=2">' . tra('Click here') . '</a>';
			} else {
				$result['error'] .= '<br>' . tra('You need to log in.');
			}
		}

		return $result;
	}

	/**
	 * @param $userlist array 'user' => username
	 * @param $user string
	 * @return bool
	 */
	private function isFriend($userlist, $user) {
		foreach($userlist as $v) {
			if (isset($v['user']) && $v['user'] === $user) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Admin user "perform with checked" but with no action selected
	 *
	 * @param $input
	 * @throws Services_Exception
	 */
	public function action_no_action($input)
	{
		throw new Services_Exception(tra('No action was selected. Please select an action before clicking OK.'), 409);
	}

	/**
	 * Admin user "perform with checked" action to remove selected users
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_remove_users($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm modal popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			$items = $params['checked'];
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Are you sure you want to delete the following user?');
				} else {
					$msg = tra('Are you sure you want to delete the following users?');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'customMsg' => $msg,
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = $input->asArray('items');
			$this->removeUsers($items);
			if (count($items) === 1) {
				$msg = tra('The following user has been deleted:');
			} else {
				$msg = tra('The following users have been deleted:');
			}
			return [
				'extra' => 'post',
				'feedback' => [
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => $items,
					'ajaxmsg' => $msg,
				],
			];
		}
	}

	/**
	 * Admin user "perform with checked" action to remove selected users and their user pages
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_remove_users_with_page($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			$items = $params['checked'];
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Are you sure you want to delete the following user and related user page?');
				} else {
					$msg = tra('Are you sure you want to delete the following users and their user pages?');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'customMsg' => $msg,
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = $input->asArray('items');
			$this->removeUsers($items, true);
			if (count($items) === 1) {
				$msg = tra('The following user and related user page have been deleted:');
			} else {
				$msg = tra('The following users and their user pages have been deleted:');
			}
			return [
				'extra' => 'post',
				'feedback' => [
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => $items,
					'ajaxmsg' => $msg,
				],
			];
		}
	}

	/**
	 * Admin user "perform with checked" action to remove selected users and send to banning page with users preselected
	 * for banning
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_remove_users_and_ban($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		Services_Exception_Disabled::check('feature_banning');
		Services_Exception_Denied::checkGlobal('admin_banning');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			$items = $params['checked'];
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Are you sure you want to delete and ban the following user?');
				} else {
					$msg = tra('Are you sure you want to delete and ban the following users?');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'customMsg' => $msg,
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = $input->asArray('items');
			$this->removeUsers($items, false, true);
			$mass_ban_ip = implode('|', $items);
			if (count($items) === 1) {
				$msg = tra('The following user has been deleted:');
				$timeoutmsg = tra('You will be redirected in a few seconds to a form where this user\'s IP has been preselected for banning.');
			} else {
				$msg = tra('The following users have been deleted:');
				$timeoutmsg = tra('You will be redirected in a few seconds to a form where these users\' IPs have been preselected for banning.');
			}
			return [
				'url' => 'tiki-admin_banning.php?mass_ban_ip_users=' . $mass_ban_ip,
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'modal_alert',
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => json_encode($items),
					'ajaxmsg' => $msg,
					'ajaxtimeoutMsg' => $timeoutmsg,
					'ajaxtimer' => 8,
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Admin user "perform with checked" action to remove selected users and their user pages and send to
	 * banning page with users preselected for banning
	 * 	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_NotFound
	 */
	function action_remove_users_with_page_and_ban($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		Services_Exception_Disabled::check('feature_banning');
		Services_Exception_Denied::checkGlobal('admin_banning');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			$items = $params['checked'];
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Are you sure you want to delete the following user and related user page and ban the user IP?');
				} else {
					$msg = tra('Are you sure you want to delete the following users and their user pages and ban their IPs?');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'customMsg' => $msg,
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = $input->asArray('items');
			$this->removeUsers($items, true, true);
			$mass_ban_ip = implode('|', $items);
			if (count($items) === 1) {
				$msg = tra('The following user and related user page have been deleted:');
				$timeoutmsg = tra('You will be redirected in a few seconds to a form where this user\'s IP has been preselected for banning.');
			} else {
				$msg = tra('The following users and their user pages have been deleted:');
				$timeoutmsg = tra('You will be redirected in a few seconds to a form where these users\' IPs have been preselected for banning.');
			}
			return [
				'url' => 'tiki-admin_banning.php?mass_ban_ip_users=' . $mass_ban_ip,
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'modal_alert',
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => json_encode($items),
					'ajaxmsg' => $msg,
					'ajaxtimeoutMsg' => $timeoutmsg,
					'ajaxtimer' => 8,
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Admin user "perform with checked" action to redirect to banning page with users preselected for banning
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_ban_ips($input)
	{
		Services_Exception_Disabled::check('feature_banning');
		Services_Exception_Denied::checkGlobal('admin_banning');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			$items = $params['checked'];
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Are you sure you want to ban the following user\'s IP?');
				} else {
					$msg = tra('Are you sure you want to ban the following users\' IPs?');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm ban'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'customMsg' => $msg,
						'items' => $items,
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = $input->asArray('items');
			$mass_ban_ip = implode('|', $items);
			if (count($items) === 1) {
				$msg = tra('You will be redirected in a few seconds to a form where the following user\'s IP has been preselected for banning:');
			} else {
				$msg = tra('You will be redirected in a few seconds to a form where the following users\' IPs have been preselected for banning:');
			}
			return [
				'url' => 'tiki-admin_banning.php?mass_ban_ip_users=' . $mass_ban_ip,
				'FORWARD' => [
					'controller' => 'utilities',
					'action' => 'modal_alert',
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => json_encode($items),
					'ajaxmsg' => $msg,
					'ajaxtimer' => 8,
					'modal' => '1'
				]
			];
		}
	}

	/**
	 * Admin user "perform with checked" action to assign user to or remove users from groups
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_manage_groups($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			if (count($params['checked']) > 0) {
				//remove from group icon clicked for a specific user
				if ($params['groupremove']) {
					$items[] = $params['groupremove'];
					return [
						'FORWARD' => [
							'controller' => 'access',
							'action' => 'confirm',
							'title' => tra('Please confirm removal from group'),
							'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
							'customMsg' => tr('Are you sure you want to remove user %0 from the following group:',
								$params['checked'][0]),
							'items' => $items,
							'extra' => [
								'add_remove' => 'remove',
								'user' => $params['checked'][0]
							],
							'ticket' => $check['ticket'],
							'modal' => '1',
						]
					];
				//selected users to be added or removed from selected groups groups
				} else {
					$all_groups = json_decode($params['all_groups'], true);
					$countgrps = count($all_groups) < 21 ? count($all_groups) : 20;
					$rows = ceil(count($params['checked']) / 8);
					global $prefs;
					$chosenpref = $prefs['jquery_ui_chosen'];
					return [
						'title' => tra('Change group assignments for selected users'),
						'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
						'all_groups' => $all_groups,
						'countgrps' => $countgrps,
						'chosenpref' => $chosenpref,
						'users' => $params['checked'],
						'rows' => $rows,
						'ticket' => $check['ticket'],
						'modal' => '1',
					];
				}
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//single user removed from a particular group
			if ($input->isArray('extra')) {
				$extra = $input->asArray('extra');
				$groups = $input->asArray('items');
				$users[] = $extra['user'];
				$add_remove = $extra['add_remove'];
			//selected users added or removed from selected groups
			} else {
				$groups = $input->asArray('checked_groups');
				$users = json_decode($input->offsetGet('users'), true);
				$add_remove = $input->add_remove->word();
			}
			if (!empty($users) && !empty($groups)) {
				global $user;
				$userlib = TikiLib::lib('user');
				$logslib = TikiLib::lib('logs');
				$userGroups = $userlib->get_user_groups_inclusion($user);
				$permname = 'group_' . $add_remove . '_member';
				$groupperm = Perms::get()->$permname;
				$userperm = Perms::get()->group_join;
				foreach ($users as $assign_user) {
					foreach ($groups as $group) {
						if ($groupperm || (array_key_exists($group, $userGroups) && $userperm)) {
							if ($add_remove === 'add') {
								$res = $userlib->assign_user_to_group($assign_user, $group);
								if ($res) {
									$logmsg = sprintf(tra('%s %s assigned to %s %s.'), tra('user'), $assign_user, tra('group'), $group);
									$logslib->add_log('adminusers', $logmsg, $user);
								} else {
									throw new Services_Exception(tra('An error occurred. The group assignment failed'), 400);
								}
							} elseif ($add_remove === 'remove') {
								$userlib->remove_user_from_group($assign_user, $group);
								$logmsg = sprintf(tra('%s %s removed from %s %s.'), tra('user'), $assign_user,
									tra('group'), $group);
								$logslib->add_log('adminusers', $logmsg, $user);
							}
						} else {
							throw new Services_Exception_Denied();
						}
					}
				}
				$msg = count($users) === 1 ? tra('The following user:') : tra('The following users:');
				$verb = $add_remove == 'add' ? 'added to' : 'removed from';
				$grpcnt = count($groups) === 1 ? 'group' : 'groups';
				$toMsg = tr('Have been %0 the following %1:', tra($verb), tra($grpcnt));
				return [
					'extra' => 'post',
					'feedback' => [
						'ajaxtype' => 'feedback',
						'ajaxheading' => tra('Success'),
						'ajaxitems' => $users,
						'ajaxmsg' => $msg,
						'ajaxtoMsg' => $toMsg,
						'ajaxtoList' => $groups,
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No groups were selected. Please select one or more groups.'), 409);
			}
		}
	}

	function action_default_groups($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		Services_Exception_Denied::checkGlobal('group_add_member');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			if (count($params['checked']) > 0) {
				$all_groups = json_decode($params['all_groups'], true);
				$rows = ceil(count($params['checked']) / 8);
				return [
					'title' => tra('Set default group for selected users'),
					'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
					'all_groups' => $all_groups,
					'users' => $params['checked'],
					'rows' => $rows,
					'ticket' => $check['ticket'],
					'modal' => '1',
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$groups = $input->asArray('checked_groups');
			$users = json_decode($input->offsetGet('users'), true);
			if (!empty($users) && !empty($groups)) {
				global $user;
				$userlib = TikiLib::lib('user');
				$logslib = TikiLib::lib('logs');
				$userGroups = $userlib->get_user_groups_inclusion($user);
				$groupperm = Perms::get()->group_add_member;
				$userperm = Perms::get()->group_join;
				foreach ($users as $assign_user) {
					foreach ($groups as $group) {
						if ($groupperm || (array_key_exists($group, $userGroups) && $userperm)) {
							$userlib->set_default_group($assign_user, $group);
							$logmsg = sprintf(tra('group %s set as the default group for user %s.'),
								$group, $assign_user);
							$logslib->add_log('adminusers', $logmsg, $user);
						}
					}
				}
				$msg = count($users) === 1 ? tra('For the following user:') : tra('For the following users:');
				$toMsg = tra('The following group has been set as the default group:');
				return [
					'extra' => 'post',
					'feedback' => [
						'ajaxtype' => 'feedback',
						'ajaxheading' => tra('Success'),
						'ajaxitems' => $users,
						'ajaxmsg' => $msg,
						'ajaxtoMsg' => $toMsg,
						'ajaxtoList' => $groups,
						'modal' => '1',
					]
				];
			} else {
				throw new Services_Exception(tra('No groups were selected. Please select one or more groups.'), 409);
			}
		}
	}

	function action_email_wikipage($input)
	{
		Services_Exception_Disabled::check('feature_wiki');
		Services_Exception_Denied::checkGlobal('admin_users');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			parse_str($input->offsetGet('params'), $params);
			if (count($params['checked']) > 0) {
				$rows = ceil(count($params['checked']) / 8);
				return [
					'title' => tra('Send wiki page content by email to selected users'),
					'confirmAction' => 'tiki-user-' . $input->offsetGet('action'),
					'users' => $params['checked'],
					'rows' => $rows,
					'ticket' => $check['ticket'],
					'modal' => '1',
				];
			} else {
				throw new Services_Exception(tra('No users were selected. Please select one or more users.'), 409);
			}
			//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$wikiTpl = $input->offsetGet('wikiTpl');
			$tikilib = TikiLib::lib('tiki');
			$pageinfo = $tikilib->get_page_info($wikiTpl);
			if (!$pageinfo) {
				throw new Services_Exception_NotFound(tra('Page not found'));
			}
			if (empty($pageinfo['description'])) {
				throw new Services_Exception(tra('The page does not have a description, which is mandatory to perform this action.'));
			}
			$bcc = $input->offsetGet('bcc');
			include_once ('lib/webmail/tikimaillib.php');
			$mail = new TikiMail();
			if (!empty($bcc)) {
				if (!validate_email($bcc)) {
					throw new Services_Exception(tra('Invalid bcc email address.'));
				}
				$mail->setBcc($bcc);
				$bccmsg = tr('and blind copied to %0', $bcc);
			}
			$foo = parse_url($_SERVER['REQUEST_URI']);
			$machine = $tikilib->httpPrefix(true) . dirname($foo['path']);
			$machine = preg_replace('!/$!', '', $machine); // just in case
			global $smarty, $user;
			$smarty->assign_by_ref('mail_machine', $machine);

			$users = json_decode($input->offsetGet('users'), true);
			$userlib = TikiLib::lib('user');
			$logslib = TikiLib::lib('logs');
			foreach ($users as $mail_user) {
				$smarty->assign_by_ref('user', $mail_user);
				$mail->setUser($mail_user);
				$mail->setSubject($pageinfo['description']);
				$text = $smarty->fetch('wiki:' . $wikiTpl);
				if (empty($text)) {
					throw new Services_Exception(tra('The template page has no text or it cannot be extracted.'));
				}
				$mail->setHtml($text);
				if (!$mail->send($userlib->get_user_email($mail_user))) {
					$errormsg = tra('Unable to send mail');
					if (Perms::get()->admin) {
						$mailerrors = print_r($mail->errors, true);
						$errormsg .= $mailerrors;
					}
					throw new Services_Exception($errormsg);
				} else {
					if (!empty($bcc))
						$logmsg = sprintf(tra('Mail sent to user %s'), $mail_user);
					$logmsg = !empty($bccmsg) ? $logmsg . ' ' . $bccmsg : $logmsg;
					if (!empty($msg)) {
						$logslib->add_log('adminusers', $logmsg, $user);
					}
				}
				$smarty->assign_by_ref('user', $user);
			}
			$msg = count($users) === 1 ? tr('The page %0 has been emailed to the following user:', $wikiTpl)
				: tr('The page %0 has been emailed to the following users:', $wikiTpl);
			$toMsg = !empty($bcc) ? tr('And blind copied to %0.', $bcc) : '';
			return [
				'extra' => 'post',
				'feedback' => [
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Success'),
					'ajaxitems' => $users,
					'ajaxmsg' => $msg,
					'ajaxtoMsg' => $toMsg,
					'modal' => '1',
				]
			];
		}
	}


	private function removeUsers(array $users, $page = false)
	{
		global $user;
		foreach ($users as $deleteuser) {
			if ($deleteuser != 'admin') {
				$userlib = TikiLib::lib('user');
				$res = $userlib->remove_user($deleteuser);
				if ($res === true) {
					$logslib = TikiLib::lib('logs');
					$logslib->add_log('adminusers', sprintf(tra('Deleted account %s'), $deleteuser), $user);
				} else {
					throw new Services_Exception_NotFound(tr('An error occurred, user %0 could not be deleted',
						$deleteuser));
				}
				if ($page) {
					global $prefs;
					$page = $prefs['feature_wiki_userpage_prefix'] . $deleteuser;
					Services_Exception_Denied::checkObject('remove', 'wiki page', $page);
					$tikilib = TikiLib::lib('tiki');
					$res = $tikilib->remove_all_versions($page);
					if ($res !== true) {
						throw new Services_Exception_NotFound(tr('An error occurred, user %0 could not be deleted',
							$deleteuser));
					}
				}
			}
		}
		return true;
	}
}