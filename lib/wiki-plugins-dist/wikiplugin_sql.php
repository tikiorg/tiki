<?php
function wikiplugin_sql($data,$params) {
  global $tikilib;
  $ret = '';
  @$result = $tikilib->query($data);
  if(!$result) {
    return tra('There is an error in the plugin data');
  }
  $first = true;
  $class='even';
  while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
   if($first) {
     $ret.="<div align='center'><table class='normal'><tr>";
     $first = false;
     foreach(array_keys($res) as $col) {
       $ret.="<td class='heading'>$col</td>";
     }
     $ret.="</tr>";
   } 
   $ret.="<tr>";
   if($class=='even') {$class='odd';} else {$class='even';}
   foreach($res as $name=>$val) {
     $ret.="<td class='$class'>$val</td>";
   }
   $ret.="</tr>";
  }
  if($ret) {
    $ret.="</table></div>";
  }
  

  return $ret;
}
?>