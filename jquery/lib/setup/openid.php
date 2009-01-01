<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// OpenID support
if( isset( $_SESSION['openid_userlist'] ) && isset( $_SESSION['openid_url'] ) )
{
	$smarty->assign( 'openid_url', $_SESSION['openid_url'] );
	$smarty->assign( 'openid_userlist', $_SESSION['openid_userlist'] );
}
else
{
	$smarty->assign( 'openid_url', '' );
	$smarty->assign( 'openid_userlist', array() );
}
