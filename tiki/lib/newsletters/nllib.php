<?php
 
class NlLib {
  var $db;  // The PEAR db object used to access the database
  var $tikilib; // The tikilib object
  
  function NlLib($db,$tikilib) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
    $this->tikilib = $tikilib;
  }
  
  function sql_error($query, $result) 
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }
  
  
   
  function replace_newsletter($nlId,$name,$description,$allowAnySub,$frequency)
  {
    $name = addslashes($name);
    $description = addslashes($description);
    if($nlId) {
      // update an existing quiz
      $query = "update tiki_newsletters set 
      name = '$name',
      description = '$description',
      allowAnySub = '$allowAnySub',
      frequency = $frequency
      where nlId = $nlId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
    } else {
      // insert a new quiz
      $now = date("U");
      $query = "insert into tiki_newsletters(name,description,allowAnySub,frequency,lastSent,editions,users,created)
      values('$name','$description','$allowAnySub',$frequency,$now,0,0,$now)";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);
      $queryid = "select max(nlId) from tiki_newsletters where created=$now";
      $nlId = $this->db->getOne($queryid);  
    }
    return $nlId;
  }

  function remove_newsletter_subscription($nlId,$email)
  {
    $valid = $this->db->getOne("select valid from tiki_newsletter_subscriptions where nlId=$nlId and email='$email'");	
    if($valid) {
      $query = "update tiki_newsletters set users=users-1 where nlId=$nlId";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query, $result);	
    }
    $query = "delete from tiki_newsletter_subscriptions where nlId=$nlId and email='$email'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);	
  }

  function newsletter_subscribe($nlId,$email) 
  {
    $email=addslashes($email);  	 
    // Generate a code and store it and send an email with the
    // URL to confirm the subscription put valid as 'n'
    $foo = parse_url($_SERVER["REQUEST_URI"]);
    $url_subscribe = httpPrefix().$foo["path"];
    $code = md5($this->tikilib->genPass());
    $now = date("U");
    $query = "replace into tiki_newsletter_subscriptions(nlId,email,code,valid,subscribed)
    values($nlId,'$email','$code','n',$now)";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $info = $this->get_newsletter($nlId);
    $smarty->assign('info',$info);
    // Now send an email to the address with the confirmation instructions
    $smarty->assign('mail_date',date("U"));
    $smarty->assign('mail_user',$user);
    $smarty->assign('code',$code);
    $smarty->assign('url_subscribe',$url_subscribe);
    $smarty->assign('server_name',$_SERVER["SERVER_NAME"]);
    $mail_data=$smarty->fetch('mail/confirm_newsletter_subscription.tpl');
    @mail($email, tra('Newsletter subscription information at ').$_SERVER["SERVER_NAME"],$mail_data);
    
  }
  
  function confirm_subscription($code)
  {
    $foo = parse_url($_SERVER["REQUEST_URI"]);
    $url_subscribe = httpPrefix().$foo["path"];       	 
    $cant = $this->db->getOne("select * from tiki_newsletter_subscriptions where code='$code'");
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $info = $this->get_newsletter($res["nlId"]);
    $smarty->assign('info',$info);
    $query = "update tiki_newsletters set users=users+1 where nlId=".$res["nlId"];
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Now send a welcome email
    $smarty->assign('mail_date',date("U"));
    $smarty->assign('mail_user',$user);
    $smarty->assign('code',$res["code"]);
    $smarty->assign('url_subscribe',$url_subscribe);
    $mail_data=$smarty->fetch('mail/newsletter_welcome.tpl');
    @mail($email, tra('Welcome to ').$info["name"].tra(' at ').$_SERVER["SERVER_NAME"],$mail_data);
    return $this->get_newsletter($res["nlId"]);
  }
  
  function unsubscribe($code)
  {
    $foo = parse_url($_SERVER["REQUEST_URI"]);
    $url_subscribe = httpPrefix().$foo["path"];
    $cant = $this->db->getOne("select * from tiki_newsletter_subscriptions where code='$code'");
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    if(!$result->numRows()) return false;
    $info = $this->get_newsletter($res["nlId"]);
    $smarty->assign('info',$info);
    $smarty->assign('code',$res["code"]);
    $query = "delete from tiki_newsletter_subscription where code='$code'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $query = "update tiki_newsletters set users=users-1 where nlId=".$res["nlId"];
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    // Now send a bye bye email
    $smarty->assign('mail_date',date("U"));
    $smarty->assign('mail_user',$user);
    $smarty->assign('url_subscribe',$url_subscribe);
    $mail_data=$smarty->fetch('mail/newsletter_byebye.tpl');
    @mail($email, tra('Bye bye from ').$info["name"].tra(' at ').$_SERVER["SERVER_NAME"],$mail_data);
    return $this->get_newsletter($res["nlId"]);
  }
  
  function get_newsletter($nlId) 
  {
    $query = "select * from tiki_newsletters where nlId=$nlId";
    $result = $this->db->query($query);
    if(!$result->numRows()) return false;
    if(DB::isError($result)) $this->sql_error($query, $result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
   
  function list_newsletters($offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where (name like '%".$find."%' or description like '%".$find."%')";  
    } else {
      $mid=" "; 
    }
    $query = "select * from tiki_newsletters $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_newsletters $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
   
  function list_newsletter_subscriptions($nlId,$offset,$maxRecords,$sort_mode,$find)
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
    $mid=" where nlId=$nlId and (name like '%".$find."%' or description like '%".$find."%')";  
    } else {
      $mid=" where nlId=$nlId "; 
    }
    $query = "select * from tiki_newsletter_subscriptions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from tiki_newsletter_subscriptions $mid";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
    
  function remove_newsletter($nlId)
  {
    $query = "delete from tiki_newsletters where nlId=$nlId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $query = "delete from tiki_newsletter_subscriptions where nlId=$nlId";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $this->tikilib->remove_object('newsletter',$nlId);
    return true;    
  }
  
  // Now functions to add/remove/replace/list email addresses from the list of subscriptors
  
  
}

$nllib= new NlLib($dbTiki,$tikilib);
?>