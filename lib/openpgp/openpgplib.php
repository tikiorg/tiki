<?php

/////////////////////////////////////////////////////////////////////////////
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//
// PURPOSE:
// A brief OpenPGP support class for Tiki OpenPGP functionality in
//  - webmail
//  - ZF based mail
//  - newsletters
//  - admin notifications
//
//
//
// CHANGE HISTORY:
// v0.10
// 2012-11-04	hollmeer: Collected all functions into intial version openpgplib.php.
//		Minimal preparation/calling portions remain in caller sources,
//		as it seems so far adequate with current approch to leave
//		such portions e.g. there
//		NOTE: Zend Framework as is wrapped by bringing/changing
//		      necessary classes from
//				Zend/Mail/ and
//				Zend/Mail/Transport/
//		      into lib/openpgp/ per now. No patches needed anymore
//		      into ZF to enable 100% PGP/MIME encryption.
// v0.11
// 2014-11-04	hollmeer: Protected function naming to _xxxx
// v0.12
// 2014-12-01	hollmeer: Changed all OpenGPG functionality configuration to use 
//		preferences
//
//
//
/////////////////////////////////////////////////////////////////////////////


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class OpenPGPLib
{

	//PGP/MIME HEADER CONSTANTS
	const MULTIPART_PGP_ENCRYPTED = 		'multipart/encrypted';
	const TYPE_PGP_PROTOCOL = 			'application/pgp-encrypted';
	const PGP_MIME_NOTE = 				'This is an OpenPGP/MIME encrypted message (RFC 2440 and 3156)';
	const TYPE_PGP_CONTENT_VERSION = 		'application/pgp-encrypted';
	const DESCRIPTION_PGP_CONTENT_VERSION = 	'PGP/MIME version identification';
	const PGP_MIME_VERSION_IDENTIFICATION = 	'Version: 1';
	const TYPE_PGP_CONTENT_ENCRYPTED = 		'application/octet-stream; name="encrypted.asc"';
	const DESCRIPTION_PGP_CONTENT_ENCRYPTED = 	'OpenPGP encrypted message';
	const DISPOSITION_PGP_CONTENT_INLINE = 		'inline; filename="encrypted.asc"';

	/**
	 * EOL character string used by transport
	 * @var string
	 * @access public
	 */
	private $EOL = "\n";


	/**
	* Full path to gpg
	* @var string
	* @access protected
	*/
	private $_gpg_path;

	/**
	* Full path to keyring directory
	* @var string
	* @access protected
	*/
	private $_gpg_home;

	/**
	* gpg signer idfile
	* @var string
	* @access protected
	*/
	private $_gpg_sgn_id;

	/**
	* gpg signer passphrase
	* @var string
	* @access protected
	*/
	private $_gpg_sgn_passphrase;

	/**
	* gpg signer full passfile path
	* @var string
	* @access protected
	*/
	private $_gpg_sgn_passfile_path;

	/**
	* gpg trust
	* depending on which version of GnuPG we're using there
	* are two different ways to specify "always trust"
	* @var string
	* @access protected
	*/
	private $_gpg_trust;

	/**
	* Constructor function. Set initial defaults.
	*/
	function OpenPGPLib()
	{
		global $prefs,$tiki_p_admin;

		$this->_gpg_path = $prefs['openpgp_gpg_path'];
		$this->_gpg_home = $prefs['openpgp_gpg_home'];
		$this->_gpg_sgn_id = $prefs['sender_email'];
		if ($prefs['openpgp_gpg_signer_passphrase_store'] == 'file') {
			$this->_gpg_sgn_passfile_path = $prefs['openpgp_gpg_signer_passfile'];
			$this->_gpg_sgn_passphrase = '';
		} else {
			$this->_gpg_sgn_passfile_path = '';
			$this->_gpg_sgn_passphrase = $prefs['openpgp_gpg_signer_passphrase'];
		}
		$this->_gpg_trust = '';

		$this->setCrlf();
	}

	/**
	* Accessor to set the CRLF style
	*/
	function setCrlf($crlf = "\n")
	{
		if (!defined('CRLF')) {
			define('CRLF', $crlf, true);
		}

		if (!defined('MAIL_MIMEPART_CRLF')) {
			define('MAIL_MIMEPART_CRLF', $crlf, true);
		}
	}

	/**
	 * Gnupg version check; sets internal variable once
	 *
	 * @access protected
	 * @return void
	 */
	protected function _gpg_check_version()
	{

		//////////////////////////////////////////
		// find which version of GnuPG we're using
		//////////////////////////////////////////

		///////////////////////////////
		// open the GnuPG process and get the reply
		// we're only concerned with the first line of output, so use "false" as last argument
		$commandline = $this->_gpg_path
					.' --version';
		$ret = $this->_gpg_exec_proc($commandline, NULL, false);

		/////////////////////////////////////////////////////
		// get the version (we are only concerned with the first line of output,
		// which was read from gpg-process-output as single-line-read into $ret[1]
		$gpg_version_output = $ret[0];

		///////////////////////////////////////////////
		// sanity check - see if we're working with gpg
		if (preg_match('/^gpg /', $gpg_version_output) == 0) {
			$error_msg = 'gpg executable is not GnuPG: "'.$this->_gpg_path.'"';
			trigger_error($error_msg, E_USER_ERROR);
			// if an error message directs you to the line above please
			// double check that your path to gpg is really GnuPG
			die();
		}

		/////////////////////////////////////////////////////////////
		// pick the version number out of $gpg_encrypt_version_output
		// we'll need this so we can determine the correct
		// way to tell GnuPG how to "always trust"
		$gpg_gpg_version = preg_replace('/^.* /', '', $gpg_version_output);

		////////////////////////////////////////////////////////
		// depending on which version of GnuPG we're using there
		// are two different ways to specify "always trust"
		if ("$gpg_gpg_version" < '1.2.3') {
			$this->_gpg_trust = '--always-trust';		// the old way
		} else {
			$this->_gpg_trust = '--trust-model always';	// the new way
		}

		/////////////////////////////////////////////
		// unset variables that we don't need anymore
		unset($gpg_version_output,
			$gpg_gpg_version,
			$commandline);

		////////////////////////////////////////
		// we're done checking the GnuPG version
		////////////////////////////////////////
		return;

	}

	/**
	 * Gnupg process call function
	 *
	 * @param string	$gpg_proc_call
	 * @param string	$gpg_proc_input
	 * @param boolean  	$read_multilines
	 * @access protected
	 * @return array
	 *		0 => process call output (STDOUT)
	 *		1 => warnings and notices (STDERR)
	 *		2 => exit status
	 */
	protected function _gpg_exec_proc($gpg_proc_call = '', $gpg_proc_input = NULL, $read_multilines = true)
	{

		if ($gpg_proc_call == '') die;

		//////////////////////////////////////////////
		// set up pipes for handling I/O to/from GnuPG
		$gpg_descriptorspec = array(
			0 => array("pipe", "r"),  // STDIN is a pipe that GnuPG will read from
			1 => array("pipe", "w"),  // STDOUT is a pipe that GnuPG will write to
			2 => array("pipe", "w")   // STDERR is a pipe that GnuPG will write to
		);

		///////////////////////////////
		// this opens the GnuPG process
		$gpg_process = proc_open(
			$gpg_proc_call,
			$gpg_descriptorspec,
			$gpg_pipes
		);

		//////////////////////////////////////////////////////////////////
		// this writes the "$gpg_encrypt_secret_message" to GnuPG on STDIN
		if (is_resource($gpg_process)) {
			if ($gpg_proc_input != NULL) {
				fwrite($gpg_pipes[0], $gpg_proc_input);
			}
			fclose($gpg_pipes[0]);

			/////////////////////////////////////////////////////////
			// this reads the output from GnuPG from STDOUT
			$gpg_proc_output = '';
			if ($read_multilines) {
				while (!feof($gpg_pipes[1])) {
					$gpg_proc_output .= fgets($gpg_pipes[1], 1024);
				}
				fclose($gpg_pipes[1]);
			} else {
				$gpg_proc_output = fgets($gpg_pipes[1], 1024);
			}

			/////////////////////////////////////////////////////////
			// this reads warnings and notices from GnuPG from STDERR
			$gpg_error_message = '';
			while (!feof($gpg_pipes[2])) {
				$gpg_error_message .= fgets($gpg_pipes[2], 1024);
			}
			fclose($gpg_pipes[2]);

			/////////////////////////////////////////
			// this collects the exit status of GnuPG
			$gpg_exit_status = proc_close($gpg_process);

			////////////////////////////////////////////
			// unset variables that are no longer needed
			// and can only cause trouble
			unset($gpg_descriptorspec,
				$gpg_process,
				$gpg_pipes);

			////////////////////////////////////
			// this returns an array containing:
			// [0] encrypted output (STDOUT)
			// [1] warnings and notices (STDERR)
			// [2] exit status
			return array($gpg_proc_output, $gpg_error_message,  $gpg_exit_status);
		} else {
			////////////////////////////////////////////
			// unset variables that are no longer needed
			// and can only cause trouble
			unset($gpg_descriptorspec,
				$gpg_process,
				$gpg_pipes);

			//////////////////////////////
			// set output as otherwise nothing
			$gpg_proc_output = '';
			$gpg_error_message = 'Fatal process call error: Process call failed!';
			$gpg_exit_status = 99;
			return array($gpg_proc_output, $gpg_error_message,  $gpg_exit_status);
		}

	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// Encryption function; encrypts & signs the message
	//
	// usage:
	// array gpg_encrypt(secret-message, recipients);
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Encryption function; encrypts & signs the message
	 *
	 * @param string	$secret-message
	 * @param array/string  $recipients
	 * @access public
	 * @return array
	 *		0 => encrypted message output (STDOUT)
	 *		1 => warnings and notices (STDERR)
	 *		2 => exit status
	 */
	function gpg_encrypt()
	{

		global $prefs;

		//////////////////////////////////////////////////////////
		// sanity check - make sure there are at least 2 arguments
		// any extra arguments are considered to be additional key IDs
		if (func_num_args() < 2) {
			trigger_error("gpg_encrypt() requires at least 2 arguments", E_USER_ERROR);
			// if an error message directs you to the line above please
			// double check that you are providing at least 2 arguments
			die();
		}

		////////////////////////////////
		// assign arguments to variables
		$gpg_args = func_get_args();
		$gpg_secret_message = array_shift($gpg_args);	// 1st argument - secret message; let the gpg process to deal with empty/faulty content

		///////////////////////////////////////////////////////////////////////
		// make sure that each recipient has the message encrypted to their key
		// the 2nd argument, and any subsequent arguments, are key IDs
		$gpg_recipient_list = '';
		foreach ($gpg_args as $gpg_recipient) {
			if (is_array($gpg_recipient)) {
				foreach ($gpg_recipient as &$item) {
					$gpg_recipient_list .= ' -r ' . $item;
				}
			} else {
				$gpg_recipient_list .= " -r ${gpg_recipient}";
			}
		}

		//////////////////////////////////////////
		// find which version of GnuPG we're using
		//////////////////////////////////////////
		if ($this->_gpg_trust == '') {
			$this->_gpg_check_version();
		}

		///////////////////////////////
		// open the GnuPG process and get the reply
		$commandline = '';
		if ($prefs['openpgp_gpg_signer_passphrase_store'] == 'file') { 
			// get signer-key passphrase from a file
			$commandline .= $this->_gpg_path
					.' --no-random-seed-file'
					.' --homedir '.$this->_gpg_home
					.' '.$this->_gpg_trust
					.' --batch'
					.' --local-user '.$this->_gpg_sgn_id
					.' --passphrase-file '.$this->_gpg_sgn_passfile_path
					.' -sea '.$gpg_recipient_list
					.' ';
		} else { 
			// get signer-key passphrase from preferences
			$commandline .= $this->_gpg_path
					.' --no-random-seed-file'
					.' --homedir '.$this->_gpg_home
					.' '.$this->_gpg_trust
					.' --batch'
					.' --local-user '.$this->_gpg_sgn_id
					.' --passphrase '.$this->_gpg_sgn_passphrase
					.' -sea '.$gpg_recipient_list
					.' ';
		}
		$ret = $this->_gpg_exec_proc($commandline, $gpg_secret_message);

		unset($gpg_args,
			$gpg_secret_message,
			$gpg_recipient_list,
			$commandline);

		////////////////////////////////////
		// this returns an array containing:
		// [0] encrypted output (STDOUT)
		// [1] warnings and notices (STDERR)
		// [2] exit status
		return $ret;

	}

	/////////////////////////////////////////////////////////////
	//
	// Get the public-key fingerprint for a key associated with the ID
	//
	/////////////////////////////////////////////////////////////

	/**
	 * Get public-key fingerprint
	 *
	 * @param string	$gpg_key_id
	 * @access public
	 * @return array
	 *		0 => public key gingerprint output (STDOUT)
	 *		1 => warnings and notices (STDERR)
	 *		2 => exit status
	 */
	function gpg_getFingerprint($gpg_key_id = NULL)
	{

		//////////////////////////////////////////////////////////
		// sanity check - make sure there is 1 argument
		if ($gpg_key_id == NULL) {
			trigger_error("gpg_getFingerprint() requires 1 argument", E_USER_ERROR);
			// if an error message directs you to the line above please
			// double check that you are providing 1 argument
			die();
		}

		///////////////////////////////////////////////////////////////////////
		// the argument is key ID; if array, accept only the first
		$gpg_key_id_to_return = '';
		if (is_array($gpg_key_id)) {
			foreach ($gpg_key_id as &$item) {
				$gpg_key_id_to_return .= $item;
				break;
			}
		} else {
			$gpg_key_id_to_return .= $gpg_key_id;
		}

		//////////////////////////////////////////
		// find which version of GnuPG we're using
		//////////////////////////////////////////
		if ($this->_gpg_trust == '') {
			$this->_gpg_check_version();
		}

		///////////////////////////////
		// open the GnuPG process and get the reply
		$commandline = $this->_gpg_path
					.' --homedir '.$this->_gpg_home
					.' '.$this->_gpg_trust
					.' --fingerprint'
					.' --list-sigs '.$gpg_key_id_to_return
					.' ';
		$ret = $this->_gpg_exec_proc($commandline);

		unset($gpg_key_id_to_return,
			$commandline);

		////////////////////////////////////
		// this returns an array containing:
		// [0] fingerprint output (STDOUT)
		// [1] warnings and notices (STDERR)
		// [2] exit status
		return $ret;

	}

	//////////////////////////////////////////////////////////////////////////////
	//
	// Get the public-key ascii-armor-block for a key associated with the ID
	//
	//////////////////////////////////////////////////////////////////////////////

	/**
	 * Get public-key ascii armor block
	 *
	 * @param string	$gpg_key_id
	 * @access public
	 * @return array
	 *		0 => public key armor output (STDOUT)
	 *		1 => warnings and notices (STDERR)
	 *		2 => exit status
	 */
	function gpg_getPublicKey($gpg_key_id = NULL)
	{

		//////////////////////////////////////////////////////////
		// sanity check - make sure there is 1 argument
		if ($gpg_key_id == NULL) {
			trigger_error("gpg_getPublicKey() requires 1 argument", E_USER_ERROR);
			// if an error message directs you to the line above please
			// double check that you are providing 1 argument
			die();
		}

		///////////////////////////////////////////////////////////////////////
		// the argument is key ID; if array, accept only the first
		$gpg_key_id_to_return = '';
		if (is_array($gpg_key_id)) {
			foreach ($gpg_key_id as &$item) {
				$gpg_key_id_to_return .= $item;
				break;
			}
		} else {
			$gpg_key_id_to_return .= $gpg_key_id;
		}

		//////////////////////////////////////////
		// find which version of GnuPG we're using
		//////////////////////////////////////////
		if ($this->_gpg_trust == '') {
			$this->_gpg_check_version();
		}

		///////////////////////////////
		// open the GnuPG process and get the reply
		$commandline = $this->_gpg_path
					.' --homedir '.$this->_gpg_home
					.' '.$this->_gpg_trust
					.' --export --armor '.$gpg_key_id_to_return
					.' ';
		$ret = $this->_gpg_exec_proc($commandline);

		unset($gpg_key_id_to_return,
			$commandline);

		////////////////////////////////////
		// this returns an array containing:
		// [0] public key armor output (STDOUT)
		// [1] warnings and notices (STDERR)
		// [2] exit status
		return $ret;

	}

	/////////////////////////////////////////////////////////////////
	//
	// MESSAGE COMPOSING
	//
	/////////////////////////////////////////////////////////////////

	/**
	 * ALPHAFIELDS 2012-01-08: added to retrieve admin email public key armor block
	 * Returns the contact users' email or empty
	 *
	 * @access public
	 * @return string (contact users' email or empty)
	 */
	function get_admin_email_for_armor_block()
	{
		global $user, $prefs, $tikilib;
		$empty = '';
		return isset($prefs['sender_email']) ? $prefs['sender_email'] : $empty;
	}

	/////////////////////////////////////////////////////////////////////////////
	/**
	 * Function to get publickey ascii-armor-block and original headers into body
	 *
	 * @param  string	$req_priority
	 * @param  string	$req_to
	 * @param  string	$req_cc
	 * @access public
	 * @return array
	 *		0 => $prepend_email_body
	 *		1 => $user_armor
	 */
	function getPublickeyArmorBlock($req_priority,$req_to,$req_cc)
	{

		global $user;
		$userlib = TikiLib::lib('user');

		// get user email for publickey armor block retrieval
		$user_email = '';
		if ($user != 'admin') {
			$user_email = $userlib->get_user_email($user);
		} else {
			// NOTE: This function is in this lib-class, not in $userlib!
			$user_email = $this->get_admin_email_for_armor_block();
		}
		$user_armor = '';
		if ($user_email) {
			//retrieve armor block for keyid
			$gpg = $this->gpg_getPublicKey($user_email);
			// $gpg is an array containing
			// $gpg[0] armor output (STDOUT)
			// $gpg[1] warnings and notices (STDERR)
			// $gpg[2] exit status from gpg

			// test gpg's exit status
			if ("$gpg[2]" == '0') {
				// if the gpg command returned zero
				$user_armor = "\n\n--original sender public key below--\n\n".$gpg[0];
			} else {
				// if the gpg command returned non-zero
				$error_msg = 'OpenPGPLib: _getPublickeyArmorBlock() returned error code: '.$gpg[2];
				trigger_error($error_msg, E_USER_ERROR);
				// if an error message directs you to the line above please
				// double check that your gnupg-configuration, process-call commandline input, and other parameters are correct
			}
			// TODO: hardcoded addresses into preferences from db
			$gpg = $this->gpg_getFingerprint($user_email);
			// $gpg is an array containing
			// $gpg[0] fingerprint (STDOUT)
			// $gpg[1] warnings and notices (STDERR)
			// $gpg[2] exit status from gpg

			// test gpg's exit status
			if ("$gpg[2]" == '0') {
				// if the gpg command returned zero
				$user_armor = $user_armor . "\n-----fingerprint data below-------\n\n" . $gpg[0]
							  . "\n----------------------------------\n\n";
			} else {
				// if the gpg command returned non-zero
				$error_msg = 'OpenPGPLib: _getPublickeyArmorBlock() returned error code: '.$gpg[2];
				trigger_error($error_msg, E_USER_ERROR);
				// if an error message directs you to the line above please
				// double check that your gnupg-configuration, process-call commandline input, and other parameters are correct
			}
		}
		// generate a message has ID to be used as a consistent message referenceID across all recipients
		// and prepend it into message body
		$tmpstr = chunk_split(md5(rand().microtime()), 8, '-');
		$tmpstr = substr($tmpstr, 0, strlen($tmpstr)-1);
		$prepend_email_body = "ID:      "
					. $tmpstr
					. " (use this for message reference)\n"
					. "Prio:    " . $req_priority . "\n"
					. "From:    " . $user . " (".$user_email.")\n"
					. "To:      " . $req_to . "\n"
					. "Cc:      " . $req_cc . "\n\n";

		return array($prepend_email_body,$user_armor);
	}

	/////////////////////////////////////////////////////////////////
	//
	// WEBMAIL/htmlMimeMail GNUPG-ENCRYPTED PGP/MIME MAIL FUNCTIONS
	//
	/////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////
	/**
	 * Function to encode a header if necessary
	 * according to RFC2047
	 */
	function _encodeHeader($input, $charset = 'ISO-8859-1')
	{
		preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);

		foreach ($matches[1] as $value) {
			$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);

			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
		}

		return $input;
	}


	/////////////////////////////////////////////////////////////////
	/**
	 * Send a message to a user with gpg-armor block etc included
	 * A changed encryption-related version was copied/changed from lib/messu/messulib.pgp
	 * into lib/openpgp/openpgplib.php for prepending/appending content into
	 * message body

	 * @param  string	$user
	 * @param  string	$from
	 * @param  string	$to
	 * @param  string	$cc
	 * @param  string	$subject
	 * @param  string	$body
	 * @param  string	$prepend_email_body
	 * @param  string	$user_pubkeyarmor
	 * @param  string	$priority
	 * @param  string	$replyto_hash
	 * @param  string	$replyto_email
	 * @param  string	$bcc_sender
	 * @access public
	 * @return boolean	true/false
	 */
	function post_message_with_pgparmor_attachment($user, $from, $to, $cc, $subject, $body,
							$prepend_email_body, // NOTE this
							$user_pubkeyarmor,   // NOTE this
							$priority, $replyto_hash='', $replyto_email='', $bcc_sender = '')
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');

		$subject = strip_tags($subject);
		$body = strip_tags($body, '<a><b><img><i>');
		// Prevent duplicates
		$hash = md5($subject . $body);

		if ($tikilib->getOne("select count(*) from `messu_messages` where `user`=? and `user_from`=? and `hash`=?", array($user,$from,$hash))) {
			return false;
		}

		$query = "insert into `messu_messages`(`user`,`user_from`,`user_to`,`user_cc`,`subject`,`body`,`date`,`isRead`,`isReplied`,`isFlagged`,`priority`,`hash`,`replyto_hash`) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$tikilib->query($query, array($user,$from,$to,$cc,$subject,$body,(int) $tikilib->now,'n','n','n',(int) $priority,$hash,$replyto_hash));

		// Now check if the user should be notified by email
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix(true). $foo["path"];
		$machine = str_replace('messu-compose', 'messu-mailbox', $machine);
		if ($tikilib->get_user_preference($user, 'minPrio', 6) <= $priority) {
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			$email = $userlib->get_user_email($user);
			if ($email) {
				include_once('lib/webmail/tikimaillib.php');
				$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
				$smarty->assign('mail_machine', $machine);
				$smarty->assign('mail_date', $tikilib->now);
				$smarty->assign('mail_user', stripslashes($user));
				$smarty->assign('mail_from', stripslashes($from));
				$smarty->assign('mail_subject', stripslashes($subject));
				////////////////////////////////////////////////////////////////////////
				//                                                                    //
				// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION PREPARATION      //
				// USING lib/openpgp/opepgplib.php                                    //
				//                                                                    //
				// prepend original headers into email                                //
				$aux_body = $prepend_email_body . $body;
				$body = $aux_body;
				//                                                                    //
				////////////////////////////////////////////////////////////////////////
				$smarty->assign('mail_body', stripslashes($body));
				$mail = new TikiMail($user);
				$lg = $tikilib->get_user_preference($user, 'language', $prefs['site_language']);
				if (empty($subject)) {
					$s = $smarty->fetchLang($lg, 'mail/messu_message_notification_subject.tpl');
					$mail->setSubject(sprintf($s, $_SERVER["SERVER_NAME"]));
				} else {
					$mail->setSubject($subject);
				}
				$mail_data = $smarty->fetchLang($lg, 'mail/messu_message_notification.tpl');
				////////////////////////////////////////////////////////////////////////
				//                                                                    //
				// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION PREPARATION      //
				// USING lib/openpgp/opepgplib.php                                    //
				//                                                                    //
				// append pgparmor block and fingerprint into email                   //
				$mail_data .= $user_pubkeyarmor;
				//                                                                    //
				////////////////////////////////////////////////////////////////////////
				$mail->setText($mail_data);

				if ($userlib->user_exists($from)) {
					$from_email = $userlib->get_user_email($from);
					if ($bcc_sender === 'y' && !empty($from_email)) {
						$mail->setBcc($from_email);
					}
					if ($replyto_email !== 'y' && $userlib->get_user_preference($from, 'email is public', 'n') == 'n') {
						$from_email = '';	// empty $from_email if not to be used - saves getting it twice
					}
					if (!empty($from_email)) {
						$mail->setReplyTo($from_email);
					}
				}
				if (!empty($from_email)) {
					$mail->setFrom($from_email);
				}

				if (!$mail->send(array($email), 'mail')) {
					return false; //TODO echo $mail->errors;
				}
			}
		}
		return true;
	}

	/////////////////////////////////////////////////////////////////
	/**
	 * Function to insert original subject into text part body
	 *
	 * @param  array	$mail_headers / headers array
	 * @param  string	$text / The text body of the message
	 * @access public
	 * @return string	$text / The text body of the message prepended with original subject
	 */
	function prependSubjectToText($mail_headers,$text)
	{

		// AS THE Subject-header is hidden and contains a hash only in a pgp/mime encrypted message subject-header
		// extract the original subject	and prepend it into the text part
		// TODO: for some reason, newsletter notifications etc
		// do not set Subject yet at this point so no adjustment is generated for them;
		// what is the problem and solution?
		$subject = '';
		if (!empty($mail_headers['Subject'])) {
			$subject = $mail_headers['Subject'];
		}
		$ret = "******** PGP/MIME-ENCRYPTED MESSAGE ********\n"
			      . "Subject: " . $subject
			      . "\n\n"
			      . $text;
		return $ret;
	}

	/**
	 * Function to insert original subject into html part body
	 *
	 * @param  array	$mail_headers / headers array
	 * @param  string	$html / The html body of the message
	 * @param  string	$text / The text body of the message
	 * @access public
	 * @return array
	 *		0 => $html / The html body of the message prepended with original subject
	 *		1 => $html_text / The html_text body of the message prepended with original subject
	 */
	function prependSubjectToHtml($mail_headers,$html,$text)
	{

		// AS THE Subject-header is hidden and contains a hash only in a pgp/mime encrypted message subject-header
		// extract the original subject	and prepend it into the html part
		// TODO: for some reason, newsletter notifications etc
		// do not set Subject yet at this point so no adjustment is generated for them;
		// what is the problem and solution?
		$subject = '';
		if (!empty($mail_headers['Subject'])) {
			$subject = $mail_headers['Subject'];
		}
		$ret_html = "******** PGP/MIME-ENCRYPTED MESSAGE ********<br>"
					. "Subject: "  .$subject
					. "<br>"
					. $html;

		$ret_html_text = "******** PGP/MIME-ENCRYPTED MESSAGE ********<br>"
					. "Subject: " . $subject
					. "<br>"
					. $text;
		return array($ret_html,$ret_html_text);
	}

	/////////////////////////////////////////////////////////////////
	/**
	 * Prepate encryption of a mail using gnupg
	 *
	 * @param  array		$original_mail_headers / headers array
	 * @param  string	$original_mail_body / The main body of the message after building
	 * @param  string	$mail_build_params_head_charset
	 * @param  array/string	$recipients (needs to be not-imploded string or array)
	 * @access public
	 * @return array
	 *		0 => string $gnupg_header
	 *		1 => string $gnupg_subject
	 *		2 => string $pgpmime_encrypted_message_body
	 */
	function prepareEncryptWithMailSender($original_mail_headers,$original_mail_body,$mail_build_params_head_charset,$mail_recipients)
	{

	    	// Define gnupg/mime header variables; see constants above in this class
	    	$gnupg_mpe = OpenPGPLib::MULTIPART_PGP_ENCRYPTED;
	    	$gnupg_tpp = OpenPGPLib::TYPE_PGP_PROTOCOL;
	    	$gnupg_pmn = OpenPGPLib::PGP_MIME_NOTE;
	    	$gnupg_tpcv = OpenPGPLib::TYPE_PGP_CONTENT_VERSION;
	    	$gnupg_dpcv = OpenPGPLib::DESCRIPTION_PGP_CONTENT_VERSION;
	    	$gnupg_pmvi = OpenPGPLib::PGP_MIME_VERSION_IDENTIFICATION;
	    	$gnupg_tpce = OpenPGPLib::TYPE_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpce = OpenPGPLib::DESCRIPTION_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpci = OpenPGPLib::DISPOSITION_PGP_CONTENT_INLINE;


		// Define gnupg boundary
		$gnupg_boundary = '------gnupg-' . md5(rand().microtime());

		// Get flat representation of headers
		foreach ($original_mail_headers as $name => $value) {
			// first process Content-Type and discard original and set pgp/mime-required after loop
			if ($name == 'Content-Type') {
				$tmpvalue = $this->_encodeHeader($value, $mail_build_params_head_charset);
				// check if original message is just text/plain, so we can discard the entire
				// orig_header from being prepended into encrypted part
				$pos = strpos($tmpvalue, 'text/plain');
				if ($pos !== false) {
					// so text/plain ...set discard-flag...
					$discard_orig_header = true;
				} else {
					// reach here if not text/plain
					$orig_headers[] = $name . ': ' . $tmpvalue;
				}
			} else {
				// Get flat representation of original headers
				$orig_headers[] = $name . ': ' . $this->_encodeHeader($value, $mail_build_params_head_charset);
			}
		}

		// save original header to be added into encrypted part
		if ($discard_orig_header) {
			// original body is text/plain, so no headers there
			$orig_header = '';
		} else {
			$orig_header = implode(CRLF, $orig_headers);
		}

		//////////////////////////////////////////////////////////////////////////
		// NOTE: pgpmime_header; this must be returned as header (string here) for the mail() function
		$pgpmime_header = "Content-Type: {$gnupg_mpe};{$this->EOL} protocol=\"{$gnupg_tpp}\";{$this->EOL} boundary=\"{$gnupg_boundary}\"{$this->EOL}{$this->EOL}";

		// Instead of original Subject, use this to hide even the subject:
		// - generate a message hash ID to be used instead of orig subject;
		// - show the original Subject in the encrypted message body;
		$tmpstr = chunk_split(
			md5(
				$pgpmime_header
				.rand()
				.microtime()
			),
			8,
			'-'
		);
		$tmpstr = substr($tmpstr, 0, strlen($tmpstr)-1);
        	$replace_subject_with_msgID = '[PGP/MIME] ' . $tmpstr;
		//////////////////////////////////////////////////////////////////////////
		// NOTE: pgpmime subject; this must be returned as subject for the mail() function
        	$pgpmime_subject = $replace_subject_with_msgID;

	    	// gnupg pgp/mime note
	    	$gnupg = "{$gnupg_pmn}{$this->EOL}";

	    	// gnupg part 1 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpcv}{$this->EOL}Content-Description: {$gnupg_dpcv}{$this->EOL}{$this->EOL}{$gnupg_pmvi}{$this->EOL}{$this->EOL}";

	    	// gnupg part 2 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpce}{$this->EOL}Content-Description: {$gnupg_dpce}{$this->EOL}Content-Disposition: {$gnupg_dpci}{$this->EOL}{$this->EOL}";

	    	// gnupg encrypted/signed message body
	    	// original header to be added into encrypted part (use here the prepared/imploded orig_header from above)
		// NOTE: signer and signer passphrase are set ready in this class instantiation,
		// and used in the following function directly from there
	    	$gnupg .= $this->_encryptSignGnuPG($orig_header."{$this->EOL}{$this->EOL}".$original_mail_body, $mail_recipients);
	    	// gnupg end boundary
	    	$gnupg .= "{$this->EOL}--{$gnupg_boundary}--{$this->EOL}";

		//////////////////////////////////////////////////////////////////////////
		// NOTE: gnupg into mail body; this must be returned as encrypted body for the mail() function
	    	$pgpmime_encrypted_message_body = $gnupg;

		return array(
				0 => $pgpmime_header,
				1 => $pgpmime_subject,
				2 => $pgpmime_encrypted_message_body);
	}


	/////////////////////////////////////////////////////////////////
	/**
	* Prepare encryption of a mail using gnupg
	*
	* @param  array		$original_mail_headers / headers array
	* @param  string	$original_mail_body / The main body of the message after building
	* @param  string	$mail_build_params_head_charset
	* @param  array/string	$recipients (needs to be not-imploded string or array)
	* @access public
	* @return array
	*		0 => array $gnupg_header_array
	*		1 => string $pgpmime_encrypted_message_body
	*/
	function prepareEncryptWithSmtpSender($original_mail_headers,$original_mail_body,$mail_build_params_head_charset,$mail_recipients)
	{

	    	// Define gnupg/mime header variables; see constants above in this class
	    	$gnupg_mpe = OpenPGPLib::MULTIPART_PGP_ENCRYPTED;
	    	$gnupg_tpp = OpenPGPLib::TYPE_PGP_PROTOCOL;
	    	$gnupg_pmn = OpenPGPLib::PGP_MIME_NOTE;
	    	$gnupg_tpcv = OpenPGPLib::TYPE_PGP_CONTENT_VERSION;
	    	$gnupg_dpcv = OpenPGPLib::DESCRIPTION_PGP_CONTENT_VERSION;
	    	$gnupg_pmvi = OpenPGPLib::PGP_MIME_VERSION_IDENTIFICATION;
	    	$gnupg_tpce = OpenPGPLib::TYPE_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpce = OpenPGPLib::DESCRIPTION_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpci = OpenPGPLib::DISPOSITION_PGP_CONTENT_INLINE;

		// Define gnupg boundary
		$gnupg_boundary = '------gnupg-' . md5(rand().microtime());

		// Define a boundary for orig body, if needed
		$add_boundary_for_orig = '------orig--' . md5(rand().microtime());

		// set discard-flag originally false; see below foreach loop and later the origheader implode
		$discard_orig_header = true;

		// Get flat representation of original headers to be put with the encrypted body
		foreach ($original_mail_headers as $name => $value) {
			// first process Content-Type and discard original and set pgp/mime-required after loop
			if ($name == 'Content-Type') {
				$tmpvalue = $this->_encodeHeader($value, $mail_build_params_head_charset);
				// check if original message is just text/plain, so we can discard the entire
				// orig_header from being prepended into encrypted part
				$pos = strpos($tmpvalue, 'text/plain');
				if ($pos !== false) {
					// so text/plain ...set discard-flag...
					$discard_orig_header = true;
				} else {
					// reach here if not text/plain
					$orig_headers[] = $name . ': ' . $tmpvalue;
				}
			} else {
				// Get flat representation of original headers
				$orig_headers[] = $name . ': ' . $this->_encodeHeader($value, $mail_build_params_head_charset);
			}
		}

		// save original header to be added into encrypted part
		if ($discard_orig_header) {
			// original body is text/plain, so no headers there
			$orig_header = '';
		} else {
			$orig_header = implode(CRLF, $orig_headers);
		}

	    	// gnupg pgp/mime headers; original headers from call-parameters to be added into encrypted part (see above/below)
	    	$pgpmime_header = "Content-Type: {$gnupg_mpe};{$this->EOL} protocol=\"{$gnupg_tpp}\";{$this->EOL} boundary=\"{$gnupg_boundary}\"{$this->EOL}{$this->EOL}";

		//////////////////////////////////////////////////////////////////////////
		// Instead of original Subject, use this to hide even the subject:
		// - generate a message hash ID to be used instead of orig subject;
		// - show the original Subject in the encrypted message body;
		$tmpstr = chunk_split(
			md5(
				$pgpmime_header
				.rand()
				.microtime()
			),
			8,
			'-'
		);
		$tmpstr = substr($tmpstr, 0, strlen($tmpstr)-1);
        	$replace_subject_with_msgID = '[PGP/MIME] ' . $tmpstr;
		//////////////////////////////////////////////////////////////////////////
		// NOTE: pgpmime_header_array; this must be returned as header array (NOTE: array here) for the mail() function
		// prepend the opaque Subject before other PGP/MIME headers
        	$pgpmime_header_array[] = "Subject: " . $replace_subject_with_msgID;
		// instead of the original, set this pgp/mime-required header into $gnupg_header_array -return array
		$pgpmime_header_array[] = $pgpmime_header;

	    	// Create gnupg pgp/mime parts etc below and set/repalced to message body by caller
		// NOTE: here we do not need to create directly the $gpgpmime_header, because the $gnupg_header_array is returned back
		// to caller in array format (caller is htmlMimeMail)

	    	// gnupg pgp/mime note
	    	$gnupg = "{$gnupg_pmn}{$this->EOL}";

	    	// gnupg part 1 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpcv}{$this->EOL}Content-Description: {$gnupg_dpcv}{$this->EOL}{$this->EOL}{$gnupg_pmvi}{$this->EOL}{$this->EOL}";

	    	// gnupg part 2 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpce}{$this->EOL}Content-Description: {$gnupg_dpce}{$this->EOL}Content-Disposition: {$gnupg_dpci}{$this->EOL}{$this->EOL}";

	    	// gnupg encrypted/signed message body
	    	// original header to be added into encrypted part (use here the prepared/imploded orig_header from above)
		// NOTE: signer and signer passphrase are set ready in this class instantiation,
		// and used in the following function directly from there
	    	$gnupg .= $this->_encryptSignGnuPG($orig_header."{$this->EOL}{$this->EOL}".$original_mail_body, $mail_recipients);
	    	// gnupg end boundary
	    	$gnupg .= "{$this->EOL}--{$gnupg_boundary}--{$this->EOL}";

		//////////////////////////////////////////////////////////////////////////
		// NOTE: gnupg into mail body; this must be returned as encrypted body for the mail() function
	    	$pgpmime_encrypted_message_body = $gnupg;

		return array(
				0 => $pgpmime_header_array,
				1 => $pgpmime_encrypted_message_body);
	}

	//////////////////////////////////////////////////////////
	//
	// ZEND FRAMEWORK GNUPG-ENCRYPTED PGP/MIME MAIL FUNCTIONS
	//
	//////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////
	/**
	 * Prepend header name to header value; copied here from Zend/Mail/Transport/Abstract
	 * See usage in the array_walk in function below
	 * @param string $item
	 * @param string $key
	 * @param string $prefix
	 * @static
	 * @access protected
	 * @return void
	 */
	protected static function _formatHeader(&$item, $key, $prefix)
	{
        	$item = $prefix . ': ' . $item;
	}

	//////////////////////////////////////////////////////////
	/**
	 * Prepate encryption of a mail using gnupg
	 *
	 * @param  string $original_mail_headers
	 * @param  string $original_mail_body
	 * @param  array|string $recipients
	 * @access public
	 * @return array
	 *		0 => string $pgpmime_header
	 *		1 => string $pgpmime_encrypted_message_body
	 */
	function prepareEncryptWithZendMail($original_mail_header = '', $original_mail_body = NULL, $recipients)
	{

	    	// Define gnupg/mime header variables; see constants above in this class
	    	$gnupg_mpe = OpenPGPLib::MULTIPART_PGP_ENCRYPTED;
	    	$gnupg_tpp = OpenPGPLib::TYPE_PGP_PROTOCOL;
	    	$gnupg_pmn = OpenPGPLib::PGP_MIME_NOTE;
	    	$gnupg_tpcv = OpenPGPLib::TYPE_PGP_CONTENT_VERSION;
	    	$gnupg_dpcv = OpenPGPLib::DESCRIPTION_PGP_CONTENT_VERSION;
	    	$gnupg_pmvi = OpenPGPLib::PGP_MIME_VERSION_IDENTIFICATION;
	    	$gnupg_tpce = OpenPGPLib::TYPE_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpce = OpenPGPLib::DESCRIPTION_PGP_CONTENT_ENCRYPTED;
	    	$gnupg_dpci = OpenPGPLib::DISPOSITION_PGP_CONTENT_INLINE;

	    	// Define gnupg boundary
	    	$gnupg_boundary = '------gnupg-' . md5(rand().microtime());

	    	//*********************
	    	// NOTE: in smtp, $mail_recipients includes the Cc and Bcc recipients.
	    	// so the message gets properly encrypted for them also;
	    	// the problem is still with the Sendmail for such cases / see above...?
	    	// What is the case in Zend Abstract, Sendmail and Smtp...?
	    	//*********************

	    	// Create gnupg pgp/mime parts etc below and set to this->output

	    	// gnupg pgp/mime headers; original headers from call-parameters to be added into encrypted part (see below)
	    	$pgpmime_header = "Content-Type: {$gnupg_mpe};{$this->EOL} protocol=\"{$gnupg_tpp}\";{$this->EOL} boundary=\"{$gnupg_boundary}\"{$this->EOL}{$this->EOL}";

		// Instead of original Subject, use this to hide even the subject:
		// - generate a message hash ID to be used instead of orig subject;
		// - show the original Subject in the encrypted message body;
		$tmpstr = chunk_split(
			md5(
				$pgpmime_header
				.rand()
     	  		.microtime()
			),
			8,
			'-'
		);
		$tmpstr = substr($tmpstr, 0, strlen($tmpstr)-1);
        	$replace_subject_with_msgID = '[PGP/MIME] ' . $tmpstr;
		// prepend the opaque Subject before other PGP/MIME headers
        	$pgpmime_header = "Subject: " . $replace_subject_with_msgID . $this->EOL . $pgpmime_header;

	    	// gnupg pgp/mime note
	    	$gnupg = "{$gnupg_pmn}{$this->EOL}";

	    	// gnupg part 1 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpcv}{$this->EOL}Content-Description: {$gnupg_dpcv}{$this->EOL}{$this->EOL}{$gnupg_pmvi}{$this->EOL}{$this->EOL}";

	    	// gnupg part 2 header
	    	$gnupg .= "--{$gnupg_boundary}{$this->EOL}Content-Type: {$gnupg_tpce}{$this->EOL}Content-Description: {$gnupg_dpce}{$this->EOL}Content-Disposition: {$gnupg_dpci}{$this->EOL}{$this->EOL}";

	    	// gnupg encrypted/signed message body
	    	// original header to be added into encrypted part
		// NOTE: signer and signer passphrase are set ready in this class instantiation,
		// and used in the following function directly from there
	    	$gnupg .= $this->_encryptSignGnuPG($original_mail_header."{$this->EOL}{$this->EOL}".$original_mail_body, $recipients);
	    	// gnupg end boundary
	    	$gnupg .= "{$this->EOL}--{$gnupg_boundary}--{$this->EOL}";

	    	// gnupg into mail body
	    	$pgpmime_encrypted_message_body = $gnupg;

		return array(
				0 => $pgpmime_header,
				1 => $pgpmime_encrypted_message_body);
	}

	//////////////////////////////////////////////////////////
	/**
	 * Encrypt and sign a mail using gnupg
	 *
	 * @param  string $unencrypted_message
	 * @param  array/string $recipients (needs to be not-imploded string or array)
	 * @access protected
	 * @return string ($encrypted_message)
	 */
	protected function _encryptSignGnuPG($unencrypted_message, $recipients)
	{

    		$encrypted_message = '';

    		// encrypt $message to recipients
    		// sign wth signer
		$gpg = $this->gpg_encrypt("${unencrypted_message}", $recipients);

    		// $gpg is an array containing
    		// $gpg[0] encrypted output (STDOUT)
		// $gpg[1] warnings and notices (STDERR)
    		// $gpg[2] exit status from gpg

    		// test gpg's exit status
    		if ("$gpg[2]" == '0') {
    			// if the gpg command returned zero
    			$encrypted_message = $gpg[0];
    		} else {
		    	// if the gpg command returned non-zero
			$error_msg = 'OpenPGPLib: _encryptSignGnuPG() returned error code: '.$gpg[2];
			trigger_error($error_msg, E_USER_ERROR);
			// if an error message directs you to the line above please
			// double check that your gnupg-configuration, process-call commandline input, and other parameters are correct
    		}

    		return $encrypted_message;

	}

}
$openpgplib = new OpenPGPLib;
