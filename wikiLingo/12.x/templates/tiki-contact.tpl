{title}{tr}Contact Us{/tr}{/title}

{if $sent eq '1'}
{remarksbox icon="accept" title="{tr}Success{/tr}"}{$message}{/remarksbox}
{else}
<h2>{tr}Send a message to us{/tr}</h2>
	{if isset($errorMessage)}
		{remarksbox title="Invalid" type="errors"}{$errorMessage}{/remarksbox}
	{/if}
	<form method="post" action="tiki-contact.php">
		{ticket}
		<input type="hidden" name="to" value="{$prefs.contact_user|escape}">
		<table class="formcolor">
		{if $prefs.contact_priority_onoff eq 'y'}
			<tr>
				<td>{tr}Priority:{/tr}</td>
				<td style="width:99%">
					<select name="priority">
						<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
						<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
					</select>
				</td>
			</tr>
		{/if}
			{if $user eq ''}
			<tr>
				<td>{tr}Your email{/tr}:</td>
				<td>
					<input type="text" name="from" value="{$from}" maxlength="255" style="width:25%">
				</td>
			</tr>
			{/if}
			<tr>
				<td>{tr}Subject:{/tr}</td>
				<td>
					<input type="text" name="subject" value="{$subject}" maxlength="255" style="width:99%">
				</td>
			</tr>
			<tr>
				<td>{tr}Message:{/tr}</td>
				<td>
					{textarea rows="20" name="body" cols="80" _simple='y' _toolbars='n'}{$body}{/textarea}
				</td>
			</tr>
			{if $prefs.feature_antibot eq 'y' && $user eq ''}
				{include file='antibot.tpl' td_style="form"}
			{/if}
			<tr>
				<td></td>
				<td>
					<input type="submit" class="btn btn-default" name="send" value="{tr}Send{/tr}">
				</td>
			</tr>
		</table>
	</form>
{/if}

{if strlen($email)>0}
	<h2>{tr}Contact us by email{/tr}</h2>
	{tr}Click here to send us an email:{/tr} {mailto text="$email" address="$email0" encode="javascript" extra='class="link"'}
{else}
	<p><a class="link" href="tiki-contact.php">{tr}Send another message{/tr}</a></p>
{/if}
