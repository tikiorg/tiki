<a class="pagetitle" href="tiki-live_support_admin.php">{tr}Live support system{/tr}</a>
<br/><br/>
[ <a class="link" {jspopup href="tiki-live_support_console.php"}>{tr}Open operator console{/tr}</a> |
<a class="link" {jspopup width="300" height="450" href="tiki-live_support_client.php"}>{tr} Open client window{/tr}</a> ]
<br/><br/>

{if count($online_operators) > 0}
<h3>{tr}Online operators{/tr}</h3>
{section name=ix loop=$online_operators}
{$online_operators[ix].user|avatarize}<br/>
{/section}
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
		<a href='tiki-live_support_admin.php?removeuser={$offline_operators[ix].user}'><img src='img/icons/trash.gif' border='0' alt='{tr}del{/tr}' title='{tr}del{/tr}' /></a>
		</td>
	</tr>
{/section}
</table>
{/if}

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


