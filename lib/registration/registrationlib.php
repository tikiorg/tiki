<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @class RegistrationLib
 *
 * This class provides registration functions
 *
 * @date created: 2003/3/21 16:48
 */

//this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

require_once('lib/tikilib.php'); # httpScheme(), get_user_preference
require_once('lib/webmail/tikimaillib.php');
require_once( 'lib/db/tiki_registration_fields.php' );

if (!isset($Debug)) $Debug = false;

class RegistrationLib extends TikiLib
{

    // Validate emails...
  function SnowCheckMail($Email, $sender_email, $novalidation, $Debug=false)
  {
	global $prefs;
	if (!isset($_SERVER["SERVER_NAME"])) {
		$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
	}	
    $HTTP_HOST=$_SERVER['SERVER_NAME']; 
    $Return =array();
    // Variable for return.
    // $Return[0] : [true|false]
    // $Return[1] : Processing result save.

    //Fix by suilinma
    if (!eregi("^[-_a-z0-9+]+(\\.[-_a-z0-9+]+)*\\@([-a-z0-9]+\\.)*([a-z]{2,4})$", $Email)) {
	// luci's regex that also works
	//	if (!eregi("^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$", $Email)) {
        $Return[0]=false;
        $Return[1]="${Email} is E-Mail form that is not right.";
        if ($Debug) echo "Error : {$Email} is E-Mail form that is not right.<br>";
        return $Return;
    }
    else if ($Debug) echo "Confirmation : {$Email} is E-Mail form that is right.<br>";

    // E-Mail @ by 2 by standard divide. if it is $Email this "lsm@ebeecomm.com"..
    // $Username : lsm
    // $Domain : ebeecomm.com
    // list function reference : http://www.php.net/manual/en/function.list.php
    // split function reference : http://www.php.net/manual/en/function.split.php
    list ($Username, $Domain) = explode ("@", $Email);
	
	if($prefs['validateEmail'] == 'n') {
		$Return[0]=true;
		$Return[1]="The email appears to be correct."; 
		Return $Return;
	}

    // That MX(mail exchanger) record exists in domain check .
    // checkdnsrr function reference : http://www.php.net/manual/en/function.checkdnsrr.php
    if ( checkdnsrr ( $Domain, "MX" ) )  {
        if($Debug) echo "Confirmation : MX record about {$Domain} exists.<br>";
        // If MX record exists, save MX record address.
        // getmxrr function reference : http://www.php.net/manual/en/function.getmxrr.php
        if ( getmxrr ($Domain, $MXHost))  {
					if($Debug) {
						echo "Confirmation : Is confirming address by MX LOOKUP.<br>";
						$j=0;
						foreach($MXHost as $mxh) {
							echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result(".++$j.") - $mxh<BR>";
						}
					}
        }
        // Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
        // $ConnectAddress socket connection address.
        $ConnectAddress = $MXHost[0];
    } else {
        // If there is no MX record simply @ to next time address socket connection do .
        $ConnectAddress = $Domain;
        if ($Debug) echo "Confirmation : MX record about {$Domain} does not exist.<br>";
		if ($novalidation == 'mini') {
			$Return[0]=false;
    		$Return[1]="{$Email} domain is incorrect.";
    		return $Return;
		}
    }

	if ($novalidation != 'yes' && $novalidation != 'mini') {	// Skip the connecting test if it didn't work the first time
	    // fsockopen function reference : http://www.php.net/manual/en/function.fsockopen.php
	    @$Connect = fsockopen ( $ConnectAddress, 25 );

	    // Success in socket connection
	    if ($Connect) {
	        if ($Debug) 
						echo "Connection succeeded to {$ConnectAddress} SMTP.<br>";
	        // Judgment is that service is preparing though begin by 220 getting string after connection .
	        // fgets function reference : http://www.php.net/manual/en/function.fgets.php
	        if ( ereg ("^220", $Out = fgets ($Connect, 1024 ))) {
	            // Inform client's reaching to server who connect.
	            fputs ( $Connect, "HELO $HTTP_HOST\r\n" );
                if ($Debug) echo "Run : HELO $HTTP_HOST<br>";
		        $Out = fgets ( $Connect, 1024 ); // Receive server's answering cord.

	            // Inform sender's address to server.
	            fputs ( $Connect, "MAIL FROM: <{$prefs['sender_email']}>\r\n" );
                if ($Debug) echo "Run : MAIL FROM: &lt;{$prefs['sender_email']}&gt;<br>";
		        $From = fgets ( $Connect, 1024 ); // Receive server's answering cord.

	            // Inform listener's address to server.
	            fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" );
                if ($Debug) echo "Run : RCPT TO: &lt;{$Email}&gt;<br>";
		        $To = fgets ( $Connect, 1024 ); // Receive server's answering cord.

	            // Finish connection.
	            fputs ( $Connect, "QUIT\r\n");
                if ($Debug) echo "Run : QUIT<br>";

	            fclose($Connect);

                // Server's answering cord about MAIL and TO command checks.
                // Server about listener's address reacts to 550 codes if there does not exist
                // checking that mailbox is in own E-Mail account.
                if (!ereg ("^250", $From ) || !ereg ( "^250", $To)) {
                    $Return[0] = false;
                    $Return[1] = 'not_recognized';
                    if ($Debug) echo "{$Email} is not recognized by the mail server.<br>";
                    return $Return;
                }
	        }
	    }
	    // Failure in socket connection
	    else {
	        $Return[0] = false;
	        $Return[1] = "Cannot connect to mail server ({$ConnectAddress}).";
	        if ($Debug) echo "Cannot connect to mail server ({$ConnectAddress}).<br>";
	        return $Return;
	    }
	}
    $Return[0]=true;
    $Return[1]="{$Email} is valid.";
    return $Return;
  }


  function get_customfields($user=false) {
      $table = new TikiRegistrationFields();
      return $table->getVisibleFields2($user);
  }       


  /**
   *  Create a new user in the database on user registration
   *  @access private
   *  @returns true on success, false to halt event proporgation
   */
  function create_user() {
		global $_REQUEST, $_SERVER, $email_valid, $prefs
			, $registrationlib_apass, $customfields, $userlib, $tikilib, $Debug
			;

		if ($Debug) 
			print "::create_user";

		if ($email_valid != 'no') {
			if ($prefs['validateUsers'] == 'y') {
				//$apass = addslashes(substr(md5($tikilib->genPass()), 0, 25));
        $apass = addslashes(md5($tikilib->genPass()));
				$registrationlib_apass = $apass;
				$userlib->add_user($_REQUEST["name"], $apass, $_REQUEST["email"], $_REQUEST["pass"]);
			} else {
				$userlib->add_user($_REQUEST["name"], $_REQUEST["pass"], $_REQUEST["email"], '');
			}

			// Custom fields
			foreach ($customfields as $custpref=>$prefvalue ) {
				if ($customfields[$custpref]['show']) {
					//print $_REQUEST[$customfields[$custpref]['prefName']];
					$tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
				}
			}
		}
		return true;
  }
}
$registrationlib = new RegistrationLib;
