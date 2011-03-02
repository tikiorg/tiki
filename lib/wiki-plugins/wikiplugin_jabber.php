<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Tiki plugin for Jabber
   Written by Dennis Heltzel, 9/15/2003
   You can only use this plugin to connect to a jabber server that is running on the same server as the http server for tiki. This is a constraint of the applet for security reasons. you can get more info about that at jabberapplet.jabberstudio.org.

   Example usage:
   {JABBER(height=>200,width=>200,xmlhostname=>jabber.org,defaultColor=>255,255,255)}{JABBER}

   To make this plugin work:
   1) get the JabberApplet from here: http://jabberapplet.jabberstudio.org/JabberApplet.jar
   2) put it in a directory called jabber under $TIKI_BASE/lib, the path must resolve to: lib/jabber/JabberApplet.jar
   3) make sure your local jabber server is started and you know it's name
   4) substitute the name of your local jabber server for 'jabber.org' in the example avobe and put it into a wiki page.

*/

function wikiplugin_jabber_help() {
	return tra("Runs a Java applet to access a local Jabber service").":<br />~np~{JABBER(height=>200,width=>200,xmlhostname=>jabber.org,defaultColor=>255,255,255)}{JABBER}~/np~. See lib/wiki-plugins/wikiplugin_jabber.php to make this plugin work";
}

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
