{title}{tr}Contact Us{/tr}{/title}

{if $sent eq '1'}
	{remarksbox icon="accept" title="{tr}Success{/tr}"}{$message}{/remarksbox}
{else}
	<h2>{tr}Send a message to us{/tr}</h2>
	{if isset($errorMessage)}
		{remarksbox title="Invalid" type="errors"}{$errorMessage}{/remarksbox}
	{/if}
	<form class="form form-horizontal" method="post" action="tiki-contact.php">
		{ticket}
		<input type="hidden" name="to" value="{$prefs.contact_user|escape}">
		{if $prefs.contact_priority_onoff eq 'y'}
			<div class="form-group">
				<label for="priority" class="col-sm-2 control-label">{tr}Priority:{/tr}</label>
				<div class="col-sm-10">
					<select id="priority" name="priority" class="form-control">
						<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
						<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
						<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
						<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
						<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
					</select>
				</div>
			</div>
		{/if}
		{if $user eq ''}
			<div class="form-group">
				<label for="from" class="col-sm-2 control-label">{tr}Your email{/tr}:</label>
				<div class="col-sm-10">
					<input type="text" id="from" name="from" value="{$from}" class="form-control">
				</div>
			</div>
		{/if}
		<div class="form-group">
			<label for="subject" class="col-sm-2 control-label">{tr}Subject:{/tr}</label>
			<div class="col-sm-10">
				<input type="text" id="subject" name="subject" value="{$subject}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="body" class="col-sm-2 control-label">{tr}Message:{/tr}</label>
			<div class="col-sm-10">
				{textarea rows="20" name="body" id="body" class="form-control" _simple='y' _toolbars='n'}{$body}{/textarea}
			</div>
		</div>
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			{include file='antibot.tpl' td_style="form"}
		{/if}
		<div class="form-group text-center">
			<input type="submit" class="btn btn-primary btn-sm" name="send" value="{tr}Send{/tr}">
		</div>
	</form>
{/if}

{if strlen($email)>0}
	<h2>{tr}Contact us by email{/tr}</h2>
	{tr}Click here to send us an email:{/tr} {mailto text="$email" address="$email0" encode="javascript" extra='class="link"'}
{else}
	<p><a class="link" href="tiki-contact.php">{tr}Send another message{/tr}</a></p>
{/if}
