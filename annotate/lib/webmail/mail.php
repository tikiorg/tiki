<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* TikiMail class
 *
 *  This is the core class for Tikiwiki Mail handling. It takes care of all the
 *  connection faced stuff.
 *
 */
 
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class TikiMail
{
	var $debug = true;	// Defines if we are on a debuggin state or not.
	
	var $connection;  	// the mail stream.
	var $type;        	// Type of mail server (pop3, imap, nntp, ...)
	var $options;     	// Options given (no-tls, no-validate-cert, ..)
	var $state;	      	// How do we feel.
	
	var $server;      	// Server we should connect to.
	var $port;	      	// Uhmm.. TCP seems to require a port to knock at.
	var $user;	      	// Username to tell when somebody opens the door.
	var $pass;	      	// That somebody may ask for a password..
	var $mailbox;    	// Some somebody's accept different mailboxes/folders.
	
	/*
	* Our constructor class. Takes as params:
	* $type - mandatory, type of server by now imap or imaps.
	* $server, $user, $pass - mandatory and self explanatory.
	* $mailbox - optional. Defaults to INBOX.
	* $options - optional. Options to give to imap_open()
	*/
	function TikiMail($type, $server, $port, $user, $pass, $mailbox = "INBOX", $options = '') {
		$this->server = $server;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		$this->mailbox = $mailbox;
		$this->options = $options;
		
		$this->type = $type;
		$this->connection = 0;
		$this->state = 'new';
		
		if($this->debug) {
			echo "Created new TikiMail object in which:<br>";
			echo " Type = $type<br> User = $user<br> Mailbox = $mailbox<br> Options are: $options<br>";
		}
	}
	
	/*
	* connect() knock at servers door and present ourselves.
	*/
	function connect() {
		$this->connection = imap_open("\{$this->server:$this->port/$this->type/$this->options}$this->mailbox", "$this->user", "$this->pass");
                if (!($this->connection)) {
			if($this->debug) {
				echo "Something bad happened while connecting!<br>";
				echo implode("<br />\n", imap_errors());
			}
			return false;
		}
		$this->state = 'online';
                return true;
	}
	
	/*
	* disconnect() 
	* We took coofe with the neighboor and talked about things, now it's time to leave
	*/
	function disconnect() {
		if(($this->connection == 0) || $this->state = 'new') {
			if($this->debug) {
				echo "We should first connect to server before disconnecting I think...<br>";
			}
			return false;
		}
		imap_close($this->connection);
		// TODO: Error checking.
		$this->connection=0;
		return true;
	}
	
	/* 
	* mailbox_create()
	*  On servers supporting that create a new mailbox/folder.
	*  IMAP is an example of that.
	*/
	function mailbox_create($mailbox) {
		if($this->connection == 0) {
			return false;
		}
		if(!imap_createmailbox($this->connection, "\{$this->server:$this->port}$mailbox")) {
			if($this->debug) {
				echo "We had problems creating the mailbox: $ourbox$mailbox<br>";
				echo implode("<br />\n", imap_errors());
			}
			return false;
		}
		return true;			
	}
	
	/*
	* mailbox_check()
	*  Check a mailbox for new messages
	*/
	function mailbox_check($mailbox = '') {
		if($this->connection == 0) {
			return false;
		}
		if($mailbox == '')
			$mailbox = $this->mailbox;
			
		$status = imap_status($this->connection, "\{$this->server:$this->port}$mailbox", SA_UNSEEN);
		if(!$status) {
			echo "I smell problems when looking for unseen messages<br>";
			echo implode("<br />\n", imap_errors());
			return -1;
		}
		return $status->unseen;
	}
	
	/* 
	* mailbox_delete()
	*  Delete a mailbox from the server.
	*/
	function mailbox_delete($mailbox) {
		if($this->connection == 0) {
			return false;
		}
		if(!imap_deletemailbox($this->connection, "\{$this->server:$this->port}$mailbox")) {
			if($this->debug) {
				echo "We had problems deleting the mailbox: $mailbox<br>";
				echo implode("<br />\n", imap_errors());
			}
			return false;
		}
		return true;					
	}
	
	/* 
	* mailbox_expunge()
	*   expunge deleted messages from mailbox.
	*/
	function mailbox_expunge() {
		if($this->state == 'new' || $this->connection == 0) {
			return false;
		}
		imap_expunge($this->connection);
		return true;
	}
	
	/*
	* mailbox_get_info()
	*  Mainly it retrieves headers of mailbox messages... ALL
	*  It returns al 'objects' collected in an array, the object spec can be
	*  seen at: http://es2.php.net/manual/en/function.imap-headerinfo.php
	*/
	function mailbox_get_info() {
		if($this->connection == 0) {
			return false;
		}
		$total = $this->mailbox_get_total();

		$headers = array();
		$count=0;	//my array domination is clear here... again

		for($i = 0; $i <= $total; $i++) {
			$thismsg = imap_headerinfo($this->connection, $i);
			// TODO: Error checking.
			$headers[$i] = $thismsg;
		}
		return $headers;
	}
	
	/*
	* mailbox_get_total()
	*  A shortcut to get the total messages inside a mailbox.
	*/
	
	function mailbox_get_total() {
		if($this->connection == 0) {
			return false;
		}
		$count = imap_num_msg($this->connection);
		// TODO: Error checking...
		
		return $count;
	}
	
	/*
	* mailbox_rename()
	*  Renames a mailbox
	*/
	function mailbox_rename($oldname, $newname) {
		if($this->connection == 0) {
			return false;
		}
		if(!imap_renamemailbox($this->connection, "\{$this->server:$this->port}$oldname", "\{$this->server:$this->port}$newname")) {
			if($this->debug) {
				echo "We had problems renaming the mailbox: $oldname to $newname<br>";
				echo implode("<br />\n", imap_errors());
			}
			return false;
		}
		return true;				
	}
	
	/*
	* mailboxes_check()
	*  Check mailboxes for new messages
	*/
	function mailboxes_check() {
		if(!$this->mailboxes_list()) {
			return false;
		}
		$boxes = $this->mailboxes_list();
		$boxes_c = array();
		$msg_c = array();
		$count=0;	//my array domination is clear here...
		reset($boxes);
		foreach($boxes as $box) {
			$boxes_c[$count]['mailbox'] = $box;
			$boxes_c[$count]['unseen'] = $this->mailbox_check($box);
			$count += 1;
		}
		
		return $boxes_c;
	}
	
	/*
	* mailboxes_list()
	*   get a list of the mailboxes avaible on the server
	*/
	function mailboxes_list() {
		if($this->connection == 0) {
			return false;
		}
		$boxes = array();
		$list = imap_list($this->connection, "\{$this->server:$this->port}", "*");
		if (is_array($list)) {
			reset($list);
			while (list($key, $val) = each($list)) {
				$boxes[] = substr(strstr(imap_utf7_decode($val), '}'), 1);
			}
		} else {
			if($this->debug) {
				echo "Had problems collecting mailboxes list..";
				echo implode("<br />\n", imap_errors());
			}
			return false;
		}
		return $boxes;
	}
	
	/* 
	* message_get_headers()
	*  Get the headers of a specifyc message
	*  Look at mailbox_get_info() about data returned.
	*/
	function message_get_headers($msgno) {
		if($this->connection) {
			return false;
		}
		$headers = imap_headerinfo($this->connection, $msgno);
		// TODO: Error checking.
		
		return $headers;
	}
	
	/*
	* message_get_body()
	*  fetch the contents of a message, say: the body
	*/
	function message_get_body() {
		//this is hard... what to do with attachments? also what to do when plain/text body and html body?	
	}
	
	/*
	* message_delete()
	*  Delete a message
	*/
	function message_delete() {
		if($this->connection == 0) {
			return false;
		}
		imap_delete($this->connection, $msgno);
		// TODO: Error checking.
		return true;		
	}

	/*
	* message_undelete()
	*  UnDelete a message
	*/
	function message_undelete($msgno) {
		if($this->connection == 0) {
			return false;
		}
		imap_undelete($this->connection, $msgno);
		// TODO: Error checking.
		return true;
	}
}
