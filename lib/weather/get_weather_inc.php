<?php
global $fCityID;
?>
<form name="form1" method="get" action="<?=$PHP_SELF?>">
  <div align="center">Enter a City ID to test: 
    <input type="text" name="fCityID" value="<?=(empty($fCityID)?'USNC0558':$fCityID)?>">
    <input type="submit" name="Submit" value="Submit">
  </div>
</form>
<hr>
<a href="http://www.w3.weather.com/outlook/travel/local/">Search weather.com</a> for a city code. It looks like  "USNC0120" or "FRXX0076" and appears in the url of the city.

<?
if ( !empty($fCityID) ) {

  // This is the code you need to have on your page.
  
  // $CityID is being submitted from the user via the
  // form you filled in. You can replace it with a
  // static string if you like (eg: "USPA0380")
  
  // Naturally you can replace $Weather->OutputDebug
  // with your own calls (getTemp for example)

  include("Weather.php");
  $Weather = New Weather($fCityID);
  $Weather->OutputDebug();
  
  // End weather fetching code. Easy, eh?


?>
 <?
}
?>

<p>This is a virgin port of <a href="http://www.ravis.org/code/weather/">Weather</a> from ravis.org</p>
