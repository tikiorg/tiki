<?php

class cc_date
{
  function getmysql()
    {
      $array = getdate(time());
      $str = "";
      $str .= $array[year];
      $str .= "-";
      if ($array[mon] < 10)
	$str .= "0".$array[mon];
      else
	$str .= $array[mon];
      $str .= "-";
      if ($array[mday] < 10)
	$str .= "0".$array[mday];
      else
	$str .= $array[mday];
      $str .= " ";
      if ($array[hours] < 10)
	$str .= "0".$array[hours];
      else
	$str .= $array[hours];
      $str .= ":";
      if ($array[minutes] < 10)
	$str .= "0".$array[minutes];
      else
	$str .= $array[minutes];

      $str .= ":";
      if ($array[seconds] < 10)
	$str .= "0".$array[seconds];
      else
	$str .= $array[seconds];
      return $str;
    }
}
?>
