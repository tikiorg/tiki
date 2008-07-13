<?php // -*- coding:utf-8 -*-
/* $Id: /cvsroot/tikiwiki/tiki/tiki-special_chars.php,v 1.5.2.1 2007-12-22 01:56:52 mose Exp $ */
echo  '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Special Character Input</title>
</head>
<body style="background:#101070; color:white;">
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var target;
function handleListChange(theList) {
    var numSelected = theList.selectedIndex;
    if (numSelected != 0) {
        document.getElementById('spec').value += theList.options[numSelected].value;
        theList.selectedIndex = 0;
    }
}
//--><!]]>
</script>

<form name="characters">
<table border="0" cellspacing="0" cellpadding="2">
  <!--
  <tr>
    <td align="left">
      <p class="smallheader">Select the character then copy and paste them from the textarea.</p>
    </td>
  </tr>
  -->
  <tr>
    <td align="left">
      <table border="0" cellspacing="0"
cellpadding="0">
        <tr>
          <td align="center">
            <select name="a"
onchange="handleListChange(this)">
              <option value="a" selected="selected"> a </option>
              <option value="À"> À </option>
              <option value="à"> à </option>
              <option value="Á"> Á </option>
              <option value="á"> á </option>
              <option value="Â"> Â </option>
              <option value="â"> â </option>
              <option value="Ã"> Ã </option>
              <option value="ã"> ã </option>
              <option value="Ä"> Ä </option>
              <option value="ä"> ä </option>
              <option value="Å"> Å </option>
              <option value="å"> å </option>
            </select>
          </td>
          <td align="center">
            <select name="e"
onchange="handleListChange(this)">
              <option value="e" selected="selected"> e </option>
              <option value="È"> È </option>
              <option value="è"> è </option>
              <option value="É"> É </option>
              <option value="é"> é </option>
              <option value="Ê"> Ê </option>
              <option value="ê"> ê </option>
              <option value="Ë"> Ë </option>
              <option value="ë"> ë </option>
            </select>
          </td>
          <td align="center">
            <select name="i"
onchange="handleListChange(this)">
              <option value="i" selected="selected"> i </option>
              <option value="Ì"> Ì </option>
              <option value="ì"> ì </option>
              <option value="Í"> Í </option>
              <option value="í"> í </option>
              <option value="Î"> Î </option>
              <option value="î"> î </option>
              <option value="Ï"> Ï </option>
              <option value="ï"> Ï </option>
            </select>
          </td>
          <td align="center">
            <select name="o"
onchange="handleListChange(this)">
              <option value="o" selected="selected"> o </option>
              <option value="Ò"> Ò </option>
              <option value="ò"> ò </option>
              <option value="Ó"> Ó </option>
              <option value="ó"> ó </option>
              <option value="Ô"> Ô </option>
              <option value="ô"> ô </option>
              <option value="Õ"> Õ </option>
              <option value="õ"> õ </option>
              <option value="Ö"> Ö </option>
              <option value="ö"> ö </option>
            </select>
          </td>
          <td align="center">
            <select name="u"
onchange="handleListChange(this)">
              <option value="u" selected="selected"> u </option>
              <option value="Ù"> Ù </option>
              <option value="ù"> ù </option>
              <option value="Ú"> Ú </option>
              <option value="ú"> ú </option>
              <option value="Û"> Û </option>
              <option value="û"> û </option>
              <option value="Ü"> Ü </option>
              <option value="ü"> ü </option>
            </select>
          </td>
          <td align="center">
            <select name="Other"
onchange="handleListChange(this)">
              <option value="misc" selected="selected"> Other
</option>
              <option value="¢"> ¢ </option>
              <option value="£"> £ </option>
              <option value="¤"> ¤ </option>
              <option value="¥"> ¥ </option>
              <option value="Æ"> Æ </option>
              <option value="æ"> æ </option>
              <option value="Œ"> Œ </option>
              <option value="œ"> œ </option>
              <option value="ß"> ß </option>
              <option value="Ç"> Ç </option>
              <option value="ç"> ç </option>
              <option value="Ñ"> Ñ </option>
              <option value="ñ"> ñ </option>
              <option value="ý"> ý </option>
              <option value="ÿ"> ÿ </option>
              <option value="¿"> ¿ </option>
              <option value="&lt;"> < </option>
              <option value="&gt;"> > </option>
              <option value="["> [ </option>
              <option value="]"> ] </option>
              <option value="|"> | </option>
              <option value="("> ) </option>
              <option value=")"> ( </option>
              <option value="{"> { </option>
              <option value="}"> } </option>
              <option value="'"> ' </option>
              <option value="&quot;"> " </option>
              <option value="_"> _ </option>
              <option value="-"> - </option>
              <option value="*"> * </option>
              <option value="#"> # </option>
              <option value=";"> ; </option>
              <option value=":"> : </option>
              <option value="&amp;"> & </option>
		  <option value="&reg;"> ® </option>
		  <option value="&trade;"> &trade; </option>
		  <option value="&plusmn;"> ± </option>
             </select>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" class="fixed">
			<?php $area = preg_replace('/[^-_0-9a-zA-Z\.]/','',$_REQUEST["area_name"]); ?>
      <input type="text" id='spec' />
  <input type="button" class="button"
onclick="javascript:window.opener.document.getElementById('<?php echo $area; ?>').value=window.opener.document.getElementById('<?php echo $area; ?>').value+getElementById('spec').value;" name="ins" value="ins" />
      <input type="button" class="button"
onclick="window.close();" name="close" value="close" />
    </td>
  </tr>
</table>
</form>
</body>
