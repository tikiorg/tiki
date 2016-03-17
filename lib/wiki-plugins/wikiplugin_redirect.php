<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_redirect_info()
{
	return array(
		'name' => tra('Redirect'),
		'documentation' => 'PluginRedirect',
		'description' => tra('Redirect to another page'),
		'prefs' => array( 'wikiplugin_redirect' ),
		'validate' => 'arguments',
		'iconname' => 'next',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page Name'),
				'description' => tra('Wiki page name to redirect to.'),
				'since' => '3.0',
				'filter' => 'pagename',
				'default' => '',
				'profile_reference' => 'wiki_page',
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Complete URL, internal or external.'),
				'since' => '3.0',
				'filter' => 'url',
				'default' => '',
			),
			'perspective' => array(
				'required' => false,
				'name' => tra('Perspective'),
				'description' => tra('The ID of a perspective to switch to (requires feature_perspective).'),
				'since' => '7.1',
				'filter' => 'int',
				'default' => '',
				'profile_reference' => 'perspective',
			),
			'autologin_remotetiki' => array(
				'required' => false,
				'name' => tra('Auto login remote Tiki and redirect to page there.'),
				'description' => tra('Base URL where remote Tiki is located, to auto login to prior to redirection to page there, e.g. https://othertiki.com.'),
				'filter' => 'url',
				'default' => '',
			),
		),
	);
}

function wikiplugin_redirect($data, $params)
{
	global $just_saved;
	$tikilib = TikiLib::lib('tiki');
	extract($params, EXTR_SKIP);
	$areturn = '';

	if (!isset($page)) {
		$areturn = "REDIRECT plugin: No page specified!<br />";
	}
	if (!isset($url)) {
		$areturn .= "REDIRECT plugin: No url specified!<br />";
	}
	if (isset($page)) {
		$location = $page;
	} else if (isset($url)) {
		$location = $url;
	} else if (isset($perspective)) {
		$location = tra('perspective ') . $perspective;
	} else {
		$location = tra('nowhere');
		$areturn .= "REDIRECT plugin: No perspective specified!";
	}

	if ($just_saved) {
		$areturn = sprintf(tra("REDIRECT plugin: The redirection to '%s' is disabled just after saving the page."), $location);
	} else if (TikiLib::lib('parser')->option['indexing']) {
		return;
	} else if (TikiLib::lib('parser')->option['preview_mode']) {
		$areturn = sprintf(tra("REDIRECT plugin: The redirection to '%s' is disabled in preview mode. "), $location);
	} else if ((isset($_REQUEST['redirectpage']))) {
		$areturn = tra("REDIRECT plugin: redirect loop detected!");
	} else if (isset(TikiLib::lib('parser')->option['print']) && TikiLib::lib('parser')->option['print'] == 'y') {
		$info = $tikilib->get_page_info($location);
		return $tikilib->parse_data($info['data'], TikiLib::lib('parser')->option);
	} else {

		if (isset($perspective)) {
			global $base_host;
			$access = TikiLib::lib('access');
			$perspectivelib = TikiLib::lib('perspective');
			$access->check_feature('feature_perspective');

			if ($_SESSION['current_perspective'] !== $perspective) {
		
				if ( $perspectivelib->perspective_exists($perspective) ) {
					$_SESSION['current_perspective'] = $perspective;
				}
				if (empty($page) && empty($url)) {
					$url =  $base_host . $_SERVER['REQUEST_URI'];
				}
			}
			$areturn = '';	// errors set above not relevant if using perspective
		}

		// Make it possible to edit the plugin in wysiwyg
		// Do not redirect if the page is being edited
		$isEditMode = (strpos($_SERVER['SCRIPT_NAME'], 'tiki-editpage.php') !== false) || (isset($_REQUEST['controller']) && $_REQUEST['controller'] == 'edit');
		if ($isEditMode == false) {
			// Auto login to remote Tiki functionality
			if (!empty($autologin_remotetiki)) {
				if (substr($autologin_remotetiki, -1) == '/') {
					$autologin_remotetiki = rtrim($autologin_remotetiki, '/');
				}
				if (!empty($page)) {
					$redirect_page = $page;
				} else {
					$redirect_page = '';
				}
				$remotetikiurl = get_remotetikiurl($autologin_remotetiki, $redirect_page);
				if (filter_var($remotetikiurl, FILTER_VALIDATE_URL)) {
					header("Location: $remotetikiurl");
					die;
				} else {
					TikiLib::lib('access')->display_error('', tra('Remote system error'), "500");
					die;
				}
			}

			/* SEO: Redirect with HTTP status 301 - Moved Permanently than default 302 - Found */
			if (isset($page)) {
				TikiLib::lib('access')->redirect("tiki-index.php?page=$page&redirectpage=".$_REQUEST['page'], '', 301);
			}
			if (isset($url)) {

				global $base_url, $url_path;		// try to detect redirect loop to server root
				if (
					$url == $base_url ||			// whole site url
					$url . '/' == $base_url ||		// optional trailing /
					$url == $url_path ||			// just the path?
					$url . '/' == $url_path ||
					preg_match('/[\.]?\/$/', $url)	// either ./ or / current dir or root
				) {

					$hp = TikiLib::lib('wiki')->get_default_wiki_page();

					if ($_REQUEST['page'] === $hp && !isset($_GET['page']) && !isset($_POST['page'])) {
						return '';						// don't redirect if we've already been redirected to the "home page"
					}
				}
				TikiLib::lib('access')->redirect($url);
			}
		}
	}
	return $areturn;
}

/**
 * This function gets a URL with a token in it so that the user can be redirected that to actually login
 * @param $autologin_remotetiki The remote Tiki base url, e.g. https://remotetiki.com
 * @param $redirect_page The pagename of the page on the remote Tiki to redirect to.
 * If not set, user will end up on the default home page on remote Tiki.
 * @return string The URL with the token in it.
 */
function get_remotetikiurl($autologin_remotetiki, $redirect_page) {
	// Get URL for user to login into remote Tiki
	global $user, $base_url;
	TikiLib::lib('access')->check_user($user);
	$email = TikiLib::lib('user')->get_user_email($user);
	$realName = trim(TikiLib::lib('tiki')->get_user_preference($user, 'realName', ''));
	$remotetikiurl = $autologin_remotetiki . '/tiki-autologin.php';
	$client = TikiLib::lib('tiki')->get_http_client( $remotetikiurl );
	$groups = TikiLib::lib('user')->get_user_groups($user);
	$base = array( 'uname' => $user, 'email' => $email, 'realName' => $realName, 'page' => $redirect_page, 'base_url' => $base_url, 'groups' => $groups );
	try {
		$client->setParameterPost( $base );
		$client->setMethod(Zend\Http\Request::METHOD_POST);
		$response = $client->send();
		if ($response->isSuccess()) {
			return $response->getBody();
		} else {
			TikiLib::lib('access')->display_error('', $response->getReasonPhrase(), $response->getStatusCode());
			die;
		}
	} catch ( Zend\Http\Exception\ExceptionInterface $e ) {
		throw new Exception($e->getMessage());
	}
}