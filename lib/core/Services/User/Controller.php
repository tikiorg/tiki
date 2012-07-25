<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		$result = array();
		$name = $input->name->string();
		$pass = $input->pass->string();
		$passAgain = $input->passAgain->string();
		$captcha = $input->captcha->arra();
		$antibotcode = $input->antibotcode->string();
		$email = $input->email->string();

		include_once('lib/registration/registrationlib.php');
		$regResult = $registrationlib->register_new_user(array(
			'name' => $name,
			'pass' => $pass,
			'passAgain' => $passAgain,
			'captcha' => $captcha,
			'antibotcode' => $antibotcode,
			'email' => $email,
		));

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
}

