<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_Controller
{

	/**
	 * @var UsersLib
	 */
	private $lib;

	/**
	 * @var TikiAccessLib
	 */
	private $access;

	function setUp()
	{
		$this->lib = TikiLib::lib('user');
		$this->access = TikiLib::lib('access');
	}


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

		$result = $this->lib->get_users($offset, $maxRecords, 'login_asc', '', '', false, $groupFilter);

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

		$name = $input->name->text();
		$pass = $input->pass->text();
		$passAgain = $input->passAgain->text();
		$captcha = $input->captcha->asArray();
		$antibotcode = $input->antibotcode->text();
		$email = $input->email->text();

		if ($prefs['user_unique_email'] == 'y' && TikiLib::lib('user')->get_user_by_email($email)) {
			$errormsg = tra('We were unable to create your account because this email is already in use.');
			throw new Services_Exception($errormsg);
		}

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
				$this->lib->get_user_preference($user, 'show_mouseover_user_info', 'y') == 'y' || $prefs['feature_friends'] == 'y') {

			$other_user = $input->username->email();
			$result['other_user'] = $other_user;

			if ($this->lib->user_exists($other_user) &&
					($tikilib->get_user_preference($other_user, 'user_information', 'public') === 'public' || $user == $other_user || $prefs['feature_friends'] == 'y')) {

				$info = $this->lib->get_user_info($other_user);

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
					$result['fullname'] = $this->lib->clean_user($other_user);
				} else {
					$result['fullname'] = $other_user;
				}

				if ($prefs['feature_community_mouseover_gender'] == 'y' && $prefs['feature_community_gender'] == 'y') {
					$result['gender'] = $this->lib->get_user_preference($other_user, 'gender');
					if ($result['gender'] == tr('Hidden')) {
						$result['gender'] = '';
					}
				}

				if ($prefs['feature_score'] == 'y') {
					$info['score'] =  TikiLib::lib('score')->get_user_score($other_user);
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
						$result['email'] = TikiMail::scrambleEmail($info['email'], $email_isPublic);
					//} elseif ($friend) {
					//	$result['email'] = $info['email']; // should friends see each other's emails whatever the settings? I doubt it (jb)
					}
				}

				if ($prefs['feature_community_mouseover_lastlogin'] == 'y') {
					$result['lastSeen'] = $info['currentLogin'] ? $info['currentLogin'] : null;
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
					tra('You need to set "Show my information on mouseover".') . '<br>' .
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
		Services_Utilities::modalException(tra('No action was selected. Please select an action before clicking OK.'));
	}

	/**
	 * Admin user "perform with checked" action to remove selected users
	 *
	 * @param $input JitFilter
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
			$items = $input->asArray('checked');
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Delete the following user?');
				} else {
					$msg = tra('Delete the following users?');
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'modal' => '1',
					'confirmAction' => $input->action->word(),
					'confirmController' => 'user',
					'customMsg' => $msg,
					'confirmButton' => tra('Delete'),
					'items' => $items,
					'extra' => ['referer' => $referer],
					'ticket' => $check['ticket'],
					'confirm' => 'y',
				];
			} else {
				Services_Utilities::modalException(tra('No users were selected. Please select one or more users.'));
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//delete user
			$items = json_decode($input['items'], true);

			// maybe delete page as well?
			$remove_pages = ! empty($input->remove_pages->word());

			// check for trackers
			$remove_items = $input->remove_items->text();
			$remove_items = $remove_items ? explode(',', $remove_items) : [];

			// file galleries?
			$remove_files = ! empty($input->remove_files->word());

			// do the deleting...
			$extra = json_decode($input['extra'], true);
			$del = $this->removeUsers($items, $remove_pages, $remove_items, $remove_files, $extra['referer']);

			if ($del) {
				//prepare feedback
				if (count($items) === 1) {
					$msg = tra('The following user has been deleted:');
					$toMsg = tra('Submit form below to ban this user.');
				} else {
					$msg = tra('The following users have been deleted:');
					$toMsg = tra('Submit form below to ban these users.');
				}
				$feedback = [
					'tpl' => 'action',
					'mes' => $msg,
					'items' => $items,
				];

				//redirect to banning page if selected
				if ($input->ban_users->word()) {
					$feedback['toMsg'] = $toMsg;
					Feedback::success($feedback, 'session');
					$url = 'tiki-admin_banning.php?mass_ban_ip_users=' . implode('|', $items);
					global $prefs;
					if ($prefs['javascript_enabled'] !== 'y') {
						$this->access->redirect($url);
					} else {
						return ['url' => $url];
					}
					//refresh page
				} else {
					Feedback::success($feedback, 'session');
					return Services_Utilities::refresh($extra['referer']);
				}
			}
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
			$items = $input->asArray('checked');
			if (count($items) > 0) {
				if (count($items) === 1) {
					$msg = tra('Ban the following user\'s IP?');
					$help = tra('Clicking Ban will redirect you to a form where this user\'s is preselected for IP banning.');
				} else {
					$msg = tra('Ban the following users\' IPs?');
					$help = tra('Clicking Ban will redirect you to a form where these users\' are preselected for IP banning.');
				}
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'confirmAction' => $input->action->word(),
						'confirmController' => 'user',
						'customMsg' => $msg,
						'confirmButton' => tra('Ban'),
						'items' => $items,
						'ticket' => $check['ticket'],
						'help' => $help,
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No users were selected. Please select one or more users.'));
			}
		//after confirm submit - redirect to banning page with users preselected
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$url = 'tiki-admin_banning.php?mass_ban_ip_users=' . implode('|', $items);
			$feedback = ['mes' => tr('See highlighted section in the form below for users you have selected for banning.'),];
			Feedback::note($feedback, 'session');
			return Services_Utilities::redirect($url);
		}
	}

	/**
	 * Admin user "perform with checked" action to assign user to or remove users from groups
	 *
	 * @param JitFilter $input
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
			$selected = $input->asArray('checked');
			if (count($selected) > 0) {
				//provide redirect if js is not enabled
				if (!empty($input['anchor'])) {
					$referer = $_SERVER['HTTP_REFERER'];
				} else {
					$referer = Services_Utilities::noJsPath();
				}
				//remove from group icon clicked for a specific user
				if (isset($input['groupremove'])) {
					$items = $input->asArray('groupremove');
					return [
						'FORWARD' => [
							'controller' => 'access',
							'action' => 'confirm',
							'confirmAction' => $input->action->word(),
							'confirmController' => 'user',
							'customMsg' => tr('Remove user %0 from the following group?',
								$selected[0]),
							'items' => $items,
							'extra' => [
								'add_remove'    => 'remove',
								'user'          => $selected[0],
								'referer'       => $referer,
								'anchor'        => $input->anchor->striptags()
							],
							'ticket' => $check['ticket'],
							'modal' => '1',
						]
					];
				//selected users to be added or removed from selected groups groups
				} else {
					$all_groups = $this->lib->list_all_groups();
					$countgrps = count($all_groups) < 21 ? count($all_groups) : 20;
					$users = $input->asArray('checked');
					if (count($users) == 1) {
						$customMsg = tra('For this user:');
						$userGroups = TikiLib::lib('tiki')->get_user_groups($users[0]);
					} else {
						$customMsg = tra('For these selected users:');
						$userGroups = '';
					}
					return [
						'title' => tra('Change group assignments for selected users'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'user',
						'customMsg' => $customMsg,
						'all_groups' => $all_groups,
						'countgrps' => $countgrps,
						'items' => $users,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'modal' => '1',
						'confirm' => 'y',
						'userGroups' => str_replace(['\'','&'], ['%39;','%26'], json_encode($userGroups)),
					];
				}
			} else {
				Services_Utilities::modalException(tra('No users were selected. Please select one or more users.'));
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$extra = json_decode($input['extra'], true);

			// default group?
			$defaultGroup = $input->default_group->text();

			//selected users added or removed from selected groups
			if (isset($input['checked_groups'])) {
				$groups = $input->asArray('checked_groups');
				$users = json_decode($input['items'], true);
				$add_remove = $input->add_remove->word();
			//single user removed from a particular group
			} elseif (!empty($extra['add_remove'])) {
				$groups = json_decode($input['items'], true);
				$users[] = $extra['user'];
				$add_remove = $extra['add_remove'];
			} else if ($defaultGroup) {
				$users = json_decode($input['items'], true);
				$groups = [];
			}
			if (!empty($users) && (!empty($groups) || $defaultGroup)) {
				global $user;
				$logslib = TikiLib::lib('logs');
				$userGroups = $this->lib->get_user_groups_inclusion($user);
				$permname = 'group_' . $add_remove . '_member';
				$groupperm = Perms::get()->$permname;
				$userperm = Perms::get()->group_join;
				foreach ($users as $assign_user) {
					foreach ($groups as $group) {
						if ($groupperm || (array_key_exists($group, $userGroups) && $userperm)) {
							if ($add_remove === 'add') {
								$res = $this->lib->assign_user_to_group($assign_user, $group);
								if ($res) {
									$logmsg = sprintf(tra('%s %s assigned to %s %s.'), tra('user'), $assign_user, tra('group'), $group);
									$logslib->add_log('adminusers', $logmsg, $user);
								} else {
									Feedback::error(['mes' => tra('An error occurred. The group assignment failed.')],
										'session');
									return Services_Utilities::closeModal($extra['referer']);
								}
							} elseif ($add_remove === 'remove') {
								$this->lib->remove_user_from_group($assign_user, $group);
								$logmsg = sprintf(tra('%s %s removed from %s %s.'), tra('user'), $assign_user,
									tra('group'), $group);
								$logslib->add_log('adminusers', $logmsg, $user);
							}
						} else {
							Feedback::error(['mes' => tra('Permission denied')], 'session');
							return Services_Utilities::closeModal($extra['referer']);
						}
					}

					if ($defaultGroup) {
						$this->lib->set_default_group($assign_user, $defaultGroup);
					}

				}
				//prepare feedback
				if (count($users) === 1) {
					$msg = tra('The following user:');
					$helper = 'Has';
				} else {
					$msg = tra('The following users:');
					$helper = 'Have';
				}
				if ($defaultGroup && empty($groups)) {
					$groups[] = $defaultGroup;
					$toMsg = tr('%0 had the following group set as default:', tra($helper));
				} else {
					$verb = $add_remove == 'add' ? 'added to' : 'removed from';
					$grpcnt = count($groups) === 1 ? 'group' : 'groups';
					$toMsg = tr('%0 been %1 the following %2:', tra($helper), tra($verb), tra($grpcnt));
				}
				$feedback = [
					'tpl' => 'action',
					'mes' => $msg,
					'items' => $users,
					'toMsg' => $toMsg,
					'toList' => $groups,
				];
				Feedback::success($feedback, 'session');
				//return to page
				if (!empty($extra['anchor'])) {
					return Services_Utilities::redirect($extra['referer'] . $extra['anchor']);
				} else {
					return Services_Utilities::refresh($extra['referer']);
				}
			} else {
				Feedback::error(['mes' => tra('No groups were selected. Please select one or more groups.')], 'session');
				return Services_Utilities::closeModal($extra['referer']);
			}
		}
	}

	/**
	 * Admin user "perform with checked" action to assign the default group for a user or users
	 *
	 * @param JitFilter $input
	 * @return array
	 * @throws Exception
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_default_groups($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		Services_Exception_Denied::checkGlobal('group_add_member');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			$users = $input->asArray('checked');
			if (count($users) > 0) {
				$all_groups = $this->lib->list_all_groups();
				$all_groups = array_combine($all_groups, $all_groups);
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm_select',
						'title' => tra('Set default group for selected users'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'user',
						'customMsg' => tra('For these selected users:'),
						'toList' => $all_groups,
						'toMsg' => tra('Make this the default group:'),
						'items' => $users,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No users were selected. Please select one or more users.'));
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$groups = isset($input['checked_groups']) ? $input->asArray('checked_groups')
				: $input->asArray('toId');
			$users = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			if (!empty($users) && !empty($groups)) {
				//perform action
				global $user;
				$logslib = TikiLib::lib('logs');
				$userGroups = $this->lib->get_user_groups_inclusion($user);
				$groupperm = Perms::get()->group_add_member;
				$userperm = Perms::get()->group_join;
				foreach ($users as $assign_user) {
					foreach ($groups as $group) {
						if ($groupperm || (array_key_exists($group, $userGroups) && $userperm)) {
							$this->lib->set_default_group($assign_user, $group);
							$logmsg = sprintf(tra('group %s set as the default group for user %s.'),
								$group, $assign_user);
							$logslib->add_log('adminusers', $logmsg, $user);
						}
					}
				}
				//prepare feedback
				$msg = count($users) === 1 ? tra('For the following user:') : tra('For the following users:');
				$toMsg = tra('The following group has been set as the default group:');
				$feedback = [
					'tpl' => 'action',
					'mes' => $msg,
					'items' => $users,
					'toMsg' => $toMsg,
					'toList' => $groups,
				];
				Feedback::success($feedback, 'session');
				//return to page
				return Services_Utilities::refresh($extra['referer']);
			} else {
				Feedback::error(['mes' => tra('No groups were selected. Please select one or more groups.')], 'session');
				return Services_Utilities::closeModal($extra['referer']);
			}
		}
	}

	/**
	 * Admin user "perform with checked" action to email a wiki page to a user
	 *
	 * @param $input JitFilter
	 * @return array
	 * @throws Exception
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 * @throws Services_Exception_Disabled
	 * @throws Services_Exception_NotFound
	 */
	function action_email_wikipage($input)
	{
		Services_Exception_Disabled::check('feature_wiki');
		Services_Exception_Denied::checkGlobal('admin_users');
		$check = Services_Exception_BadRequest::checkAccess();
		//first pass - show confirm popup
		if (!empty($check['ticket'])) {
			$users = $input->asArray('checked');
			if (count($users) > 0) {
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'title' => tra('Send wiki page content by email to selected users'),
					'confirmAction' => $input->action->word(),
					'confirmController' => 'user',
					'customMsg' => tra('For these selected users:'),
					'items' => $users,
					'extra' => ['referer' => $referer],
					'ticket' => $check['ticket'],
					'modal' => '1',
					'confirm' => 'y',
				];
			} else {
				Services_Utilities::modalException(tra('No users were selected. Please select one or more users.'));
			}
		//after confirm submit - perform action and return success feedback
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$wikiTpl = $input['wikiTpl'];
			$tikilib = TikiLib::lib('tiki');
			$pageinfo = $tikilib->get_page_info($wikiTpl);
			$extra = json_decode($input['extra'], true);
			if (!$pageinfo) {
				Feedback::error(tra('Page not found'), 'session');
				return Services_Utilities::closeModal($extra['referer']);
			}
			if (empty($pageinfo['description'])) {
				Feedback::error(tra('The page does not have a description, which is mandatory to perform this action.'),
					'session');
				return Services_Utilities::closeModal($extra['referer']);
			}
			$bcc = $input['bcc'];
			include_once ('lib/webmail/tikimaillib.php');
			$mail = new TikiMail();
			if (!empty($bcc)) {
				if (!validate_email($bcc)) {
					Feedback::error(tra('Invalid bcc email address'), 'session');
					return Services_Utilities::closeModal($extra['referer']);
				}
				$mail->setBcc($bcc);
				$bccmsg = tr('and blind copied (bcc) to %0', $bcc);
			}
			$foo = parse_url($_SERVER['REQUEST_URI']);
			$machine = $tikilib->httpPrefix(true) . dirname($foo['path']);
			$machine = preg_replace('!/$!', '', $machine); // just in case
			global $user;
			$smarty = TikiLib::lib('smarty');
			$smarty->assign_by_ref('mail_machine', $machine);

			$users = json_decode($input['items'], true);
			$logslib = TikiLib::lib('logs');
			foreach ($users as $mail_user) {
				$smarty->assign_by_ref('user', $mail_user);
				$mail->setUser($mail_user);
				$mail->setSubject($pageinfo['description']);
				$text = $smarty->fetch('wiki:' . $wikiTpl);
				if (empty($text)) {
					Feedback::error(tra('The template page has no text or the text cannot be extracted.'), 'session');
					return Services_Utilities::closeModal($extra['referer']);
				}
				$mail->setHtml($text);
				if (!$mail->send($this->lib->get_user_email($mail_user))) {
					$errormsg = tra('Unable to send mail');
					if (Perms::get()->admin) {
						$mailerrors = print_r($mail->errors, true);
						$errormsg .= $mailerrors;
					}
					Feedback::error($errormsg, 'session');
					return Services_Utilities::closeModal($extra['referer']);
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
			//prepare feedback
			$msg = count($users) === 1 ? tr('The page %0 has been emailed to the following user:', $wikiTpl)
				: tr('The page %0 has been emailed to the following users:', $wikiTpl);
			$toMsg = !empty($bcc) ? tr('And blind copied to %0.', $bcc) : '';
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $users,
				'toMsg' => $toMsg,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	function action_send_message($input) {
		global $user;
		$userlib = TikiLib::lib('user');
		$referer = Services_Utilities::noJsPath();
		//ensures a user was selected to send a message to.
		if (empty($input->userwatch->text())) {
			Feedback::error(tra('No user was selected.'), 'session');
			return Services_Utilities::closeModal($referer);
		}
		//sets default priority for the message to 3 if no priority was given
		if (!empty($input->priority->text())) {
			$priority = $input->priority->text();
		} else {
			$priority = 3;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($input->subject->text()) && empty($input->body->text())) {
				Feedback::error(tra('Message not sent - no subject or body.'), 'session');
			} else {
				//if message is successfully sent
				if (TikiLib::lib('message')->post_message($input->userwatch->text(), $user, $input->to->text(), '',
					$input->subject->text(), $input->body->text(), $priority, '', isset($input->replytome) ? 'y' : '',
					isset($input->bccme) ? 'y' : ''))
				{
					$message = tr('Your message was successfully sent to %0,',
						$userlib->clean_user($input->userwatch->text()));
					Feedback::success($message, 'session');
				} else {
					Feedback::error(tra('An error occurred, please check your mail settings and try again.'), 'session');
				}
			}
			return Services_Utilities::closeModal($referer);
		} else {
			return array(
				'title' => tra("Send Me a Message"),
				'userwatch' => $input->userwatch->text(),
				'priority' => $priority,
				'referer' => $referer
			);
		}
	}

	function action_get_message_count($input) {
		global $user;

		$sinceDate = null;
		if ($input->sinceDate->int()) {
			$sinceDate = $input->sinceDate->int();
		}

		$unread = null;
		if ($input->unread->bool()) {
			$unread = $input->unread->bool();
		}
		$messagelib = TikiLib::lib("message");
		return (int) $messagelib->count_messages($user, 'messages', $unread, $sinceDate);
	}

	function action_invite_tempuser($input)
	{
		Services_Exception_Denied::checkGlobal('admin_users');
		$emails = $input->tempuser_emails->text();
		$groups = $input->tempuser_groups->text();
		$expiry = $input->tempuser_expiry->int();
		$prefix = $input->tempuser_prefix->text();
		$path = $input->tempuser_path->text();
		$referer = Services_Utilities::noJsPath();

		if (empty($prefix)) {
			$prefix = 'guest';
		}
		if (empty($path)) {
			$path = 'index.php';
		}

		$groups = explode(',', $groups);
		$emails = explode(',', $emails);
		$groups = array_map('trim', $groups);
		$emails = array_map('trim', $emails);
		if ($expiry > 0) {
			$expiry = $expiry * 3600 * 24; //translate day input to seconds
		} else if ($expiry != -1) {
			Feedback::error(tra('Please specify validity period'), 'session');
			Services_Utilities::sendFeedback($referer);
		}

		foreach($groups as $grp) {
			if (!TikiLib::lib('user')->group_exists($grp)) {
				Feedback::error(tr('The group %0 does not exist', $grp), 'session');
				Services_Utilities::sendFeedback($referer);
			}
		}

		TikiLib::lib('user')->invite_tempuser($emails, $groups, $expiry, $prefix, $path);

		Feedback::success(tra('Your invite has been sent.'), 'session');
		Services_Utilities::sendFeedback($referer);
	}

	function action_upload_avatar( $input ) {
		global $user;
		$userwatch = $input->user->none();

		if (!$userwatch) {
			$errormsg = tra('You must set a user for whom to set an avatar.');
			throw new Services_Exception($errormsg);
		}

		if ($user != $userwatch && Perms::get()->admin != 'y') {
			$errormsg = tra('You do not have the permission to change the avatar.');
			throw new Services_Exception($errormsg);
		}
		$this->access->check_feature('feature_userPreferences');
		$this->access->check_user($user);

		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (empty($_FILES['userfile']['name'])) {
				$errormsg = tra('You must select an avatar to upload.');
				throw new Services_Exception($errormsg, 400);
			}
			$name = $_FILES['userfile']['name'];

			$avatarlib = TikiLib::lib('avatar');
			$avatarlib->set_avatar_from_url($_FILES['userfile']['tmp_name'], $userwatch, $name);
			return true;
		} else {
			return array(
				"title" => tra("Upload Avatar"),
				"userwatch" => $userwatch,
			);
		}
	}

	private function removeUsers(array $users, $page = false, $trackerIds = [], $files = false, $referer = false)
	{
		global $user;
		foreach ($users as $deleteuser) {
			if ($deleteuser != 'admin') {

				// remove the user's objects, wiki page first
				if ($page) {
					global $prefs;
					$page = $prefs['feature_wiki_userpage_prefix'] . $deleteuser;
					Services_Exception_Denied::checkObject('remove', 'wiki page', $page);
					$tikilib = TikiLib::lib('tiki');
					$res = $tikilib->remove_all_versions($page);
					if ($res !== true) {
						Feedback::error(tr('An error occurred. User %0 could not be deleted', $deleteuser), 'session');
						Services_Utilities::closeModal($referer);
						return false;
					}
				}

				// then tracker items "owner" by the user
				if (! empty($trackerIds)) {
					$trklib = TikiLib::lib('trk');

					$items = $trklib->get_user_items($deleteuser, false);

					foreach($items as $item) {
						if (in_array($item['trackerId'], $trackerIds)) {
							$trklib->remove_tracker_item($item['itemId'], true);
						}
					}
				}

				// then tracker items "owner" by the user
				if ($files) {
					$filegallib = TikiLib::lib('filegal');

					$galleryId = $filegallib->get_user_file_gallery($deleteuser);

					if ($galleryId) {
						$filegallib->remove_file_gallery($galleryId);
					}
				}

				// and finally remove the actual user (and other associated data)
				$res = $this->lib->remove_user($deleteuser);
				if ($res === true) {
					$logslib = TikiLib::lib('logs');
					$logslib->add_log('adminusers', sprintf(tra('Deleted account %s'), $deleteuser), $user);
				} else {
					Feedback::error(tr('An error occurred. User %0 could not be deleted', $deleteuser), 'session');
					Services_Utilities::closeModal($referer);
					return false;
				}
			}
		}
		return true;
	}
}
