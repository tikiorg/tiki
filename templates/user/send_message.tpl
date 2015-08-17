{extends 'layout_view.tpl'}
{block name="title"}
	{title}{$title|escape}{/title}
{/block}
{block name="content"}

<div id="message">
	<form id="send-message-form" method="post" action="{service controller=user action=send_message}" name="f">
		<input type="hidden" name="to" value="{$userwatch|escape}">
		<input type="hidden" name="userwatch" value="{$userwatch|escape}">

		<p>{tr}The following message will be sent to user{/tr} {$userwatch|username}:</p>
		<div class="form-group">
			<label class="control-label" for="priority">{tr}Priority{/tr}</label>
			<select name="priority" id="priority" class="form-control">
				<option value="1" {if $priority eq 1}selected="selected"{/if}>1: {tr}Lowest{/tr}</option>
				<option value="2" {if $priority eq 2}selected="selected"{/if}>2: {tr}Low{/tr}</option>
				<option value="3" {if $priority eq 3}selected="selected"{/if}>3: {tr}Normal{/tr}</option>
				<option value="4" {if $priority eq 4}selected="selected"{/if}>4: {tr}High{/tr}</option>
				<option value="5" {if $priority eq 5}selected="selected"{/if}>5: {tr}Very High{/tr}</option>
			</select>
		</div>
		<div class="form-group">
			<label class="control-label" for="subject">{tr}Subject{/tr}</label>
			<input type="text" name="subject" id="subject" value="" maxlength="255" class="form-control">
		</div>
		<div class="form-group">
			<label class="control-label" for="message">{tr}Message Body{/tr}</label>
			<textarea rows="12" class="form-control" name="body" id="message"></textarea>
		</div>
		<div class="form-group">
			<input type="checkbox" name="replytome" id="replytome">
			<label for="replytome">
				{tr}Reply-to my email{/tr}
				{help url="User+Information" desc="{tr}Reply-to my email:{/tr}{tr}The user will be able to reply to you directly via email.{/tr}"}
			</label>
			<input type="checkbox" name="bccme" id="bccme">
			<label for="bccme">
				{tr}Send me a copy{/tr}
				{help url="User+Information" desc="{tr}Send me a copy:{/tr}{tr}You will be sent a copy of this email.{/tr}"}
			</label>
		</div>
		<div class="submit">
			<input type="submit" class="btn btn-primary" name="send" value="{tr}Send{/tr}">
		</div>
	</form>
</div>
{/block}