<a class="pagetitle" href="tiki-live_support_admin.php">{tr}Live support system{/tr}</a>
<br/><br/>
[ <a class="link" {jspopup href="tiki-live_support_console.php"}>{tr}Open operator console{/tr}</a> |
<a class="link" {jspopup width="300" height="450" href="tiki-live_support_client.php"}> {tr}Open client window{/tr}</a> |
<a class="link" href="tiki-live_support_admin.php?show_html">{tr}Generate HTML{/tr}</a> | 
<a class="link" href="tiki-live_support_transcripts.php">{tr}Transcripts{/tr}</a>
<!--<a class="link" href="tiki-live_support_messages.php">{tr}Support tickets{/tr}</a>-->
 ]
<br/><br/>
{if $html}
	<b>Generated HTML code:</b><br/>
	Copy-paste the following XHTML snippet in the pages where you want to provide live support.<br/>
	<table>
	<tr>
		<td>
		<small>HTML code</small><br/>	
		<textarea rows="5" cols="60">{$html|escape}</textarea>
		</td>
		<td>
		<small>result</small><br/>
		{$html}
		</td>
	</tr>
	</table>	
{/if}
{if count($online_operators) > 0}
<h3>{tr}Online operators{/tr}</h3>
<table class="normal">
	<tr>
		<td width="2%" class="heading" style="text-align:center;">	
		{tr}Operator{/tr}
		</td>
		<td class="heading" colspan='2'>
		{tr}stats{/tr}
		</td>		
	</tr>
{cycle values='odd,even' print=false}	
{section name=ix loop=$online_operators}
<tr>
		<td width="2%" class="{cycle advance=false}" style="text-align:center;">
			{$online_operators[ix].user|avatarize}<br />	
			<b>{$online_operators[ix].user}</b>
		</td>
		<td class="{cycle advance=false}">
			<table width="100%">
				<tr>
					<td>{tr}Accepted requests{/tr}:</td>
					<td>{$online_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{$online_operators[ix].status} {tr}since{/tr}:</td>
					<td>{$online_operators[ix].status_since|tiki_short_datetime}</td>
					
				</tr>
				<tr>
					<td><a class="link" href="tiki-live_support_transcripts.php?filter_operator={$online_operators[ix].user}">{tr}transcripts{/tr}</a></td>
				</tr>
			</table>
		</td>
		<td class="{cycle}" style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$offline_operators[ix].user}'><img src='img/icons/trash.gif' border='0' alt='{tr}del{/tr}' title='{tr}del{/tr}' /></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
{/if}

{if count($offline_operators) > 0}
<h3>{tr}Offline operators{/tr}</h3>
{cycle values='odd,even' print=false}
<table class="normal">
	<tr>
		<td width="2%" class="heading" style="text-align:center;">	
		{tr}Operator{/tr}
		</td>
		<td class="heading" colspan='2'>
		{tr}stats{/tr}
		</td>		
	</tr>
{section name=ix loop=$offline_operators}
	<tr>
		<td width="2%" class="{cycle advance=false}" style="text-align:center;">
			{$offline_operators[ix].user|avatarize}<br />	
			<b>{$offline_operators[ix].user}</b>
		</td>
		<td class="{cycle advance=false}">
			<table width="100%">
				<tr>
					<td>{tr}Accepted requests{/tr}:</td>
					<td>{$offline_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{$offline_operators[ix].status} {tr}since{/tr}:</td>
					<td>{$offline_operators[ix].status_since|tiki_short_datetime}</td>
				</tr>
			</table>
		</td>
		<td class="{cycle}" style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$offline_operators[ix].user}'><img src='img/icons/trash.gif' border='0' alt='{tr}del{/tr}' title='{tr}del{/tr}' /></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
{/if}

{if $tiki_p_live_support_admin eq 'y'}
<h3>{tr}Add an operator to the system{/tr}</h3>
<small>{tr}Operators must be tiki users{/tr}</small>
<form method="post" action="tiki-live_support_admin.php">
<table class="normal">
	<tr>
		<td class="formcolor">{tr}user{/tr}</td>
		<td class="formcolor">
			<select name="user">
				{section name=ix loop=$users}
					<option value="{$users[ix].user}">{$users[ix].user}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td class="formcolor">&nbsp;</td>
		<td class="formcolor">
			<input type="submit" name="adduser" value="{tr}set as operator{/tr}" />
		</td>
	</tr>
</table>
</form>
{/if}

