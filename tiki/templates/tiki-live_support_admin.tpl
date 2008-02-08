<h1><a class="pagetitle" href="tiki-live_support_admin.php">
{tr}Live support system{/tr}</a>

  
      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Live+Support" target="tikihelp" class="tikihelp" title="{tr}Live Support{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}



      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-live_support_admin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Live Support tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}


</h1>
<a class="linkbut" {jspopup href="tiki-live_support_console.php"}>{tr}Open operator console{/tr}</a>
<a class="linkbut" {jspopup width="300" height="450" href="tiki-live_support_client.php"}> {tr}Open client window{/tr}</a>
<a class="linkbut" href="tiki-live_support_admin.php?show_html">{tr}Generate HTML{/tr}</a>
<a class="linkbut" href="tiki-live_support_transcripts.php">{tr}Transcripts{/tr}</a>
<!--<a class="linkbut" href="tiki-live_support_messages.php">{tr}Support tickets{/tr}</a>-->
<br /><br />
{if $html}
	<b>Generated HTML code:</b><br />
	Copy-paste the following XHTML snippet in the pages where you want to provide live support.<br />
	<table>
	<tr>
		<td>
		<small>HTML code</small><br />	
		<textarea rows="5" cols="60">{$html|escape}</textarea>
		</td>
		<td>
		<small>result</small><br />
		{$html}
		</td>
	</tr>
	</table>	
{/if}
{if count($online_operators) > 0}
<h2>{tr}Online operators{/tr}</h2>
<table class="normal">
	<tr>
		<td  class="heading" style="text-align:center;">	
		{tr}Operator{/tr}
		</td>
		<td class="heading" colspan='2'>
		{tr}Stats{/tr}
		</td>		
	</tr>
{cycle values='odd,even' print=false}	
{section name=ix loop=$online_operators}
<tr>
		<td  class="{cycle advance=false}" style="text-align:center;">
			{$online_operators[ix].user|avatarize}<br />	
			<b>{$online_operators[ix].user}</b>
		</td>
		<td class="{cycle advance=false}">
			<table >
				<tr>
					<td>{tr}Accepted requests{/tr}:</td>
					<td>{$online_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{tr}{$online_operators[ix].status}{/tr} {tr}since{/tr}:</td>
					<td>{if $online_operators[ix].status_since ne "0"}{$online_operators[ix].status_since|tiki_short_datetime}{else}{tr}unknown{/tr}{/if}</td>
				</tr>
				<tr>
					<td><a class="link" href="tiki-live_support_transcripts.php?filter_operator={$online_operators[ix].user}">{tr}transcripts{/tr}</a></td>
				</tr>
			</table>
		</td>
		<td class="{cycle}" style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$online_operators[ix].user}'><img src='img/icons/trash.gif' border='0' alt='{tr}Del{/tr}' title='{tr}Del{/tr}' /></a>
<a href='tiki-live_support_admin.php?offline={$online_operators[ix].user}'><img src='img/icons/icon_unwatch.png' border='0' alt='{tr}offline{/tr}' title='{tr}offline{/tr}' /></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
{/if}

{if count($offline_operators) > 0}
<h2>{tr}Offline operators{/tr}</h2>
{cycle values='odd,even' print=false}
<table class="normal">
	<tr>
		<td  class="heading" style="text-align:center;">	
		{tr}Operator{/tr}
		</td>
		<td class="heading" colspan='2'>
		{tr}Stats{/tr}
		</td>		
	</tr>
{section name=ix loop=$offline_operators}
	<tr>
		<td  class="{cycle advance=false}" style="text-align:center;">
			{$offline_operators[ix].user|avatarize}<br />	
			<b>{$offline_operators[ix].user}</b>
		</td>
		<td class="{cycle advance=false}">
			<table >
				<tr>
					<td>{tr}Accepted requests{/tr}:</td>
					<td>{$offline_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{tr}{$offline_operators[ix].status}{/tr} {tr}since{/tr}:</td>
					<td>{if $offline_operators[ix].status_since ne "0"}{$offline_operators[ix].status_since|tiki_short_datetime}{else}{tr}unknown{/tr}{/if}</td>
				</tr>
			</table>
		</td>
		<td class="{cycle}" style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$offline_operators[ix].user}'><img src='img/icons/trash.gif' border='0' alt='{tr}Del{/tr}' title='{tr}Del{/tr}' /></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
{/if}

{if $tiki_p_live_support_admin eq 'y'}
<h2>{tr}Add an operator to the system{/tr}</h2>
<small>{tr}Operators must be tiki users{/tr}</small>
<form method="post" action="tiki-live_support_admin.php">
<table class="normal">
	<tr>
		<td class="formcolor">{tr}User{/tr}</td>
		<td class="formcolor">
			<select name="user">
				{section name=ix loop=$users}
					<option value="{$users[ix].user|escape}">{$users[ix].user}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td class="formcolor">&nbsp;</td>
		<td class="formcolor">
			<input type="submit" name="adduser" value="{tr}Set as Operator{/tr}" />
		</td>
	</tr>
</table>
</form>
{/if}

