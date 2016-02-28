<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

global $dbTiki;

if (!isset($prefs['feature_references']) && !$prefs['feature_references'] === 'y') {
	header('location: index.php');
	exit;
}

$referenceslib = TikiLib::lib('references');

if (!isset($_REQUEST['page'])) {
	$smarty->assign('msg', tra('No page indicated'));
	$smarty->display('error.tpl');
	die;
}

$smarty->assign('page', $_REQUEST['page']);
$page = $_REQUEST['page'];

$perms = Perms::get(array('wiki page', $page));

$page_id = TikiLib::lib('tiki')->get_page_id_from_name($_REQUEST['page']);

$action = $_REQUEST['action'];
$ref_id = $_REQUEST['ref_id'];
$ref_biblio_code = $_REQUEST['ref_biblio_code'];
$ref_author = $_REQUEST['ref_author'];
$ref_title = $_REQUEST['ref_title'];
$ref_part = $_REQUEST['ref_part'];
$ref_uri = $_REQUEST['ref_uri'];
$ref_code = $_REQUEST['ref_code'];
$ref_publisher = $_REQUEST['ref_publisher'];
$ref_location = $_REQUEST['ref_location'];
$ref_year = $_REQUEST['ref_year'];
$ref_style = $_REQUEST['ref_style'];
$ref_template = $_REQUEST['ref_template'];

if (isset($_REQUEST['addreference']) && $action='a_ref') {
	if (! $perms->edit_references) {
		echo json_encode(
			array(
				'result'=>tra('failure'),
				'message'=>tra('You do not have sufficient permissions to perform this action.')
			)
		);
		exit;
	}


	$errors = array();

	if (intval($page_id)) {
		if ($ref_biblio_code=='') {
			$errors[] = tra('Please enter Biblio Code.');
		}
		if (strlen($ref_biblio_code)>50) {
			$errors[] = tra('Biblio code must not exceed 50 characters.');
		}

		if (count($errors)<1) {
			$exists = $referenceslib->check_existence($page_id, $ref_biblio_code);
			if ($exists > 0) {
				echo json_encode(array('result'=>tra('failure'), 'id'=>-1));
			} else {
				$is_library = $referenceslib->check_lib_existence($ref_biblio_code);
				$id = $referenceslib->add_reference(
					$page_id,
					$ref_biblio_code,
					$ref_author,
					$ref_title,
					$ref_part,
					$ref_uri,
					$ref_code,
					$ref_year,
					$ref_style,
					$ref_template,
					$ref_publisher,
					$ref_location
				);
				echo json_encode(array('result'=>tra('success'), 'id'=>$id, 'is_library'=>$is_library));
			}
			exit;
		} else {
			foreach ($errors as $error) {
				echo json_encode(array('result'=>$error, 'id'=>''));
				exit;
			}
		}
	} else {
		$error = tra('Page not found. Please save the page first.');
		echo json_encode(array('result'=>$error, 'id'=>''));
		exit;
	}
}

if (isset($_REQUEST['addlibreference']) && $action = 'a_lib') {

	$errors = array();
	if (! $perms->use_references) {
		echo json_encode(
			array(
				'result'=>tra('failure'),
				'message'=>tra('You do not have sufficient permissions to perform this action.')
			)
		);
		exit;
	}

	if ($ref_biblio_code == '') {
		$errors[] = tra('Please enter Biblio Code.');
	}
	if (strlen($ref_biblio_code) > 50) {
		$errors[] = tra('Biblio code must not exceed 50 characters.');
	}

	if (count($errors) < 1) {
		$exists = $referenceslib->check_lib_existence($ref_biblio_code);
		if ($exists > 0) {
			echo json_encode(
				array(
					'result'=>tra('failure'),
					'message'=>tra('This reference already exists in the library.'),
					'is_library'=>$exists
				)
			);
		} else {
			$id = $referenceslib->add_reference(
				$ref_biblio_code,
				$ref_author,
				$ref_title,
				$ref_part,
				$ref_uri,
				$ref_code,
				$ref_year,
				$ref_style,
				$ref_template,
				$ref_publisher,
				$ref_location
			);
			echo json_encode(
				array(
					'result'=>tra('success'),
					'message'=>tra('Reference added to library.'),
					'id'=>$id,
					'is_library'=>$exists
				)
			);
		}
		exit;
	} else {
		foreach ($errors as $error) {
			echo json_encode(array('result'=>$error, 'message'=>$error));
			exit;
		}
	}
}

if (isset($_REQUEST['editreference'])) {
	if (! $perms->edit_references) {
		echo json_encode(
			array(
				'result'=>tra('failure'),
				'message'=>tra('You do not have sufficient permissions to perform this action.')
			)
		);
		exit;
	}


	$errors = array();

	if ($ref_biblio_code == '') {
		$errors[] = tra('Please enter Biblio Code.');
	} elseif (strlen($ref_biblio_code) > 50) {
		$errors[] = tra('Biblio code must not exceed 50 characters.');
	} else {
		$ref_details = $referenceslib->get_reference_from_id($ref_id);

		/*If new code is not equal to previous code, check_existence*/
		if ($ref_details['data'][0]['biblio_code'] != $ref_biblio_code) {
			$count = $referenceslib->check_existence($page_id, $ref_biblio_code);
			if ($count > 0) {
				$errors[] = tra('This biblio code already exists.');
			}
		}
	}

	if (count($errors)<1) {
		$referenceslib->edit_reference(
			$ref_id,
			$ref_biblio_code,
			$ref_author,
			$ref_title,
			$ref_part,
			$ref_uri,
			$ref_code,
			$ref_year,
			$ref_style,
			$ref_template,
			$ref_publisher,
			$ref_location
		);
		$exists = $referenceslib->check_lib_existence($ref_biblio_code);
		echo json_encode(
			array(
				'result'=>tra('success'),
				'message'=>tra('Bibliography saved.'),
				'is_library'=>$exists
			)
		);
		exit;
	} else {
		foreach ($errors as $error) {
			echo json_encode(array('result'=>tra('failure'), 'message'=>$error));
		}
		exit;
	}
}

if (isset($_REQUEST['action']) && isset($ref_id)) {
	if (! $perms->use_references) {
		echo json_encode(
			array(
				'result'=>tra('failure'),
				'message'=>tra('You do not have sufficient permissions to perform this action.')
			)
		);
		exit;
	}

	if ($_REQUEST['action'] == 'u_lib') {
		$exists = $referenceslib->check_existence($page_id, $ref_biblio_code);
		$id = $referenceslib->add_lib_ref_to_page($ref_id, $page_id);
		if ($id == -1) {
			echo json_encode(array('result'=>tra('failure'), 'message'=>tra('Reference already exists.'), 'id'=>$id));
		} else {
			$details = $referenceslib->get_reference_from_id($id);
			foreach ($details['data'][0] as $key=>$data) {
				if ($details['data'][0][$key] == NULL) {
					if (!$details['data'][0][$key]) {
						$details['data'][0][$key] = '';
					}
				}
			}

			echo json_encode(
				array(
					'result'=>tra('success'),
					'message'=>tra('Reference added.'),
					'id'=>$id,
					'ref_biblio_code'=>$details['data'][0]['biblio_code'],
					'ref_author'=>$details['data'][0]['author'],
					'ref_title'=>$details['data'][0]['title'],
					'ref_year'=>$details['data'][0]['year'],
					'ref_part'=>$details['data'][0]['part'],
					'ref_uri'=>$details['data'][0]['uri'],
					'ref_code'=>$details['data'][0]['code'],
					'ref_publisher'=>$details['data'][0]['publisher'],
					'ref_location'=>$details['data'][0]['location'],
					'ref_style'=>$details['data'][0]['style'],
					'ref_template'=>$details['data'][0]['template']
				)
			);
		}
		exit;
	}
}

if (isset($_REQUEST['action']) && isset($ref_id)) {
	if (! $perms->edit_references) {
		echo json_encode(
			array(
				'result'=>tra('failure'),
				'message'=>tra('You do not have sufficient permissions to perform this action.')
			)
		);
		exit;
	}

	if ($_REQUEST['action'] == 'e_del') {
		$referenceslib->remove_reference($ref_id);
		echo tra('success');
	}
}
