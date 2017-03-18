<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* inserts the content of an rss feed into a module */
function smarty_function_scheduler_params($params, $smarty)
{
	if (empty($params['name'])) {
		return;
	}

	$className = 'Scheduler_Task_' . $params['name'];

	if(!class_exists($className)) {
		return;
	}

	$schedulerParams = $params['params'];

	$class = new $className;
	$inputParams = $class->getParams();
	$taskName = strtolower($class->getTaskName());
	$html = '';

	foreach ($inputParams as $key => $param) {

		$escapedParam = smarty_modifier_escape($schedulerParams[$key]);
		$inputKey = $taskName .'_' . $key;

		switch ($param['type']) {
			case 'text':
				$input = '<input type="text" id="'. $inputKey .'" class="form-control" name="'. $inputKey .'" value="' . $escapedParam . '">';
				break;
			case 'password':
				$input = '<input type="password" id="'. $inputKey .'" class="form-control" name="'. $inputKey .'" value="' . $escapedParam . '">';
				break;
			case 'textarea':
				$input = '<textarea id="'. $inputKey .'" class="form-control" name="'. $inputKey .'"">' . $escapedParam .'</textarea>';
				break;
			case 'select':
				//@todo implement
				break;
		}

		$html .= <<<HTML
<div class="form-group row" data-task-name="{$params['name']}" style="display:none">
	<label class="col-sm-3 col-md-2 control-label" for="{$inputKey}">{$param['name']}</label>
	<div class="col-sm-7 col-md-6">
		{$input}
	</div>
</div>
HTML;
	}

	echo $html;

}
