{section name=qtg loop=$quicktags}
<a title="{tr}{$quicktags[qtg].taglabel}{/tr}" class="link" href="javascript:insertAt('{$area_name}','{$quicktags[qtg].taginsert|escape:"javascript"}');"><img src='{$quicktags[qtg].tagicon}' alt='{tr}{$quicktags[qtg].taglabel}{/tr}' title='{tr}{$quicktags[qtg].taglabel}{/tr}' border='0' /></a>
{/section}
<a title="{tr}special chars{/tr}" class="link" href="#" onClick="javascript:window.open('templates/tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');"><img src='images/ed_charmap.gif' alt='{tr}special characters{/tr}' title='{tr}special characters{/tr}' border='0' /></a>
