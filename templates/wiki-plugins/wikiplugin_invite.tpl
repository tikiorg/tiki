{* $Id$ *}
<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="email" class="col-sm-4 control-label">{tr}Email address of invitee{/tr}</label>
		<div class="col-sm-8">
			<input name="email" id="email" class="form-control" type="text" value="{$email}">
		</div>
	</div>
	<div class="form-group">
		<label for="message" class="col-sm-4 control-label">{tr}Message{/tr}</label>
		<div class="col-sm-8">
			<textarea name="message" id="message" class="form-control" rows="10">{$message|escape}</textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="groups" class="col-sm-4 control-label">{tr}Set in these groups{/tr}</label>
		<div class="col-sm-8">
			<select name="groups[]" id="groups" class="form-control" multiple="multiple">
				{foreach from=$userGroups key=gx item=gi}
					<option value="{$gx|escape}"{if (isset($groups) && in_array($gx, $groups)) or (!isset($groups) && $gx eq $params.defaultgroup)} selected="selected"{/if}>{$gx|escape}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="text-center margin-bottom-md">
		<input type="submit" class="btn btn-default" name="invite" value="{tr}Invite{/tr}">
	</div>
</form>
