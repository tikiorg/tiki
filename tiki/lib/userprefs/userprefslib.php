<?php
class UserPrefsLib extends TikiLib {

  function UserPrefsLib($db) 
  {
    # this is probably uneeded now
    if(!$db) {
      die("Invalid db object passed to UsersPrefsLib constructor");  
    }
    $this->db = $db;  
  }
  
  function set_user_avatar($user,$type,$avatarLibName,$avatarName,$avatarSize,$avatarType,$avatarData)
  {
    $avatarData = addslashes($avatarData);
    $avatarName = addslashes($avatarName);
    $query = "update users_users set
      avatarType = '$type',
      avatarLibName = '$avatarLibName',
      avatarName = '$avatarName',
      avatarSize = '$avatarSize',
      avatarFileType = '$avatarType',
      avatarData = '$avatarData'
      where login='$user'";
    $result = $this->query($query);
  }
  
  function get_user_avatar_img($user)
  {
    $query = "select * from users_users where login='$user'";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    return $res;
  }
  
}

$userprefslib= new UserPrefsLib($dbTiki);

?>