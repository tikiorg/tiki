<?php
function wikiplugin_split($data,$params) {
  global $tikilib;
  global $replacement;
  
  extract($params);
  //$data=htmlspecialchars(trim($data));
  $data = str_replace("\n",'',$data);
  $percent = "\&#x25";
  $result = "<table border='0' width='100" . $percent . "'><tr>";
  $sections = preg_split("/---+/\n",$data);
  $count = count($sections);
  $columnSize = floor(100 / $count);

  for ($i = 0; $i < $count; $i++)
  {
      $result .= "<td valign='top' width='" . $columnSize . $percent . "'>";
      $result .= $sections[$i];
      $result .= "</td>";
  }

  $result .= "</tr></table>";

  return $result;
}
?>