<div class="quicktag">
<div>{cycle values=",,,,</div><div>" advance=false print=false}
{section name=qtg loop=$quicktags}
<a title="{tr}{$quicktags[qtg].taglabel}{/tr}" href="javascript:insertAt('{$area_name}','{$quicktags[qtg].taginsert|escape:"javascript"}');"><img
src='{$quicktags[qtg].tagicon}' alt='{tr}{$quicktags[qtg].taglabel}{/tr}' title='{tr}{$quicktags[qtg].taglabel}{/tr}' border='0' /></a>
{cycle}
{/section}
{if $quicktagscant%5==0}</div><div>{/if}
<a title="{tr}special chars{/tr}" href="#" onClick="javascript:window.open('templates/tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');"><img
src='images/ed_charmap.gif' alt='{tr}special characters{/tr}' title='{tr}special characters{/tr}' border='0' /></a></div>
</div>
