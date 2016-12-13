<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_playscorm_info() {
	return array(
		'name' => tra('PlayScorm'),
		'documentation' => 'PluginPlayScorm',
		'description' => tra('Play a SCORM learning object in an iframe using Moodle'),
		'prefs' => array( 'wikiplugin_playscorm' ),
		'tags' => array( 'experimental' ),
		'format' => 'html',
		'iconname' => 'play',
		'introduced' => 12,
		'params' => array(
			'fileId' => array(
                                'required' => true,
                                'name' => tra('File ID'),
                                'area' => 'fgal_picker_id',
                                'description' => tra('Numeric ID of a SCORM zip file in a File Gallery'),
				'since' => '12.0',
				'filter' => 'digits',
                                'default' => '',
                        ),
			'moodle_url' => array(
				'required' => true,
				'name' => tra('Moodle URL'),
				'description' => tra('Web address of the Moodle instance'),
				'since' => '12.0',
				'filter' => 'url',
				'default' => '',
			),
			'moodle_course_id' => array(
				'required' => true,
				'name' => tra('Moodle Course ID'),
				'description' => tra('Course ID in Moodle to upload SCORM objects to'),
				'since' => '12.0',
				'filter' => 'digits',
				'default' => '', 
			),
			'width' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Frame Width'),
				'description' => tra('Width in pixels or %'),
				'since' => '12.0',
				'filter' => 'text',
				'default' => '1160',
			),
			'height' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Frame Height'),
				'description' => tra('Pixels or %'),
				'since' => '12.0',
				'filter' => 'text',
				'default' => '740',
			),
			'scrolling' => array(
				'safe' => true,
				'required' => false,
				'name' => tra('Scrolling'),
				'description' => tra('Choose whether to add a scroll bar'),
				'since' => '12.0',
				'default' => 'y',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n'),
				)
			),
			'id' => array(
				'required' => false,
				'name' => tra('Numeric ID'),
				'description' => tra('Numeric ID to distinguish multiple plugins of there is more than one'),
				'since' => '12.0',
				'default' => '',
				'filter' => 'digits'
			) 
		), 
	);
}

function wikiplugin_playscorm($data, $params) {

	global $base_url, $tikiroot, $tikipath, $user, $prefs;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');

	extract ($params, EXTR_SKIP);

	if (empty($moodle_url) || empty($fileId) || empty($moodle_course_id)) {
		return 'moodle_url, moodle_course_id needs to be specified in display parameters, remember to set auth parameters in Admin DSN';
	}
	if (empty($prefs['fgal_use_dir'])) {
		return 'File gallery use directory needs to be set';
	}

	$localname = "scorm$fileId";
	$sitepath = parse_url($base_url);

 	if (substr($moodle_url, -1) == '/') {
		$moodle_url = substr($moodle_url, 0, -1);
	}

	$moodle_cm_id = '';

	$info = TikiLib::lib('filegal')->get_file($fileId);
	if (!$userlib->user_has_perm_on_object($user, $info['galleryId'], 'file gallery', 'tiki_p_download_files')) {
		return '';
	}

	// check if it already is up to date
	$needrefresh = true;
	if (file_exists($prefs['fgal_use_dir'].$localname)) {
		$lastupdated = filemtime($prefs['fgal_use_dir'].$localname);
		if ($lastupdated >= $info['lastModif']) {
			$moodle_cm_id = file_get_contents($prefs['fgal_use_dir'].$localname); 
			$needrefresh = false;
		}
	}

	$fileurl = '';
	if ($needrefresh) {
		$fileurl = $base_url . "tiki-download_file.php?fileId=" . $fileId;
		require_once 'lib/auth/tokens.php';
		$tokenlib = AuthTokens::build($prefs);
		$token = $tokenlib->createToken($tikiroot . "tiki-download_file.php",
		array('fileId' => $fileId), array('Registered'), array('timeout' => 60, 'hits' => 1));
		$fileurl .= "&TOKEN=" . $token;
	}

	if ($fileurl) {
		// first upload file to moodle
		$preurl = "$moodle_url/course/modedit.php?add=scorm&course=$moodle_course_id&section=0&return=0";
		$submiturl = "$moodle_url/course/modedit.php";

		$oldVal = ini_get('arg_separator.output');
		ini_set('arg_separator.output', '&');

		$client = $tikilib->get_http_client($preurl);	

		$response = $tikilib->http_perform_request($client);

		$body = $response->getBody();

		preg_match('/sesskey=([^\"\']+)[\'\"]/', $body, $matches);

		if (empty($matches[1])) {
			return '';
		} else {
			$sesskey = $matches[1];
		}

		$client->setUri( $submiturl );
		$client->setOptions(array( 'keepalive' => false, 'maxredirects' => 0, 'timeout' => 60 ) );

		$moodleform = array( 
			'sesskey' => $sesskey,
			'course' => $moodle_course_id,
			'redirecturl' => '../mod/scorm/view.php?id=',
			'section' => 0,	
			'modulename' => 'scorm',
			'add' => 'scorm',
			'return' => 0,
			'name' => 'Tiki Scorm Preview',
			'introeditor[text]' => 'Description',
			'introeditor[format]' => 1,
			'itemId' => 31405523,
			'scormtype' => 'localsync',
			'packageurl' => $fileurl,
			'submitbutton' => 'Save and display',
			'_qf__mod_scorm_mod_form' => 1,
			'hidenav' => 0,
			'hidetoc' => 0,
			'skipview' => 2,
			'popup' => 0,
			'hidebrowse' => 0,
			'displaycoursestructure' => 0,
		);

		$client->setParameterPost( $moodleform );
		$client->setMethod(Zend\Http\Request::METHOD_POST);

		$response = $client->send();

		$body = $response->getBody();

		ini_set('arg_separator.output', $oldVal);

		preg_match('/view\.php\?id=([0-9]+)/', $body, $matches);
		if (empty($matches[1])) {
			return '';
		} else {
			$moodle_cm_id = $matches[1];
			file_put_contents($prefs['fgal_use_dir'].$localname, $moodle_cm_id);
		}
	}

	if (!$moodle_cm_id) {
		return '';
	}

	$src = "$moodle_url/mod/scorm/view.php?id=$moodle_cm_id"; // this is the simple play "student" version requiring hacks since the teacher does not get it
	//$src = "$moodle_url/mod/scorm/player.php?mode=review&cm=$moodle_cm_id&display=popup"; // alternative player version?

	if (isset($width)) {
		$smarty->assign('iframewidth', $width);
	} else {
		$smarty->assign('iframewidth', 1160);
	}
	if (isset($height)) {
		$smarty->assign('iframeheight', $height);	
	} else {
		 $smarty->assign('iframeheight', 740);
	}
	if (isset($scrolling) && $scrolling == 'n') {
		$smarty->assign('iframescrolling', 'false');
	} else {
		$smarty->assign('iframescrolling', 'true');
	}
	if (isset($id)) {
		$smarty->assign('id', $id);
	} else {
		$smarty->assign('id', '');
	}
	$smarty->assign('iframeurl', $src);
	return $smarty->fetch('wiki-plugins/wikiplugin_playscorm.tpl'); 
}
