<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_ConditionsController
{
	public static function requiresApproval($user)
	{
		global $user;

		if (! $user) {
			// Anonymous users cannot approve a thing.
			return false;
		}

		$lib = new self;
		$page = $lib->getApprovalPageInfo();

		if (! $page) {
			return false;
		}

		$perms = Perms::get('wiki page', $page['pageName']);
		if ($perms->wiki_approve) {
			// Users who can approve the terms do not need to approve them
			// This includes adminsitrators who have all permissions
			// Among other things, this avoids the issue of having to approve terms
			// after modifying the page
			return false;
		}

		$hash = $lib->generateHash($page, $user);
		$versions = $lib->getApprovedVersions($user);
		return ! in_array($hash, $versions);
	}

	function setUp()
	{
		global $user;

		Services_Exception_Disabled::check('conditions_enabled');

		if (! $user) {
			throw new Services_Exception_Denied(tr('Authentication required.'));
		}
	}

	function action_approval($input)
	{
		global $user;

		$info = $this->getApprovalPageInfo();
		$hash = $this->generateHash($info, $user);

		$content = $info['data'];
		$parse_options = array(
			'is_html' => $info['is_html'],
			'language' => $info['lang'],
		);

		$pdata = new Tiki_Render_Lazy(
			function () use ($content, $parse_options) {
				$wikilib = TikiLib::lib('wiki');
				return $wikilib->parse_data($content, $parse_options);
			}
		);

		$origin = $input->origin->url() ?: $_SERVER['REQUEST_URI'];
		$toApprove = $input->approve->word();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $toApprove) {
			if ($toApprove == $hash) {
				$this->approveVersion($hash);
				TikiLib::lib('access')->redirect($origin);
			} else {
				TikiLib::lib('errorreport')->report(tr('The terms and conditions were modified while you were reading them.'));
			}
		}

		return array(
			'title' => tr('Terms and Conditions'),
			'origin' => $origin,
			'content' => $pdata,
			'hash' => $hash,
		);
	}

	private function getApprovalPageInfo()
	{
		global $prefs;

		$page = $this->getApprovalPage();

		if ($prefs['flaggedrev_approval'] == 'y') {
			$flaggedrevisionlib = TikiLib::lib('flaggedrevision');

			if ($flaggedrevisionlib->page_requires_approval($page)) {
				if ($version_info = $flaggedrevisionlib->get_version_with($page, 'moderation', 'OK')) {
					return $version_info;
				}
			}
		}

		$tikilib = TikiLib::lib('tiki');
		return $tikilib->get_page_info($page);
	}

	private function getApprovalPage()
	{
		// TODO : Handle page language / feature_multilingual
		global $prefs;
		return $prefs['conditions_page_name'];
	}

	private function approveVersion($hash)
	{
		global $user;
		$versions = $this->getApprovedVersions($user);
		array_unshift($versions, $hash);

		$tikilib = TikiLib::lib('tiki');
		$tikilib->set_user_preference($user, 'terms_approved', implode(',', $versions));
	}

	private function getApprovedVersions($user)
	{
		$tikilib = TikiLib::lib('tiki');
		$versions = $tikilib->get_user_preference($user, 'terms_approved', '');
		return array_filter(explode(',', $versions));
	}

	private function generateHash($info, $user)
	{
		return md5($info['pageName'] . '--' . $info['version'] . '--' . TikiLib::lib('tiki')->get_site_hash() . '--' . $user);
	}
}

