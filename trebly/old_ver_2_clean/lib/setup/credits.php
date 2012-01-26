<?php

if( $user )
{
	require_once( 'lib/credits/creditslib.php' );
	if (isset($prefs['account_suspended']) && $prefs['account_suspended'] != 'y') {
		$tiki_user_credits = $creditslib->getScaledCredits( $tikilib->get_user_id($user) );
	} else {
		$tiki_user_credits = 0;
	}
	$smarty->assign( 'tiki_user_credits', $tiki_user_credits );
}