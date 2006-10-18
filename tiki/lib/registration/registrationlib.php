<?php
/**
 * @class RegistrationLib
 *
 * This class provides registration functions
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created: 2003/3/21 16:48
 * @date last-modified: $Date: 2006-10-18 19:44:20 $
 * $Header: /cvsroot/tikiwiki/tiki/lib/registration/registrationlib.php,v 1.33 2006-10-18 19:44:20 luciash Exp $
 */

//this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

require_once('lib/tikilib.php'); # httpScheme(), get_user_preference
require_once('lib/webmail/tikimaillib.php');
require_once( 'lib/db/tikitable.php' );
require_once( 'lib/db/tiki_registration_fields.php' );

if (!isset($Debug)) $Debug = false;

class RegistrationLib extends TikiLib {

  function RegistrationLib($db) 
  {
    # this is probably unneeded now
    if(!$db) {
      die("Invalid db object passed to RegistrationLib constructor");  
    }
    $this->db = $db;  
  }
  
    // Validate emails...
  function SnowCheckMail($Email,$sender_email,$novalidation,$Debug=false)
  {
	global $validateEmail;
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
    list ( $Username, $Domain ) = split ("@",$Email);
	
	if($validateEmail == 'n') {
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
              for ( $i = 0,$j = 1; $i < count ( $MXHost ); $i++,$j++ ) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result($j) - $MXHost[$i]<BR>";
        }
            }
        }
        // Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
        // $ConnectAddress socket connection address.
        $ConnectAddress = $MXHost[0];
    }
    else {
        // If there is no MX record simply @ to next time address socket connection do .
        $ConnectAddress = $Domain;
        if ($Debug) echo "Confirmation : MX record about {$Domain} does not exist.<br>";
    }

	if ($novalidation != 'yes') {	// Skip the connecting test if it didn't work the first time
	    // fsockopen function reference : http://www.php.net/manual/en/function.fsockopen.php
	    @$Connect = fsockopen ( $ConnectAddress, 25 );

	    // Success in socket connection
	    if ($Connect)
	    {
	        if ($Debug) echo "Connection succeeded to {$ConnectAddress} SMTP.<br>";
	        // Judgment is that service is preparing though begin by 220 getting string after connection .
	        // fgets function reference : http://www.php.net/manual/en/function.fgets.php
	        if ( ereg ( "^220", $Out = fgets ( $Connect, 1024 ) ) ) {

	            // Inform client's reaching to server who connect.
	            fputs ( $Connect, "HELO $HTTP_HOST\r\n" );
                if ($Debug) echo "Run : HELO $HTTP_HOST<br>";
		        $Out = fgets ( $Connect, 1024 ); // Receive server's answering cord.

	            // Inform sender's address to server.
	            fputs ( $Connect, "MAIL FROM: <{$sender_email}>\r\n" );
                if ($Debug) echo "Run : MAIL FROM: &lt;{$sender_email}&gt;<br>";
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
                if ( !ereg ( "^250", $From ) || !ereg ( "^250", $To )) {
                    $Return[0]=false;
                    $Return[1]='not_recognized';
                    if ($Debug) echo "{$Email} is not recognized by the mail server.<br>";
                    return $Return;
                }
	        }
	    }
	    // Failure in socket connection
	    else {
	        $Return[0]=false;
	        $Return[1]="Cannot connect to mail server ({$ConnectAddress}).";
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

  function get_hiddenfields() {
      $table = new TikiRegistrationFields();
      return $table->getHiddenFields();
  }

  /**
   *  Default Tikiwiki 'user_registers' callback
   *  validates data and creates a new user in the database on user registration
   *  @access private
   *  @returns true if user data validates and user was created, false (or never returns) otherwise
   */
  function callback_tikiwiki_save_registration($raisedBy, $data) {
      global $allowRegister;

      if($allowRegister != 'y') {
          header("location: index.php");
          die;
      }

      if ( $this->validate_registration() and $this->create_user() ) {
          return true;
      }
      return false;
  }


  /**
   *  Validate the registration data
   *  @access private
   *  @returns true if registration data is valid, false (or never returns) otherwise
   */
  function validate_registration() {
  global $allowRegister, $_REQUEST, $_SESSION, $min_pass_length, $useRegisterPasscode, $validateUsers, $rnd_num_reg;
  global $sender_email, $contact_user, $pass_chr_num, $validateRegistration, $email_valid;
  global $userlib, $logslib, $smarty, $tikilib;
  global $Debug;

  if($allowRegister != 'y') {
    header("location: index.php");
    die;
  }

  // novalidation is set to yes if a user confirms his email is correct after tiki fails to validate it
  if (!isset($_REQUEST['novalidation'])) {
        $novalidation = '';
  } else {
        $novalidation = $_REQUEST['novalidation'];
  }

  check_ticket('register');
  if($novalidation != 'yes' and ($_REQUEST["pass"] <> $_REQUEST["passAgain"])) {
    $smarty->assign('msg',tra("The passwords do not match"));
    $smarty->display("error.tpl");
    die;
  }
  list($cant, $u) = $userlib->other_user_exists_case_insensitive($_REQUEST["name"]);
  if($cant > 0) {
    $smarty->assign('msg',tra("User already exists").": ".$u);
    $smarty->display("error.tpl");
    die;
  }

  if($rnd_num_reg == 'y') {
        if($novalidation != 'yes' and(!isset($_SESSION['random_number']) || $_SESSION['random_number']!=$_REQUEST['regcode'])) {
    $smarty->assign('msg',tra("Wrong registration code"));
    $smarty->display("error.tpl");
    die;
        }
  }

  // VALIDATE NAME HERE
  // 'CustomFields' is an interal database token used to store additional fields to be used by the registration form
  // for historical reasons, this data is stored in the user_preferences table and so the string is not available for
  // use as a username
  if(strtolower($_REQUEST["name"])=='admin' || strtolower($_REQUEST["name"])=='customfields') {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display("error.tpl");
    die;
  }

  if(strlen($_REQUEST["name"])>37) {
    $smarty->assign('msg',tra("Username is too long"));
    $smarty->display("error.tpl");
    die;
  }

  if(strstr($_REQUEST["name"],' ')) {
    $smarty->assign('msg',tra("Username cannot contain whitespace"));
    $smarty->display("error.tpl");
    die;
  }

  //Validate password here
  if(strlen($_REQUEST["pass"])<$min_pass_length) {
    $smarty->assign('msg',tra("Password should be at least").' '.$min_pass_length.' '.tra("characters long"));
    $smarty->display("error.tpl");
    die;
  }

  // Check this code
  if($pass_chr_num == 'y') {
    if(!preg_match_all("/[0-9]+/",$_REQUEST["pass"],$foo) || !preg_match_all("/[A-Za-z]+/",$_REQUEST["pass"],$foo)) {
      $smarty->assign('msg',tra("Password must contain both letters and numbers"));
      $smarty->display("error.tpl");
      die;
    }
  }

  if(!preg_match_all("/[A-Z0-9a-z\_\-]+/",$_REQUEST["name"],$matches)) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display("error.tpl");
    die;
  }

  // Check the mode
  if($useRegisterPasscode == 'y') {
    if(($_REQUEST["passcode"]!=$tikilib->get_preference("registerPasscode",md5($tikilib->genPass()))))
    {
      $smarty->assign('msg',tra("Wrong passcode you need to know the passcode to register in this site"));
      $smarty->display("error.tpl");
      die;
    }
  }

    $email_valid = 'yes';
    if($validateUsers=='y') {
      $ret = $this->SnowCheckMail($_REQUEST["email"],$sender_email,$novalidation, $Debug);
      if(!$ret[0]) {
        if($ret[1] == 'not_recognized') {
                        $smarty->assign('notrecognized','y');
                        $smarty->assign('email',$_REQUEST['email']);
                        $smarty->assign('login',$_REQUEST['name']);
                        $smarty->assign('password',$_REQUEST['pass']);
                        $email_valid = 'no';
        } else {
//                      $smarty->assign('msg',"$ret[1]");
                $smarty->assign('msg',tra("Invalid email address. You must enter a valid email address"));
                $smarty->display("error.tpl");
                $email_valid = 'no';
                die;
        }
      }
    }
    if ($email_valid == 'no')
       return false;
    else
       return true;
  }


  /**
   *  Create a new user in the database on user registration
   *  @access private
   *  @returns true on success, false to halt event proporgation
   */
  function create_user() {
        global $_REQUEST, $_SERVER, $email_valid, $validateUsers, $registrationlib_apass, $customfields;
        global $userlib, $tikilib;
	global $Debug;

	if ($Debug) print "::create_user";

	if($email_valid != 'no') {
                if($validateUsers == 'y') {
			//$apass = addslashes(substr(md5($tikilib->genPass()),0,25));
                        $apass = addslashes(md5($tikilib->genPass()));
			$registrationlib_apass = $apass;

                        $userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"]);
		} else {
                        $userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"],'');
		}
                // Custom fields
                foreach ($customfields as $custpref=>$prefvalue ) {
                    if( $customfields[$custpref]['show'] ) {
                        //print $_REQUEST[$customfields[$custpref]['prefName']];
                        $tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
                    }
                }
	}
	return true;
  }


  /**
   *  A default TikiWiki callback that enters a line in the logs on user registration
   *  @access private
   *  @returns true on success, false to halt event proporgation
   */
  function callback_logslib_user_registers($raisedBy, $data) {
	global $logslib;
	$logslib->add_log('register','created account '.$data['user']);
	return true;
  }

  /**
   *  A default TikiWiki callback that sends the welcome email on user registraion
   *  @access private
   *  @returns true on success, false to halt event proporgation
   */
  function callback_tikiwiki_send_email($raisedBy, $data) {
	global $_REQUEST, $_SESSION, $_SERVER, $min_pass_length, $useRegisterPasscode, $validateUsers, $registrationlib_apass;
	global $contact_user, $pass_chr_num, $validateRegistration, $email_valid;
	global $smarty, $tikilib, $userlib;
	global $Debug;

	if ($Debug) print "::send_email";

	$sender_email = $userlib->get_admin_email();
	$mail_user = $data['user'];
	$mail_site = $data['mail_site'];

	if($email_valid != 'no') {
                if($validateUsers == 'y') {
			//$apass = addslashes(substr(md5($tikilib->genPass()),0,25));
			$apass = $registrationlib_apass;
			$foo = parse_url($_SERVER["REQUEST_URI"]);
                        $foo1=str_replace("tiki-register","tiki-login_validate",$foo["path"]);
                        $machine =$tikilib->httpPrefix().$foo1;

                        $smarty->assign('mail_machine',$machine);
                        $smarty->assign('mail_site',$mail_site);
                        $smarty->assign('mail_user',$mail_user);
                        $smarty->assign('mail_apass',$apass);
			$registrationlib_apass = "";
                        $smarty->assign('mail_email',$_REQUEST['email']);
                        include_once("lib/notifications/notificationemaillib.php");
                        if (isset($validateRegistration) and $validateRegistration == 'y') {
                                $smarty->assign('msg',$smarty->fetch('mail/user_validation_waiting_msg.tpl'));
                                if ($sender_email == NULL or !$sender_email) {
                                        include_once('lib/messu/messulib.php');
                                        $mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
                                        $mail_subject = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
                                        $messulib->post_message($contact_user,$contact_user,$contact_user,'',$mail_subject,$mail_data,5);
                                } else {
                                        $mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
                                        $mail = new TikiMail();
                                        $mail->setText($mail_data);
                                        $mail_data = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
                                        $mail->setSubject($mail_data);
                                        if (!$mail->send(array($sender_email)))
                                                $smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
                                }
                        } else {
                                $mail_data = $smarty->fetch('mail/user_validation_mail.tpl');
                                $mail = new TikiMail();
                                $mail->setText($mail_data);
                                $mail_data = $smarty->fetch('mail/user_validation_mail_subject.tpl');
                                $mail->setSubject($mail_data);
                                if (!$mail->send(array($_REQUEST["email"])))
                                        $smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
                                else
                                        $smarty->assign('msg',$smarty->fetch('mail/user_validation_msg.tpl'));
                        }
                        $smarty->assign('showmsg','y');
                } else {
                        $smarty->assign('msg',$smarty->fetch('mail/user_welcome_msg.tpl'));
                        $smarty->assign('showmsg','y');
                }
	}
	return true;
    }


	/**
         *  A callback that performs email notifications when a new user registers
	 *  @access private
	 *  @returns true on success, false to halt event proporgation
	 */
	function callback_tikimail_user_registers($raisedBy, $data) {
		global $notificationlib, $smarty;
		include_once("lib/notifications/notificationlib.php");
                $emails = $notificationlib->get_mail_events('user_registers','*');
                if (count($emails)) {
                        $smarty->assign('mail_user',$data['user']);
                        $smarty->assign('mail_date',date("U"));
                        $smarty->assign('mail_site',$data['mail_site']);
                        sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
                }
		return true;
        }


    /**
     *  Display the registration form
     */
    function registration_form() {
        global $allowRegister, $smarty;
        if($allowRegister != 'y') {
            header("location: index.php");
            die;
        }

        ask_ticket('register');
    }
}
  
global $dbTiki;
$registrationlib= new RegistrationLib($dbTiki);

?>
