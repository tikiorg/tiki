<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

/*************************************************************************/
 #  Mailbox 0.9.2a   by Sivaprasad R.L (http://netlogger.net)             #
 #  eMailBox 0.9.3   by Don Grabowski  (http://ecomjunk.com)              #
 #          --  A pop3 client addon for phpnuked websites --              #
 #                                                                        #
 # This program is distributed in the hope that it will be useful,        #
 # but WITHOUT ANY WARRANTY; without even the implied warranty of         #
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
 # GNU General Public License for more details.                           #
 #                                                                        #
 # You should have received a copy of the GNU General Public License      #
 # along with this program; if not, write to the Free Software            #
 # Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.              #
 #                                                                        #
 #             Copyright (C) by Sivaprasad R.L                            #
 #            Script completed by Ecomjunk.com 2001                       #
/*************************************************************************/

class POP3{
        var $hostname;
        var $user;
        var $password;
        var $apop="";
        var $port=110;
        var $DEBUG=0;
        var $exit = true;
        var $has_error = false;

        /* Private variables - DO NOT ACCESS */

        var $connection=0;
        var $greeting = "";
        var $state="DISCONNECTED";
        var $must_update=0;
        var $dummy= "";

        function POP3($hostname,$user,$password,$apop="") {
                $this->hostname = $hostname;
                $this->user = $user;
                $this->password = $password;
                $this->apop = $apop;
        }

         function AddError($error) {
                 
                 $this->has_error = true;
//               echo "<center>\n";
//        	 echo "<b>Error:</b> $error\n";
//               echo "</center>\n";
                 $this->CloseConnection();
                 //if ($this->exit) exit;
                 
        }
	
        function POP3Command($command, &$result) {
                if ($this->DEBUG) {echo "<b>Sending Command: </b>".$command."<br>";flush();}
                @fputs($this->connection, "$command\r\n");
                $result = @fgets($this->connection, 256);

                if (eregi("^(\+OK)", $result)) :
                        if ($this->DEBUG) {echo "<b>Result OK: </b><br>";flush();}
                        return true;
                else :
                        $this->AddError($result);
                endif;
        }
         function OpenConnection() {
                 if ($this->DEBUG) {echo "<b>Openning Connection to: </b>".$this->hostname."<br>";flush();}
                 if($this->hostname=="")
                           $this->AddError("You must specified a valid hostname");
                   $this->connection = fsockopen($this->hostname,$this->port, $errno, $errstr);
                if ($this->DEBUG) {echo "<b>Connection opened </b><br>";flush();}
                   if (!($this->connection)) :
                           if ($errno == 0)
                                    $this->AddError("Invalid Mail Server Name or Server Connection Error");
                           $this->AddError($errno." ".$errstr);
                   endif;
                return true;
         }

         function CloseConnection() {
                  if($this->connection!=0) :
                           fclose($this->connection);
                           $this->connection=0;
                endif;
         }

         function Open() {
                  if($this->state!="DISCONNECTED")
                           $this->AddError("1 a connection is already opened");
                  $this->OpenConnection();
                        $this->greeting = @fgets($this->connection, 100);
                          if(GetType($this->greeting)!="string" AND strtok($this->greeting," ")!="+OK") :
                                   $this->CloseConnection();
                                   $this->AddError("2 POP3 server greeting was not found");
                          endif;
                  $this->greeting=strtok("\r\n");
                  $this->must_update=0;
                  $this->state="AUTHORIZATION";
                  $this->Login();
                  return true;
         }

 /* Close method - this method must be called at least if there are any
     messages to be deleted */

         function Close() {
                  if($this->state=="DISCONNECTED")
                           $this->AddError("no connection was opened");
                  if($this->must_update)
                           $this->POP3Command("QUIT",$this->dummy);
                  $this->CloseConnection();
                  $this->state="DISCONNECTED";
                  return true;
         }

 /* Login method - pass the user name and password of POP account.  Set
     $apop to 1 or 0 wether you want to login using APOP method or not.  */

        function Login() {
                  if($this->state!="AUTHORIZATION")
                           $this->AddError("connection is not in AUTHORIZATION state");
                  if($this->apop) :
                           $this->POP3Command("APOP $this->user ".md5($this->greeting.$this->password),$this->dummy);
                  else :
                          $this->POP3Command("USER $this->user",$this->dummy);
                          $this->POP3Command("PASS $this->password",$this->dummy);
                  endif;
                  $this->state="TRANSACTION";
         }

        /* Statistics method - pass references to variables to hold the number of
     messages in the mail box and the size that they take in bytes.  */

        function Stats($msg=""){
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
	          if (!isset($result)) $result='';
                  if ($msg == "") :
                          $this->POP3Command("STAT", $result);
                  else :
                          $this->POP3Command("LIST $msg", $result);
                  endif;
                  $p = explode(" ", $result);
                  $stat["message"] = $p[1];
                  $stat["size"] = $p[2];
                  return $stat;
         }

        function GetHeaders($message=1) {
                $this->POP3Command("TOP $message 0",$this->dummy);
                for ($headers="";;) {
                        $line = fgets($this->connection, 100);
                          if (trim($line) == "." OR feof($this->connection)) {
                                  break;
                        }
                          $headers .= $line;
                  }
                return $headers;
        }

        function GetMessageID($message="") {
                if ($message) :
                        $this->POP3Command("UIDL $message", $result);
                        $id = explode (" ", $result);
                        return ereg_replace("[<>]","",$id[2]);
                else :
                        $this->POP3Command("UIDL",$this->dummy) ;
                        while (!feof($this->connection)) :
                                $line = fgets($this->connection, 100);
                                if (trim($line) == ".") {
                                          break;
                                }
                                $part = explode (" ", $line);
                                $part[1] = ereg_replace("[<>]","",$part[1]);
                                $id[$part[0]] = $part[1];
                        endwhile;
                        return $id;
                endif;

        }

        function GetMessage($msg=1) {
                $i = 0;
                $messagebody='';
                $this->POP3Command("RETR $msg",$this->dummy);
                for ($m="";;) {
                        $line = fgets($this->connection, 100);
                        if (trim($line) == "." OR feof($this->connection)) {
                                  break;
                        }
                        if (chop($line) == ""  AND $i < 1) :
                                $message["header"] = $m;
                                $i++;
                        endif;
                        if ($i > 0)
                                $messagebody .= $line;
                        $m .= $line;
                }
                $message["body"] = $messagebody;
                $message["full"] = $m;
                return $message;
        }

        function ListMessage($msg) {
                $date=0;
                $list = array();
                $list["has_attachment"] = false;
                $list["size"] = '';
                //$this->POP3Command("RETR $msg",$this->dummy);
                $this->POP3Command("TOP $msg 0",$this->dummy);
                for ($m="";;) {
			// Can't get it to work without the second
			// parameter.  Shouldn't be a problem, though:
			// that's 10 MB.
                        $line = fgets($this->connection, 10485760);
                        $list["size"] += strlen($line);
                        if (trim($line) == "." OR feof($this->connection)) {
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
                  $aux='DELE '.$message;
                  $this->POP3Command($aux,$this->dummy);
                  $this->must_update=1;
                  return true;
         }

         function ResetDeletedMessages() {
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
                  $this->POP3Command("RSET",$this->dummy);
                  $this->must_update=0;
                  return("");
         }

         function NOOP() {
                  if($this->state!="TRANSACTION")
                           $this->AddError("connection is not in TRANSACTION state");
                  $this->POP3Command("NOOP",$this->dummy);
                  return("");
          }
};

?>
