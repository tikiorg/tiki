<?php

// $Id$

// \brief Wiki plugin to redirect to another page.
// @author damian aka damosoft 30 March 2004

function wikiplugin_redirect_help() {
        return tra("Redirects you to another wiki page").":<br />~np~{REDIRECT(page=pagename [,url=http://foobar])/}~/np~";
}

function wikiplugin_redirect_info() {
	return array(
		'name' => tra('Redirect'),
		'documentation' => 'PluginRedirect',			
		'description' => tra('Redirect the user to a wiki page or generic URL.'),
		'prefs' => array( 'wikiplugin_redirect' ),
		'validate' => 'arguments',
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page Name'),
				'description' => tra('Wiki page name to redirect to.'),
			),
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Complete URL, internal or external.'),
			),
		),
	);
}

function wikiplugin_redirect($data, $params, $offset, $options) {
	global $tikilib;
	extract ($params,EXTR_SKIP);
	$areturn = '';

	if (!isset($page)) {$areturn = "REDIRECT plugin: No page specified!";}
	if (!isset($url)) {$areturn += "REDIRECT plugin: No url specified!";}
	if ((isset($_REQUEST['redirectpage']))) {
		$areturn = "REDIRECT plugin: redirect loop detected!";
	} else if (isset($options['print']) && $options['print'] == 'y') {
		$info = $tikilib->get_page_info(isset($page)?$page: $url);
		return $tikilib->parse_data($info['data'], $options);
	} else {
		/* SEO: Redirect with HTTP status 301 - Moved Permanently than default 302 - Found */
		if (isset($page)) {
			header("Location: tiki-index.php?page=$page&redirectpage=".$_REQUEST['page'], true, 301);
			exit;
		}
		if (isset($url)) {
			header("Location: $url");
			exit;
		}
	}
		
	return $areturn;
}
