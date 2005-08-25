<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class RegistrationLib extends TikiLib {

  function RegistrationLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to RegistrationLib constructor");  
    }
    $this->db = $db;  
  }
  
    // Validate emails...
  function SnowCheckMail($Email,$sender_email,$novalidation,$Debug=false)
  {
	global $validateEmail;
	$Debug=true;
	if (!isset($_SERVER["SERVER_NAME"])) {
		$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
	}	
    $HTTP_HOST=$_SERVER['SERVER_NAME']; 
    $Return =array();
    // $Debug = true;
    // Variable for return.
    // $Return[0] : [true|false]
    // $Return[1] : Processing result save.

//    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email)) {
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

  function saveRegistration() {
  global $allowRegister, $_REQUEST, $_SESSION, $min_pass_length, $useRegisterPasscode, $validateUsers;
  global $sender_email, $default_sender_email, $contact_user, $pass_chr_num, $validateRegistration;
  global $userlib, $logslib, $smarty, $tikilib;

  if($allowRegister != 'y') {
    header("location: index.php");
    exit;
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
    $smarty->assign('msg',tra("The passwords don't match"));
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
  if(strtolower($_REQUEST["name"])=='admin') {
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
      $ret = $this->SnowCheckMail($_REQUEST["email"],$sender_email,$novalidation);
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

    if($email_valid != 'no') {
                if($validateUsers == 'y') {
                        //$apass = addslashes(substr(md5($tikilib->genPass()),0,25));
                        $apass = addslashes(md5($tikilib->genPass()));
                        $foo = parse_url($_SERVER["REQUEST_URI"]);
                        $foo1=str_replace("tiki-register","tiki-login_validate",$foo["path"]);
                        $machine =$tikilib->httpPrefix().$foo1;
                        $userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"]);


                        $logslib->add_log('register','created account '.$_REQUEST["name"]);
                        $smarty->assign('mail_machine',$machine);
                        $smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
                        $smarty->assign('mail_user',$_REQUEST["name"]);
                        $smarty->assign('mail_apass',$apass);
                        $smarty->assign('mail_email',$_REQUEST['email']);
                        include_once("lib/notifications/notificationemaillib.php");
                        if (isset($validateRegistration) and $validateRegistration == 'y') {
                                $smarty->assign('msg',$smarty->fetch('mail/user_validation_waiting_msg.tpl'));
                                if ($default_sender_email == NULL or !$default_sender_email) {
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
                                        if (!$mail->send(array($default_sender_email)))
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
                        $userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"],'');
                        $logslib->add_log('register','created account '.$_REQUEST["name"]);

                        $smarty->assign('msg',$smarty->fetch('mail/user_welcome_msg.tpl'));
                        $smarty->assign('showmsg','y');
                }

                // Custom fields
                foreach ($customfields as $custpref=>$prefvalue ) {
                    //print $_REQUEST[$customfields[$custpref]['prefName']];
                    $tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
                }

                $emails = $notificationlib->get_mail_events('user_registers','*');
                if (count($emails)) {
                        include_once("lib/notifications/notificationemaillib.php");
                        $smarty->assign('mail_user',$_REQUEST["name"]);
                        $smarty->assign('mail_date',date("U"));
                        $smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
                        sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
                }

        }

    }

    function registerForm() {
        global $allowRegister, $smarty;
        if($allowRegister != 'y') {
            header("location: index.php");
            exit;
            die;
        }

        ask_ticket('register');

        $smarty->assign('mid','tiki-register.tpl');
        $smarty->display("tiki.tpl");
    }
  
  
}

$registrationlib= new RegistrationLib($dbTiki);

?>
