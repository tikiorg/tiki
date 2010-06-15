<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_datachannel_info()
{
	global $prefs;
	return array(
		'name' => tra('Data Channel'),
		'description' => tra('Displays a form to trigger data channels.'),
		'prefs' => array('wikiplugin_datachannel'),
		'body' => tra('List of fields to display. One field per line. Comma delimited: fieldname,label') . '<br /><br />' .
					tra('To use values from other forms on the same page as parameters for the data-channel use "fieldname, external=fieldid".') . ' ' .
					tra('Where "fieldid" is the id (important) of the external input to use, and "fieldname" is the name of the parameter in the data-channel'),
		'extraparams' => true,
		'params' => array(
			'channel' => array(
				'required' => true,
				'name' => tra('Channel Name'),
				'description' => tra('Name of the channel as registered by the administrator.'),
			),
			'returnURI' => array(
				'required' => false,
				'name' => tra('Return URI'),
				'description' => tra('URI to go to after data channel has run. Defaults to current page.'),
				'filter' => 'pagename',
			),
			'buttonLabel' => array(
				'required' => false,
				'name' => tra('Button Label'),
				'description' => tra('Label for the submit button. Default: "Go".'),
			),
			'class' => array(
				'required' => false,
				'name' => tra('Class'),
				'description' => tra('CSS class for this form'),
			),
			'emptyCache' => array(
				'required' => false,
				'name' => tra('Empty Caches'),
				'description' => tra('Which caches to empty. Default "Clear all Tiki caches"'),
				'default' => 'all',
				'filter' => 'word',
				'options' => array(
					array('text' => tra('Clear all Tiki caches'), 'value' => 'all'), 
					array('text' => tra('./templates_c/'), 'value' => 'templates_c'),
					array('text' => tra('./modules/cache/'), 'value' => 'modules_cache'),
					array('text' => tra('./temp/cache/'), 'value' => 'temp_cache'),
					array('text' => tra('./temp/public/'), 'value' => 'temp_public'),
					array('text' => tra('All user prefs sessions'), 'value' => 'prefs'),
					array('text' => tra('None'), 'value' => 'none'),
				),
			),
			'price' => array(
				'required' => false,
				'name' => tra('Price'),
				'description' => tr('Price to execute the datachannel (%0).', $prefs['payment_currency']),
				'prefs' => array('payment_feature'),
				'filter' => 'text',
			),
			'debug' => array(
				'required' => false,
				'name' => tra('Debug'),
				'description' => '(y | n) '.tra('Be careful, if debug is on, the page will not be refreshed and previous modules can be obsolete'),
				'default' => 'n',
				'filter' => 'word',
			),
		),
	);
}

function wikiplugin_datachannel( $data, $params )
{
	static $execution = 0;
	global $prefs, $smarty, $headerlib;
	$executionId = 'datachannel-exec-' . ++$execution;

	$fields = array();
	$inputfields = array();
	$lines = explode( "\n", $data );
	$lines = array_map( 'trim', $lines );
	$lines = array_filter( $lines );
	$js = '';

	foreach( $lines as $line ) {
		$parts = explode( ',', $line, 2 );

		if( count($parts) == 2 ) {
			if (strpos( $parts[1], 'external') === 0) {	// e.g. "fieldid,external=fieldname"
				$moreparts = explode('=', trim($parts[1]));
				if (count($moreparts) < 2) {
					$moreparts[1] = $parts[0];	// no fieldname supplied so use same as fieldid
				}
				$fields[ $moreparts[0] ] = array('fieldname' => $parts[0], 'fieldid' => $moreparts[1]);
				$js .= "\n".'$jq("input[name=\'' . $parts[0] . '\']").val( unescape($jq("#' . $moreparts[1] . '").val()));';
				$inputfields[ $parts[0] ] = 'external';
			} else {
				$fields[ $parts[0] ] = $parts[1];
				$inputfields[ $parts[0] ] = $parts[1];
			}
		}
	}
	
	require_once 'lib/profilelib/profilelib.php';
	require_once 'lib/profilelib/channellib.php';
	require_once 'lib/profilelib/installlib.php';

	$groups = Perms::get()->getGroups();

	$config = Tiki_Profile_ChannelList::fromConfiguration( $prefs['profile_channels'] );
	if( $config->canExecuteChannels( array( $params['channel'] ), $groups ) ) {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' 
			&& isset( $_POST['datachannel_execution'] ) 
			&& $_POST['datachannel_execution'] == $executionId ) {

			$input = array_intersect_key( $_POST, $inputfields );
			$static = $params;
			unset( $static['channel'] );

			if (!empty($params['price'])) {
				global $paymentlib; require_once 'lib/payment/paymentlib.php';
				$desc = tr( 'Datachannel:', $prefs['site_language'] ) . ' ' . $params['channel'];
				$posts = array();
				foreach ($input as $key => $post) {
					$posts[$key] = $post;
					$desc .= '/' . $post;
				}
				$id = $paymentlib->request_payment( $desc, $params['price'], $prefs['payment_default_delay'] );
				$paymentlib->register_behavior( $id, 'complete', 'execute_datachannel', array( $data, $params, $posts, $executionId ) );
				require_once 'lib/smarty_tiki/function.payment.php';
			return '^~np~' . smarty_function_payment( array( 'id' => $id ), $smarty ) . '~/np~^';
				
			}

			$userInput = array_merge( $input, $static );

			Tiki_Profile::useUnicityPrefix(uniqid());
			$installer = new Tiki_Profile_Installer;
			//TODO: What is the following line for? Future feature to limit capabilities of data channels?
			//$installer->limitGlobalPreferences( array() );

			$profiles = $config->getProfiles( array( $params['channel'] ) );
			$profile = reset($profiles);

			$installer->setUserData( $userInput );
			if (empty($params['debug']) || $params['debug'] != 'y') {
				$installer->setDebug();
			}
			$params['emptyCache'] = isset($params['emptyCache']) ? $params['emptyCache'] : 'all';
			$installer->install( $profile, $params['emptyCache'] );

			if (empty($params['returnURI'])) { $params['returnURI'] = $_SERVER['HTTP_REFERER']; }	// default to return to same page
			if (empty($params['debug']) || $params['debug'] != 'y') {
				header( 'Location: ' . $params['returnURI'] );
				die;
			}
			$smarty->assign('datachannel_feedbacks', array_merge($installer->getFeedback(), $profile->getFeedback()) );
		}
		$smarty->assign( 'datachannel_fields', $fields );
		$smarty->assign( 'datachannel_execution', $executionId );
		$smarty->assign( 'button_label', !empty($params['buttonLabel']) ? $params['buttonLabel'] : 'Go');
		$smarty->assign( 'form_class_attr', !empty($params['class']) ? ' class="' . $params['class'] . '"' : '');
		
		if (!empty($js)) {
			$headerlib->add_js( 'function datachannel_form_submit' . $execution .'() {' . $js .'return true;}');
			$smarty->assign( 'datachannel_form_onsubmit', ' onsubmit="return datachannel_form_submit' . $execution .'()"' );
		} else {
			$smarty->assign( 'datachannel_form_onsubmit', '');
		}

		return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_datachannel.tpl' ) . '~/np~';
	}
}

