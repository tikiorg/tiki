<?php

//this script may only be included - so its better to die if called directly.
/*Commented taht out so actually I call this from console.. hehe
  if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}
*/

/*************************************************************************/
 #  IMAP class for reading IMAP mail onto TikiWiki, class structure taken #
 #  from:								  #
 #  Mailbox 0.9.2a   by Sivaprasad R.L (http://netlogger.net)             #
 #  eMailBox 0.9.3   by Don Grabowski  (http://ecomjunk.com)              #
/*************************************************************************/

class IMAP{
        var $hostname;
        var $user;
        var $password;
        var $port=143;
	var $folder="INBOX";
        var $DEBUG=0;
        var $exit = true;
        var $has_error = false;

        /* Private variables - DO NOT ACCESS */

        var $connection=0;
        var $greeting = "";
        var $state="DISCONNECTED";
        var $must_update=0;
        var $dummy= "";

        function IMAP($hostname,$user,$password,$folder) {
                $this->hostname = $hostname;
                $this->user = $user;
                $this->password = $password;
		if($folder) 
			$this->folder = $folder;
        }

         function AddError($error) {
		var_dump(imap_errors());
                 $this->has_error = true;
               echo "<center>\n";
                 echo "<b>Error:</b> $error\n";
               echo "</center>\n";
                 $this->CloseConnection();
                 if ($this->exit) exit;

        }
	
         function OpenConnection() {
                 if ($this->DEBUG) {echo "<b>Openning Connection to: </b>".$this->hostname."<br>";flush();}
                 if($this->hostname=="")
                           $this->AddError("You must specified a valid hostname");
		echo "..";
		$this->connection = imap_open("\{$this->hostname:$this->port/novalidate-cert}INBOX", "$this->user", "$this->password");
		echo "Connected";
                if (!($this->connection)) 
                      $this->AddError("Invalid Mail Server Name or Server Connection Error");
                return true;
         }

         function CloseConnection() {
                  if($this->connection!=0) :
			imap_close($this->connection);
                        $this->connection=0;
                endif;
         }

         function Open() {
                  if($this->state!="DISCONNECTED")
                           $this->AddError("1 a connection is already opened");
                  $this->OpenConnection();
                  $this->must_update=0;
                  $this->state="TRANSACTION";
                  return true;
         }

 /* Close method - this method must be called at least if there are any
     messages to be deleted */

         function Close() {
                  if($this->state=="DISCONNECTED")
                           $this->AddError("no connection was opened");
                  if($this->must_update)
			;
                  $this->CloseConnection();
                  $this->state="DISCONNECTED";
                  return true;
         }


        /* Statistics method - pass references to variables to hold the number of
     messages in the mail box and the size that they take in bytes.  */

        function Stats($msg=""){
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
	          if (!isset($result)) $result='';
                  if ($msg == "") {
			$result = imap_mailboxmsginfo($this->connection);
			$stats["mailbox"] = $result->Mailbox;
			$stats["message"] = $result->Nmsgs; 
			$stats["size"] = $result->Size;
		} else {
			$result = imap_fetchstructure($this->connection, $msg);
			$stats["message"] = $msg;
			$stats["size"] = $result->bytes;
                }
                  return $stats;
         }

        function GetHeaders($message=1) {
		$dummy = imap_headerinfo($this->connection, $message);

		$headers["reply-to"] = $headers["from"] = $headers["cc"] = $headers["to"] = "";
		$headers["from"] = $dummy->fromaddress;
		$headers["reply-to"] = $dummy->reply_toaddress;
		$headers["cc"] = $dummy->ccaddress;
		$headers["to"] = $dummy->toaddress;

                return $headers;
        }

        function GetMessageID($message="") {
                if ($message) :
			$result = imap_uid($this->connection, $message);
                        return ereg_replace("[<>]","",$result);
                else :
			$this->AddError("GetMessageID... no message given");
                endif;

        }

        function GetMessage($msg=1) {
                $i = 0;
                $messagebody='';
		$message["body"] = imap_body($this->connection, $msg);
		$message["full"] = imap_fetchheader($this->connection, $msg)."\n".$message["body"];
                return $message;
        }

        function ListMessage($msg) {
                $date=0;
                $list = array();
                $list["has_attachment"] = false;
                $list["size"] = '';
		$header = imap_fetchheader($this->connection, $msg);

		$line = strtok($header, "\n");
                for ($m="";;) {
                        $list["size"] += strlen($line);
                        if (trim($line) == ".") {
                                  break;
                        }
                        if (eregi("^Subject: (.*)", $line, $reg)) {
                                $list["subject"] = $reg[1];
                        	// if subject is empty, set a default subject
                        	if (trim($reg[1]) == "" OR is_null($reg[1]))
																$list["subject"] = "<NO SUBJECT>";
                        }
                        if (eregi("^Date: (.*)", $line, $reg))
                                $date = $reg[1];
                        if (eregi("^From: (.*)", $line, $reg))
                                $from = $reg[1];
                        if (eregi("^Content-Disposition: attachment", $line) OR eregi("^Content-Disposition: inline", $line))
                                $list["has_attachment"] = true;;
			$line = strtok("\n");
			if(!$line)
				break;
                }
                eregi("(.+) (.+) (.+) ([0-9]{1,2})([0-9]{1,2}) (.+):(.+):(.+) .+", $date, $dreg);
                $list["date"] = $dreg[1]." ".$dreg[2]." ".$dreg[3];
                $from = eregi_replace("<|>|\[|\]|\(|\)|\"|\'|(mailto:)", "", $from);
                 if (eregi("(.*)? (.+@.+\\..+)", $from)) :
                           eregi("(.*)? (.+@.+\\..+)", $from, $reg);
                           $list["sender"]["name"] = $reg[1];
                           $list["sender"]["email"] = $reg[2];
                 else :
                         eregi("(.+@.+\\..+)", $from, $reg);
                         $list["sender"]["name"] = $reg[1];
                         $list["sender"]["email"] = $reg[1];
                endif;
                return $list;
        }

         function DeleteMessage($message) {
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
        
		imap_delete($this->connection, $message);
                  $this->must_update=1;
                  return true;
         }

         function ResetDeletedMessages() {
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
		imap_undelete($this->connection, $message);
                  $this->must_update=0;
                  return("");
         }

	function ExpugneMessages() {
		imap_expugne($this->connection);
	}

	function GetMessageFlags($msg) {
		// Actually writting that one
		var_dump(imap_headerinfo($this->connection, $msg));
	}
};



$imap = new IMAP("localhost", "user", "password", "INBOX");
$imap->Open();
echo "Opened imap";
$stats = $imap->Stats();
var_dump($stats);
$headers = $imap->GetHeaders();
var_dump($headers);
$msgid = $imap->GetMessageID("1");
var_dump($msgid);
$message = $imap->GetMessage("1");
var_dump($message);
$list = $imap->ListMessage("1");
var_dump($list);
$imap->GetMessageFlags("1");
var_dump(imap_errors);	
?>
