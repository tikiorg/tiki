<div class="quicktag">
<a href="#" onclick="toggleBlock('quicktags{$qtnum}');" class="link"><img src="img/icons/plus.gif" border='0' alt='+' />&nbsp;{tr}Quicktags{/tr} ...</a><br /><br />
<div id='quicktags{$qtnum}' {if $showtags}style="display:block;"{else}style="display:none;"{/if}>
<div>
{cycle values=$qtcycle|default:",,,</div><div>" advance=false print=false}
{section name=qtg loop=$quicktags}
<a title="{tr}{$quicktags[qtg].taglabel}{/tr}" href="javascript:insertAt('{$area_name}','{$quicktags[qtg].taginsert|escape:"javascript"}');"><img
src='{$quicktags[qtg].tagicon}' alt='{tr}{$quicktags[qtg].taglabel}{/tr}' title='{tr}{$quicktags[qtg].taglabel}{/tr}' border='0' /></a>
{cycle}
{/section}
<a title="{tr}special chars{/tr}" href="#" onClick="javascript:window.open('templates/tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');"><img
src='images/ed_charmap.gif' alt='{tr}special characters{/tr}' title='{tr}special characters{/tr}' border='0' /></a>
</div>
{if $tiki_p_admin eq 'y'}
<br /><a href="tiki-admin_quicktags.php" class="link">{tr}admin quicktags{/tr}</a>
{/if}
</div>
</div>
