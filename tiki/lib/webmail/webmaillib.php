<?php
 
class WebMailLib extends TikiLib {
  
  function WebMailLib($db) 
  {
    parent::TikiLib($db);
  }
   
   // Contacts
  function list_contacts($user,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where user='$user' and (nickname like '%".$find."%' or firstName like '%".$find."%' or lastName like'%".$find."%' or email like '%".$find."%')";  
    } else {
      $mid=" where user='$user' "; 
    }
    $query = "select * from tiki_webmail_contacts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_webmail_contacts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function are_contacts($contacts,$user)
  {
   $ret=Array();
   foreach($contacts as $con) {
     $con=trim($con);
     $cant = $this->getOne("select count(*) from tiki_webmail_contacts where email='$con'");
     if(!$cant) $ret[]=$con;
   }
   return $ret;
  }
  
  function list_contacts_by_letter($user,$offset,$maxRecords,$sort_mode,$letter)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    $mid=" where user='$user' and (email like '".$letter."%')";  
    $query = "select * from tiki_webmail_contacts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_webmail_contacts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function parse_nicknames($dirs)
  {
    for($i=0;$i<count($dirs);$i++) {
      if(!strstr($dirs[$i],'@')&&!empty($dirs[$i])) {
        print($dirs[$i]);
        $query = "select email from tiki_webmail_contacts where nickname='".$dirs[$i]."'";  
        $result = $this->query($query);
        if($result->numRows()) {
          $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
          $dirs[$i]=$res["email"];      
        }
      } 
    }
    return $dirs;
  }
  
  function replace_contact($contactId, $firstName, $lastName, $email, $nickname,$user)
  {
    $firstName=addslashes(trim($firstName));
    $lastName=addslashes(trim($lastName));
    $email=addslashes(trim($email));
    $nickname=addslashes(trim($nickname));
    
    // Check the name
        
    if($contactId) {
      $query = "update tiki_webmail_contacts set firstName='$firstName', lastName='$lastName', email='$email', nickname='$nickname' where contactId='$contactId' and user='$user'";
      $result = $this->query($query);
    } else {
      $query = "replace into tiki_webmail_contacts(firstName,lastName,email,nickname,user)
                values('$firstName','$lastName','$email','$nickname','$user')";
      
      $result = $this->query($query);
    }
    
    return true;
  }
  
  function remove_contact($contactId,$user) 
  {
    $query = "delete from tiki_webmail_contacts where contactId=$contactId and user='$user'";
    $result = $this->query($query);
    return true;
  }
  
  function get_contact($contactId,$user)
  {
    $query = "select * from tiki_webmail_contacts where contactId=$contactId and user='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_webmail_message($current,$user,$msgid) {
    $query = "delete from tiki_webmail_messages where accountId=$current and mailId=$msgid and user='$user'";
    $result = $this->query($query);
  }
  
  function replace_webmail_message($current,$user,$msgid)   {
    $query = "select count(*) from tiki_webmail_messages where accountId=$current and mailId=$msgid and user='$user'";
    
    if($this->getOne($query)==0) {
      $query = "insert into tiki_webmail_messages(accountId,mailId,user,isRead,isFlagged,isReplied)
                values($current,$msgid,'$user','n','n','n')";
      $result = $this->query($query);
    }
  }
  
  function set_mail_flag($current,$user,$msgid,$flag,$value)
  {
    $query = "update tiki_webmail_messages set $flag='$value' where accountId=$current and mailId=$msgid and user='$user'";
    $result = $this->query($query);
    return true;
    
  }
  
  function get_mail_flags($current,$user,$msgid) 
  {
    $query = "select isRead,isFlagged,isReplied from tiki_webmail_messages where accountId=$current and mailId=$msgid and user='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) {
      return Array('n','n','n');	
    }
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return Array($res["isRead"],$res["isFlagged"],$res["isReplied"]);
  }
  
  function current_webmail_account($user,$accountId)
  {
    $query = "update tiki_user_mail_accounts set current='n' where user='$user'";
    $result = $this->query($query);
    $query = "update tiki_user_mail_accounts set current='y' where user='$user' and accountId=$accountId";
    $result = $this->query($query);
  }
  
  function list_webmail_accounts($user,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" where user='$user' and (account like '%".$find."%')";  
    } else {
      $mid=" where user='$user'"; 
    }
    $query = "select * from tiki_user_mail_accounts $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_user_mail_accounts $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function replace_webmail_account($accountId, $user, $account, $pop, $port, $username, $pass, $msgs,$smtp,$useAuth,$smtpPort)
  {
    $account=addslashes($account);
    $username=addslashes($username);
    $pass=addslashes($pass);
    // Check the name
 
    if($accountId) {
      $query = "update tiki_user_mail_accounts set user='$user', account='$account', pop='$pop', port=$port,smtpPort=$smtpPort,username='$username', pass='$pass',smtp='$smtp',useAuth='$useAuth',msgs=$msgs where accountId=$accountId and user='$user'";
      $result = $this->query($query);
    } else {
      $query = "replace into tiki_user_mail_accounts(user,account,pop,port,username,pass,msgs,smtp,useAuth,smtpPort)
                values('$user','$account','$pop',$port,'$username','$pass',$msgs,'$smtp','$useAuth',$smtpPort)";
                $result = $this->query($query);
    }
  
    return true;
  }
  
  function get_current_webmail_account($user)
  {
    $query = "select * from tiki_user_mail_accounts where current='y' and user='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
  function remove_webmail_account($user,$accountId) 
  {
    $query = "delete from tiki_user_mail_accounts where accountId=$accountId and user='$user'";
    $result = $this->query($query);
    return true;
  }
  
  function get_webmail_account($user,$accountId)
  {
    $query = "select * from tiki_user_mail_accounts where accountId=$accountId and user='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
 
} # class WebMailLib

$webmaillib= new WebMailLib($dbTiki);
?>