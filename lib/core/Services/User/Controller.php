<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

		$result = array();
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

		if (is_array($regResult)) {
			foreach ($regResult as $r) {
				$result[] = $r->msg;
			}
		} else if (is_a($regResult, 'RegistrationError')) {
			$result[] = $regResult->msg;
		} else if (is_string($regResult)) {
			$result = trim($regResult, "\n");
		} elseif (!empty($regResult['msg'])) {
			$result = trim($regResult['msg'], "\n");
		}

		return array('result' => json_encode($result));
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

				$result['add_friend_button'] = '';	// can befriend yourself

				if ($prefs['feature_friends'] === 'y') {

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
						$result['add_friend_button'] = '';
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
					$result['lastSeenDt'] = $info['lastLogin'];
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
}

