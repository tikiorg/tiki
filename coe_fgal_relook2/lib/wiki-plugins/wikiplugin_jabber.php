<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_jabber_info() {
	return array(
		'name' => tra('Jabber'),
		'documentation' => 'PluginJabber',
		'description' => tra('Chat using Jabber'),
		'prefs' => array( 'wikiplugin_jabber' ),
		'icon' => 'pics/icons/comments.png',
		'params' => array(
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Applet height in pixels'),
				'default' => 200,
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Applet width in pixels'),
				'default' => 200,
			),
			'xmlhostname' => array(
				'required' => false,
				'name' => tra('XML Host Name'),
				'description' => tra('Web site where XML is hosted. Default is jabber.org'),
				'default' => 'jabber.org',
			),
			'defaultColor' => array(
				'required' => false,
				'name' => tra('Default Color'),
				'description' => tra('Set default color. Default is 255,255,255'),
				'default' => '255,255,255',
			),
		),
	);
}

function wikiplugin_jabber($data,$params) {
  global $userlib;
  global $user;
  extract($params,EXTR_SKIP);

  if(!isset($height)) {
    $height = 200;
  }
  if(!isset($width)) {
    $width = 200;
  }
  if(!isset($xmlhostname)) {
    $xmlhostname = 'jabber.org';
  }
  if(!isset($defaultColor)) {
    $defaultColor = '255,255,255';
  }
  $userpwd = $userlib->get_user_password($user);

  $result='<APPLET ARCHIVE="lib/jabber/JabberApplet.jar" CODE="org/jabber/applet/JabberApplet.class" HEIGHT='.$height.' WIDTH='.$width.' VIEWASTEXT>';
  $result.='<param name="xmlhostname" value="'.$xmlhostname.'">';
  $result.='<param name="defaultColor" value="'.$defaultColor.'">';
  if(isset($user)) {
    $result.='<param name="user" value="'.$user.'">';
  }
  if($userpwd != '') {
    $result.='<param name="pwd" value="'.$userpwd.'">';
  }
  $result.='</APPLET>';
  return $result;
}
