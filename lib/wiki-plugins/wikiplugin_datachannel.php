<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_datachannel_info()
{
	global $prefs;
	return [
		'name' => tra('Data Channel'),
		'documentation' => 'PluginDataChannel',
		'description' => tra('Display a form to access data channels'),
		'prefs' => ['wikiplugin_datachannel'],
		'body' => tr('List of fields to display. One field per line. Comma delimited: %0', '<code>fieldname,label</code>')
			. '<br /><br />' . tr(
				'To use values from other forms on the same page as parameters for the data-channel
			use %0 or %1 can be used where a value is simply listed on the same page where each value has the %2
			as an html id tag.',
				'<code>fieldname, external=fieldid</code>',
				'<code>fieldname, external=fieldid,text</code>',
				'<code>fieldid</code>'
			) . ' ' . tr('Where %0 is
			the id (important) of the external input to use, and %1 is the name of the parameter in the
			data-channel', '<code>fieldid</code>', '<code>fieldname</code>') . ' . ' . tr('To use fixed hidden preset
			values use %0', '<code>fieldname, hidden=value</code>'),
		'extraparams' => true,
		'iconname' => 'move',
		'introduced' => 4,
		'params' => [
			'channel' => [
				'required' => true,
				'name' => tra('Channel Name'),
				'description' => tra('Name of the channel as registered by the administrator.'),
				'since' => '4.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'datachannel',
			],
			'returnURI' => [
				'required' => false,
				'name' => tra('Return URL'),
				'description' => tr(
					'URL to go to after data channel has run. Defaults to current page. Can contain
					placeholders %0 or %1, where reference matches a profile object ref,
					allowing to redirect conditionally to a freshly created object.',
					'<code>~np~%reference%~/np~</code>',
					'<code>~np~%reference:urlencode%~/np~</code>'
				),
				'since' => '6.0',
				'filter' => 'url',
				'default' => '',
			],
			'returnErrorURI' => [
				'required' => false,
				'name' => tra('Return URL on error'),
				'description' => tra('URL to go to after data channel has run and failed. If not specified, use the success URL.'),
				'since' => '12.0',
				'filter' => 'url',
				'default' => '',
			],
			'quietReturn' => [
				'required' => false,
				'name' => tra('Do not use returnURI but instead return true quietly'),
				'description' => tr('If set to %0, will return quietly after data channel has run which would be needed
					if plugin is used in non-wiki page context.', '<code>y</code>'),
				'since' => '6.2',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
				'filter' => 'alpha',
				'default' => 'n',
			],
			'buttonLabel' => [
				'required' => false,
				'name' => tra('Button Label'),
				'description' => tra('Label for the submit button. Default: "Go".'),
				'since' => '6.0',
				'default' => 'Go',
			],
			'class' => [
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('CSS class for this form'),
				'since' => '6.0',
				'default' => '',
			],
			'template' => [
				'required' => false,
				'name' => tra('Template'),
				'description' => tra('Template to be used to render the form, instead of the default template'),
				'default' => '',
				'since' => '15.0',
				'filter' => 'text',
			],
			'emptyCache' => [
				'required' => false,
				'name' => tra('Empty Caches'),
				'description' => tra('Which caches to empty. Default "Clear all Tiki caches"'),
				'since' => '6.0',
				'default' => 'all',
				'filter' => 'text',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Clear all Tiki caches'), 'value' => 'all'],
					['text' => './temp/templates_c/', 'value' => 'templates_c'],
					['text' => './modules/cache/', 'value' => 'modules_cache'],
					['text' => './temp/cache/', 'value' => 'temp_cache'],
					['text' => './temp/public/', 'value' => 'temp_public'],
					['text' => tra('All user prefs sessions'), 'value' => 'prefs'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
			'price' => [
				'required' => false,
				'name' => tra('Price'),
				'description' => tr('Price to execute the data channel (%0).', $prefs['payment_currency']),
				'since' => '6.0',
				'prefs' => ['payment_feature'],
				'default' => '',
				'filter' => 'text',
			],
			'paymentlabel' => [
				'required' => false,
				'name' => tra('Payment Label'),
				'since' => '6.0',
				'prefs' => ['payment_feature'],
				'default' => '',
				'filter' => 'text',
			],
			'debug' => [
				'required' => false,
				'name' => tra('Debug'),
				'description' => tra('Be careful, if debug is on, the page will not be refreshed and previous modules
					can be obsolete (not on by default)'),
				'since' => '5.0',
				'default' => 'n',
				'filter' => 'alpha',
				'advanced' => true,
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
			],
			'array_values' => [
				'required' => false,
				'name' => tra('Multiple Values'),
				'description' => tr('Accept arrays of multiple values in the POST. e.g. %0 etc.
					(multiple values not accepted by default)', '<code>itemId[]=42&itemId=43</code>'),
				'since' => '6.0',
				'default' => 'n',
				'filter' => 'alpha',
				'advanced' => true,
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				],
			],
		],
	];
}

function wikiplugin_datachannel($data, $params)
{
	static $execution = 0;
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$headerlib = TikiLib::lib('header');
	$executionId = 'datachannel-exec-' . ++$execution;

	$datachannelWithTemplate = empty($params['template']) ? false : true;

	if (isset($params['price']) && $params['price'] == 0) {
		// Convert things like 0.00 to empty
		unset($params['price']);
	}
	$fields = [];
	$inputfields = [];
	$lines = explode("\n", $data);
	$lines = array_map('trim', $lines);
	$lines = array_filter($lines);
	$js = '';
	if (! isset($params['array_values'])) {
		$params['array_values'] = 'n';
	}

	foreach ($lines as $line) {
		$parts = explode(',', $line, 2);
		$parts = array_map('trim', $parts);

		if ($datachannelWithTemplate && (count($parts) == 1)) { // copy name as lablel, for datachannels with templates
			$parts[1] = $parts[0];
		}

		if (count($parts) == 2) {
			if (strpos($parts[1], 'external') === 0) {	// e.g. "fieldid,external=fieldname"
				$moreparts = explode('=', $parts[1], 2);
				$moreparts = array_map('trim', $moreparts);
				if (count($moreparts) < 2) {
					$moreparts[1] = $parts[0];	// no fieldid or modifier supplied so use same as fieldname
				} else {  // look for a modifier eg 'text' after the fieldid
					$extmodifier = '';
					$yetmoreparts = explode(',', $moreparts[1], 2);
					$yetmoreparts = array_map('trim', $yetmoreparts);
					if (count($yetmoreparts) > 1) {
						$extmodifier = $yetmoreparts[1];
						$moreparts[1] = $yetmoreparts[0];
					}
				}
				$fields[ $parts[0] ] = $moreparts[0];
				if ($params['array_values'] === 'y' && preg_match('/[\[\]\.#\=]/', $moreparts[1])) {	// check for [ ] = or . which would be a jQuery selector
					// might select multiple inputs
					$js .= "\n" . '$("input[name=\'' . $parts[0] . '\']").val( unescape($("' . $moreparts[1] . '").serialize()));';
				} else {	// otherwise it's an id but could have a modifier eg 'text'
					if ($extmodifier === 'text') {   // if modifier is text use text instead of val in the js
						$js .= "\n" . '$("input[name=\'' . $parts[0] . '\']").val( unescape($("#' . $moreparts[1] . '").text()));';
					} else {
						$js .= "\n" . '$("input[name=\'' . $parts[0] . '\']").val( unescape($("#' . $moreparts[1] . '").val()));';
					}
				}
				$inputfields[ $parts[0] ] = 'external';
			} elseif (strpos($parts[1], 'hidden') === 0) {
				$moreparts = explode('=', $parts[1], 2);
				$moreparts = array_map('trim', $moreparts);
				$fields[ $parts[0] ] = $moreparts[1];
				$inputfields[ $parts[0] ] = 'hidden';
			} else {
				$fields[ $parts[0] ] = $parts[1];
				$inputfields[ $parts[0] ] = $parts[1];
			}
		}
	}

	$groups = Perms::get()->getGroups();

	$config = Tiki_Profile_ChannelList::fromConfiguration($prefs['profile_channels']);
	if ($config->canExecuteChannels([ $params['channel'] ], $groups, true)) {
		$smarty->assign('datachannel_execution', $executionId);
		if ($_SERVER['REQUEST_METHOD'] == 'POST'
			&& isset($_POST['datachannel_execution'])
			&& $_POST['datachannel_execution'] == $executionId
			&& $config->canExecuteChannels([ $params['channel'] ], $groups) ) {
			$input = array_intersect_key($_POST, $inputfields);

			$trimAndMapArraysAsYaml = function ($element) {
				if (! is_array($element)) {
					return trim($element);
				}
				$element = array_map(trim, $element);
				return implode(',', $element);
			};

			$input = array_map($trimAndMapArraysAsYaml, $input);

			$itemIds = [];					// process possible arrays in post
			if ($params['array_values'] === 'y') {
				foreach ($input as $key => $val) {
					if (! empty($val)) {
						parse_str($val, $vals);
						if (is_array($vals)) {							// serialized collection of inputs
							$arr = [];
							if ($key == 'itemId') {
								foreach ($vals as $v) {					// itemId[x,y,z]
									if (is_array($v)) {
										$arr = array_merge($arr, $v);
									}
								}
								$itemIds = $arr;
							} else {
								foreach ($vals as $v) {					// fieldname[x=>a,y=>b,z=>c]
									if (is_array($v)) {
										foreach ($v as $k => $kv) {
											if (in_array($k, $itemIds)) {	// check if sent in itemIds array
												$arr[] = $kv;				// (e.g. from trackerlist checkboxes)
											}
										}
									} else {
										$arr = $val;	// not an array, so use the initial string val
									}
								}
							}
							$input[$key] = $arr;
						}
					}
				}
			}
			$inputs = [];
			if ($params['array_values'] === 'y' && ! empty($itemIds)) {
				$cid = count($itemIds);
				for ($i = 0; $i < $cid; $i++) {	// reorganise array
					$arr = [];
					foreach (array_keys($input) as $k) {
						if (isset($input[$k]) && is_array($input[$k])) {
							$arr[$k] = $input[$k][$i];
						} else {
							$arr[$k] = $input[$k];
						}
					}
					$inputs[] = $arr;
				}
			} else {
				$inputs[] = $input;
			}
			$static = $params;
			$unsets = wikiplugin_datachannel_info();	// get defined params
			$unsets = array_keys($unsets['params']);
			foreach ($unsets as $un) {					// remove defined params leaving user supplied ones
				unset($static[$un]);
			}

			if (! empty($params['price'])) {
				$paymentlib = TikiLib::lib('payment');
				$desc = empty($params['paymentlabel']) ? tr('Data channel:', $prefs['site_language']) . ' ' . $params['channel'] : $params['paymentlabel'];
				$posts = [];
				foreach ($input as $key => $post) {
					$posts[$key] = $post;
					$desc .= '/' . $post;
				}
				$id = $paymentlib->request_payment($desc, $params['price'], $prefs['payment_default_delay']);
				$paymentlib->register_behavior($id, 'complete', 'execute_datachannel', [ $data, $params, $posts, $executionId ]);
				require_once 'lib/smarty_tiki/function.payment.php';

				return '^~np~' . smarty_function_payment([ 'id' => $id ], $smarty) . '~/np~^';
			}

			$success = true;
			$arguments = [];
			foreach ($inputs as $input) {
				$userInput = array_merge($input, $static);

				Tiki_Profile::useUnicityPrefix(uniqid());
				$profiles = $config->getProfiles([ $params['channel'] ]);
				$profile = reset($profiles);
				$profile->removeSymbols();

				Tiki_Profile::useUnicityPrefix(uniqid());
				$installer = new Tiki_Profile_Installer;
				//TODO: What is the following line for? Future feature to limit capabilities of data channels?
				//$installer->limitGlobalPreferences( array() );
				// jb tiki6: looks like if set to an empty array it would prevent any prefs being set
				// i guess the idea is to be able to restrict the settable prefs to only harmless ones for security

				$installer->setUserData($userInput);
				if (! empty($params['debug']) && $params['debug'] === 'y') {
					$installer->setDebug();
				}

				$installer->disablePrefixDependencies();

				$params['emptyCache'] = isset($params['emptyCache']) ? $params['emptyCache'] : 'all';
				$success = $installer->install($profile, $params['emptyCache']) && $success;
				foreach ($profile->getLoadedObjects() as $object) {
					$arguments["%{$object->getRef()}%"] = $object->getValue();
					$arguments["%{$object->getRef()}:urlencode%"] = rawurlencode($object->getValue());
				}
			}

			if (empty($params['returnURI'])) {
				// default to return to same page
				$params['returnURI'] = $_SERVER['HTTP_REFERER'];
			}
			if (empty($params['returnErrorURI'])) {
				$params['returnErrorURI'] = $params['returnURI'];
			}

			if (empty($params['debug']) || $params['debug'] != 'y') {
				if (isset($params['quietReturn']) && $params['quietReturn'] == 'y') {
					return true;
				} elseif (! empty($installer) && ! empty($profile)) {
					if ($target = $profile->getInstructionPage()) {
						$profilefeedback = $installer->getFeedback();

						foreach ($profilefeedback as $feedback) {
							if (strpos($feedback, tra('An error occurred: ')) === 0) {
								Feedback::error($feedback, 'session');
							}
						}

						$wikilib = TikiLib::lib('wiki');
						$target = $wikilib->sefurl($target);
						header('Location: ' . $target);
						exit;
					}
				} else {
					$url = $success ? $params['returnURI'] : $params['returnErrorURI'];
					$url = str_replace(array_keys($arguments), array_values($arguments), $url);
					$access = TikiLib::lib('access');
					$access->redirect($url);
				}
			} else {
				Feedback::note(['mes' => array_merge($installer->getFeedback(), $profile->getFeedback())]);
			}
		}

		$smarty->assign('datachannel_inputfields', $inputfields);
		$smarty->assign('datachannel_fields', $fields);
		$smarty->assign('button_label', ! empty($params['buttonLabel']) ? $params['buttonLabel'] : 'Go');
		$smarty->assign('form_class_attr', ! empty($params['class']) ? ' class="' . $params['class'] . '"' : '');

		if (! empty($js)) {
			$headerlib->add_js("function datachannel_form_submit{$execution}() {{$js}\nreturn true;\n}");
			$smarty->assign('datachannel_form_onsubmit', ' onsubmit="return datachannel_form_submit' . $execution . '();"');
		} else {
			$smarty->assign('datachannel_form_onsubmit', '');
		}

		if (empty($params['template'])) {
			return '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_datachannel.tpl') . '~/np~';
		} else {
			return '~np~' . $smarty->fetch($params['template']) . '~/np~';
		}
	}
}
