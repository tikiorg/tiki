{include file="header.tpl"}
<center><h1>{tr}Interactive translator{/tr}</h1>
{if $error eq 'y'}
<h2>{$msg}</h2>
{else}
<b><i>{$analysed_word}</i></b></center>
{if $update eq 'y'}
<center>{tr}has been updated{/tr}<br> <center><input type='submit' value ='Close this window' onclick='window.opener.location.reload();self.close();'>&nbsp;&nbsp;<input type='submit' value ='Go Back' onclick='javascript:history.go(-1);'>";
<br>*{tr}Closing the window will reload your main browser{/tr}
{else}
<center><input type='submit' value ='Close this window' onclick='self.close();'></center>
<table class='normal'>
{assign var=first value=""}
{cycle values="odd,even" print=false}
{section name=ix loop=$entries}
	{if  $first ne $entries[ix].lang }
	{assign var=first value=$entries[ix].lang}
	<tr><td colspan='2'>&nbsp;</td></tr>
	<tr><td colspan=2 class='heading'><b>{tr}Language{/tr}:<i> {$entries[ix].lang}</i></b></td></tr>
	{/if}
	<form><tr class="{cycle advance=true}"><td width='40%'>
   	<input type=hidden name='lang' value='{$entries[ix].lang}'>
	<input type=hidden name='src' value='{$entries[ix].urlsource}'>{$entries[ix].source}</td><td>
	<input type=text name='dst' value='{$entries[ix].trans}' style='width:190px;' >&nbsp;&nbsp;
	<input type=submit name='submit' value='Submit'>
   	</td></tr></form>
{/section}
</table>
{/if}{*End of not-update*}
{/if}{*End of not-error*}
