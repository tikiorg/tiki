{title help="Live Support"}{tr}Live support system{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href='#' _onclick="javascript:window.open('tiki-live_support_console.php','','menubar=no,scrollbars=yes,resizable=yes,height=400,width=600');" class="btn btn-default" _text="{tr}Open operator console{/tr}"}
	{button href='#' _onclick="javascript:window.open('tiki-live_support_client.php','','menubar=no,scrollbars=yes,resizable=yes,height=450,width=300');" class="btn btn-default" _text="{tr}Open client window{/tr}"}
	{button href="?show_html" class="btn btn-default" _text="{tr}Generate HTML{/tr}"}
	{button href="tiki-live_support_transcripts.php" class="btn btn-default" _type="link" _icon_name="file-text-o" _text="{tr}Transcripts{/tr}"}
</div>

{if $html}
	<b>Generated HTML code:</b><br>
	Copy-paste the following XHTML snippet in the pages where you want to provide live support.<br>
	<table>
	<tr>
		<td>
		<small>HTML code</small><br>
		<textarea rows="5" cols="60">{$html|escape}</textarea>
		</td>
		<td>
		<small>result</small><br>
		{$html}
		</td>
	</tr>
	</table>
{/if}
{if count($online_operators) > 0}
<h2>{tr}Online operators{/tr}</h2>
<div class="table-responsive">
<table class="table">
	<tr>
		<th style="text-align:center;">
		{tr}Operator{/tr}
		</th>
		<th colspan='2'>
		{tr}Stats{/tr}
		</th>
	</tr>
{cycle values='odd,even' print=false}
{section name=ix loop=$online_operators}
<tr>
		<td style="text-align:center;">
			{$online_operators[ix].user|avatarize}<br>
			<b>{$online_operators[ix].user|escape}</b>
		</td>
		<td>
			<table>
				<tr>
					<td>{tr}Accepted requests:{/tr}</td>
					<td>{$online_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{tr}{$online_operators[ix].status}{/tr} {tr}since:{/tr}</td>
					<td>{if $online_operators[ix].status_since ne "0"}{$online_operators[ix].status_since|tiki_short_datetime}{else}{tr}unknown{/tr}{/if}</td>
				</tr>
				<tr>
					<td><a class="link" href="tiki-live_support_transcripts.php?filter_operator={$online_operators[ix].user|escape}">{tr}transcripts{/tr}</a></td>
				</tr>
			</table>
		</td>
		<td style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$online_operators[ix].user|escape}'><img src='img/icons/trash.gif' alt="{tr}Del{/tr}" title="{tr}Del{/tr}"></a>
<a href='tiki-live_support_admin.php?offline={$online_operators[ix].user|escape}'><img src='img/icons/icon_unwatch.png' alt="{tr}offline{/tr}" title="{tr}offline{/tr}"></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
</div>
{/if}

{if count($offline_operators) > 0}
<h2>{tr}Offline operators{/tr}</h2>
{cycle values='odd,even' print=false}
<div class="table-responsive">
<table class="table">
	<tr>
		<th style="text-align:center;">
		{tr}Operator{/tr}
		</th>
		<th colspan='2'>
		{tr}Stats{/tr}
		</th>
	</tr>
{section name=ix loop=$offline_operators}
	<tr>
		<td style="text-align:center;">
			{$offline_operators[ix].user|avatarize}<br>
			<b>{$offline_operators[ix].user|escape}</b>
		</td>
		<td>
			<table >
				<tr>
					<td>{tr}Accepted requests:{/tr}</td>
					<td>{$offline_operators[ix].accepted_requests}</td>
				</tr>
				<tr>
					<td>{tr}{$offline_operators[ix].status}{/tr} {tr}since:{/tr}</td>
					<td>{if $offline_operators[ix].status_since ne "0"}{$offline_operators[ix].status_since|tiki_short_datetime}{else}{tr}unknown{/tr}{/if}</td>
				</tr>
			</table>
		</td>
		<td style="text-align:right;">
		{if $tiki_p_live_support_admin eq 'y'}
			<a href='tiki-live_support_admin.php?removeuser={$offline_operators[ix].user|escape}'><img src='img/icons/trash.gif' alt="{tr}Del{/tr}" title="{tr}Del{/tr}"></a>
		{else}
			&nbsp;
		{/if}
		</td>
	</tr>
{/section}
</table>
</div>
{/if}

{if $tiki_p_live_support_admin eq 'y'}
<h2>{tr}Add an operator to the system{/tr}</h2>
<br>
<form method="post" action="tiki-live_support_admin.php" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}User{/tr}</label>
		<div class="col-sm-7">
		    <select name="user" class="form-control">
				{section name=ix loop=$users}
					<option value="{$users[ix].user|escape}">{$users[ix].user|escape}</option>
				{/section}
			</select>
			<div class="help-block">
				{tr}Operators must be tiki users{/tr}
			</div>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
		    <input type="submit" class="btn btn-default btn-sm" name="adduser" value="{tr}Set as Operator{/tr}">
	    </div>
    </div>
</form>
{/if}

