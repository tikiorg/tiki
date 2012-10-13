<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id:$

if (!empty($_REQUEST['type']) && $_REQUEST['type'] == 'request') {
	$title = 'Request blocked';
	$explanation = 'The action you tried looks suspicious and we didn\'t let it happen.';
	$back = '<p><a href="javascript:history.back()">Go back</a></p>';
} elseif (!empty($_REQUEST['type']) && $_REQUEST['type'] == 'session') {
	$title = 'Session blocked';
	$explanation = 'The actions you have been performing during your session added up to look very suspicious, so we have blocked you from accessing this site.';
	$back = '';
} else {
	$title = 'Blocked';
	$explanation = 'We do not know exactly how you got here, but it seems your request was blocked.';
	$back = '<p><a href="javascript:history.back()">Go back</a></p>';
}

if (file_exists('templates/styles/ids_blocked.local.html')) {
	$html = file_get_contents('templates/styles/ids_blocked.local.html');
} else {
	$html = file_get_contents('templates/styles/ids_blocked.html');
}

$html = str_replace('{explanation}', $explanation, $html);
$html = str_replace('{title}', $title, $html);
$html = str_replace('{back}', $back, $html);

header("HTTP/1.0 503 Service Unavailable");

echo $html;
