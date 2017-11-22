<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Render the inputs that will allow to customize a custom route
 *
 * @param $params
 * @param $smarty
 */
function smarty_function_router_params($params, $smarty)
{
	if (empty($params['name'])) {
		return;
	}

	$className = 'Tiki\\CustomRoute\\Type\\' . $params['name'];

	if (! class_exists($className)) {
		return;
	}

	$routerParams = $params['params'];

	$class = new $className();
	$inputParams = $class->getParams();
	$routerName = strtolower($class->getRouteType());
	$html = '';

	foreach ($inputParams as $key => $param) {
		$escapedParam = smarty_modifier_escape($routerParams[$key]);
		$inputKey = $routerName . '_' . $key;

		switch ($param['type']) {
			case 'text':
				$input = '<input type="text" id="' . $inputKey . '" class="form-control" name="' . $inputKey . '" value="' . $escapedParam . '">';
				break;
			case 'select':
				$input = '<select id="' . $inputKey . '" class="form-control" name="' . $inputKey . '">';

				if (! empty($param['function'])) {
					$args = [];
					foreach ($param['args'] as $value) {
						$args[] = smarty_modifier_escape($routerParams[$value]);
					}

					$objects = call_user_func_array([$className, $param['function']], $args);
					$param['options'] = $objects;
				}

				if (! empty($param['options'])) {
					foreach ($param['options'] as $optionKey => $optionValue) {
						$selected = $optionKey == $params['params'][$key] ? 'selected' : '';
						$input .= '<option value="' . $optionKey . '" ' . $selected . '>' . $optionValue . '</option>';
					}
				}

				$input .= '</select>';
				break;
		}

		$required = ! empty($param['required']) ? ' *' : '';

		$infoHtml = '';
		if (! empty($param['description'])) {
			$description = smarty_modifier_escape($param['description']);
			$icon = smarty_function_icon(['name' => 'information'], $smarty);

			$infoHtml = <<<HTML
<a class="tikihelp" title="{$param['name']}: {$description}">
	{$icon}
</a>
HTML;
		}

		$html .= <<<HTML
<div class="form-group row" data-task-name="{$params['name']}" style="display:none">
	<label class="col-sm-3 col-md-2 control-label" for="{$inputKey}">{$param['name']}{$required}</label>
	<div class="col-sm-9 col-md-10">
		{$input}
		{$infoHtml}
	</div>
</div>
HTML;
	}

	echo $html;
}
