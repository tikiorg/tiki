<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ShowTikiOrg_Controller
{
	function setUp()
	{
		global $prefs;

		Services_Exception_Disabled::check('trackerfield_showtikiorg');
	}

	function action_process($input)
	{
		$id = $input->id->int();
		$userid = $input->userid->int();
		$username = $input->username->text();
		$fieldId = $input->fieldId->int();
		$command = $input->command->word();
		$svntag = $input->svntag->text();
		$creator = $input->username->text();

		$item = Tracker_Item::fromId($id);
		if (!$item->canViewField($fieldId)) {
			throw new Services_Exception_Denied;
		}

		$field = TikiLib::lib('trk')->get_tracker_field($fieldId);
		$options = json_decode($field['options']);
		if (!is_object($options) && is_array($field['options_array'])) {
			// Support Tiki 11
			$options = new stdClass();
			$options->domain = $field['options_array'][0];
			$options->remoteShellUser = $field['options_array'][1];
			$options->publicKey = $field['options_array'][2];
			$options->privateKey = $field['options_array'][3];
		}
		$domain = $options->domain;

		$conn = ssh2_connect($domain, 22);
		$conntry = ssh2_auth_pubkey_file(
			$conn,
			$options->remoteShellUser,
			$options->publicKey,
			$options->privateKey
		);

		if (!$conntry) {
			$ret['status'] = 'DISCO';
			return $ret;
		}

		$infostring = "info -i $id -U $userid";
		$infostream = ssh2_exec($conn, $infostring);

		stream_set_blocking($infostream, TRUE);
		$infooutput = stream_get_contents($infostream);
		$ret['debugoutput'] = $infooutput;

		if (strpos($infooutput, 'MAINTENANCE: ') !== false) {
			$maintpos = strpos($infooutput, 'MAINTENANCE: ');
			$maintreason = substr($infooutput, $maintpos + 13);
			$maintreason = substr($maintreason, 0, strpos($maintreason, '"'));
			$ret['maintreason'] = $maintreason;
			$ret['status'] = 'MAINT';
			return $ret;
		}

		$versionpos = strpos($infooutput, 'VERSION: ');
		$version = substr($infooutput, $versionpos + 9);
		$version = substr($version, 0, strpos($version, PHP_EOL));
		$version = trim($version);
		$ret['version'] = $version;

		$statuspos = strpos($infooutput, 'STATUS: ');
		$status = substr($infooutput, $statuspos + 8, 5);
		$status = trim($status);
		if (!$status || $status == 'FAIL') {
			$ret['status'] = 'FAIL';
		} else {
			$ret['status'] = $status;
			$sitepos = strpos($infooutput, 'SITE: ');
			$site = substr($infooutput, $sitepos + 6);
			$site = substr($site, 0, strpos($site, ' '));
		 	$ret['showurl'] = $site;
			$ret['showlogurl'] = $site . '/info.txt';
			$ret['snapshoturl'] = $site . '/snapshots/';
			if ($site && $ret['status'] == 'ACTIV') {
				$value = 'active ' . substr($site, 0, strpos($site, '.')); // the 'active' is useful for filtering on
				TikiLib::lib('trk')->modify_field($id, $fieldId, $value);
				require_once('lib/search/refresh-functions.php');
				refresh_index('trackeritem', $id);
			} elseif ($ret['status'] == 'NONE') {
				$value = 'none';
				TikiLib::lib('trk')->modify_field($id, $fieldId, $value);
				require_once('lib/search/refresh-functions.php');
				refresh_index('trackeritem', $id);
		 	}
		}

		if (!empty($command)) {
			global $user;

			if (($command == 'update' || $command == 'reset' || $command == 'destroy') && !TikiLib::lib('user')->user_has_permission($user, 'tiki_p_admin') && $user != $creator) {
				throw new Services_Exception_Denied;
			}

			if (empty($svntag)) {
				$fullstring = "$command -u $creator -i $id -U $userid";
			} else {
				$fullstring = "$command -t $svntag -u $username -i $id -U $userid";
			}

			$stream = ssh2_exec($conn, $fullstring);
			stream_set_blocking($stream, TRUE);
			$output = stream_get_contents($stream);
			fclose($stream);
			$ret['debugoutput'] = $fullstring . "\n" . $output;

			if ($command == 'snapshot') {
				$ret['status'] = 'SNAPS';
			} else if ($command == 'destroy') {
				$ret['status'] = 'DESTR';
			} else if ($command == 'create' || $command == 'update') {
				$ret['status'] = 'BUILD';
			} else if ($command == 'reset') {
				if (strpos('ERROR', $fullstring) !== false) {
					$ret['status'] = 'RENOK';
				} else {
					$ret['status'] = 'RESOK';
				}
			}
		}

		$ret['debugoutput'] = '-' . $status . '- ' . $ret['debugoutput'];

		$cachelib = TikiLib::lib('cache');
		$cacheKey = 'STO-' . $options->domain . '-' . $fieldId . "-" . $id;
		$cachelib->invalidate($cacheKey);

		return $ret;
	}
}

