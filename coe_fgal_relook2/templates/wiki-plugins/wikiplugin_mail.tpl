{if !empty($sents)}
{remarksbox type='feedback' title="{tr}Message sent to{/tr}"}
	{tr}Email sent to:{/tr}
	<ul>
	{foreach from=$sents item=sent}
		<li>{$sent|escape}</li>
	{/foreach}
	<ul>
{/remarksbox}
{/if}
{if !empty($mail_error)}
{remarksbox type='errors' title="{tr}Errors{/tr}"}
	{tr}Error{/tr}
{/remarksbox}
{/if}
{if $preview}
	<form method="post">
	{tr}The message will be sent to{/tr}
	<ul>
		<li>{tr}Emails number:{/tr} {$nbTo}</li>
		<li>{tr}Subject:{/tr} {$mail_subject|truncate:100:"..."}</li>
		<li>{tr}Message:{/tr}  {$mail_mess|truncate:100:"..."}</li>
	</ul>
	<input type="hidden" name="mail_subject" value="{$mail_subject|escape}" />
	<input type="hidden" name="mail_mess" value="{$mail_mess|escape}" />
	<input type="submit" name="mail_send{$ipluginmail}" value="{tr}Send Mail{/tr}" />
	</form>
	<form method="post">
	<input type="submit" name="mail_cancel{$ipluginmail}" value="{tr}Cancel{/tr}" />
	</form>
{else}
<form method="post">
<table>
	{if $params.showuserdd eq 'y' or $params.showrealnamedd eq 'y'}
	<tr>
	<td>
		<label for="mail_user_dd{$ipluginmail}">{tr}Send to users:{/tr}</label>
	</td>
	<td>
		{if $params.showuserdd eq 'y'}
    		<select name="mail_user_dd[]" id="mail_user_dd{$ipluginmail}" multiple="multiple" size="8">
				<option value="" />
					{foreach from=$users item=muser}
						<option value="{$muser.userId}"{if in_array($muser.userId, $mail_user_dd)} selected="selected"{/if}>{$muser.login|escape}</option>
					{/foreach}
    		</select>
		{/if}
		{if $params.showrealnamedd eq 'y'}
    		<select name="mail_user_dd[]" id="mail_user_dd{$ipluginmail}" multiple="multiple" size="8">
				<option value="" />
					{foreach from=$names item=muser}
						<option value="{$muser.userId}"{if in_array($muser.userId, $mail_user_dd)} selected="selected"{/if}>{$muser.login|username:true:false}</option>
					{/foreach}
    		</select>
		{/if}
		{remarksbox type='tip'  title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
	</td>
	</tr>
	<tr>
	<td>
		<label for="mail_user{$ipluginmail}">{tr}Send to:{/tr}</label>
	</td>
	<td>
		{if $params.showuser eq 'y'}
			<input type="text" size="80" name="mail_user" value="{$mail_user}"/>
			{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Email separated by comma{/tr}{/remarksbox}
		{/if}
	</td>
	</tr>
	{/if}

	{if $params.showgroupdd eq 'y'}
	<tr>
	<td>
		<label for="mail_group_dd{$ipluginmail}">{tr}Send to groups:{/tr}</label>
	</td>
	<td>
		{if $params.showuser eq 'y'}
		{/if}
		{if $params.showgroupdd eq 'y'}
			{foreach from=$groups key=groupname item=gps name=mailgroups}
				<div class="wpmailgroup" style="vertical-align: top;">
				{if !empty($groupname)}{$groupname|escape}{/if}
    			<select name="mail_group_dd[][]" id="mail_group_dd{$ipluginmail}" multiple="multiple" size="8">
					<option value="" />
					{foreach from=$gps item=mgroup}
						{if $mgroup eq 'Anonymous'}
						{elseif $mgroup eq 'Registered'}
							{* <option value="all"{if in_array('All', $mail_group_dd[$smarty.foreach.mailgroups.index])} selected="selected"{/if}>{tr}All users{/tr}</option> *}
						{else}
							<option value="{$mgroup}"{if in_array($mgroup, $mail_group_dd[$smarty.foreach.mailgroups.index])} selected="selected"{/if}>{$mgroup|escape}</option>
						{/if}
					{/foreach}
    			</select>
				</div>
			{/foreach}
			{remarksbox type='tip' title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
		{/if}
	</td>
	</tr>
	{/if}

	<tr>
	<td>
		<label for="mail_subject{$ipluginmail}">{tr}Subject:{/tr}</label>
	</td>
	<td>
		<input type="text" id="mail_subject{$ipluginmail}" name="mail_subject" value="{$mail_subject}" size="80" />
	</td>
	</tr>
	<tr>
	<td>
		<label for="mail_mess{$ipluginmail}">{tr}Message:{/tr}</label>
	</td>
	<td>
		<textarea id="mail_mess{$ipluginmail}" name="mail_mess" value="{$mail_mess}" rows="20" cols="80"></textarea>
	</td>
	</tr>
	<tr>
	<td></td>
	<td>
		<input type="submit" name="mail_preview{$ipluginmail}" value="{tr}Preview Mail{/tr}" />
	</td>
	</tr>
</table>
</form>
{/if}
