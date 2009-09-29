{* $Id$ *}

<form action="tiki-admin.php?page=messages" method="post" name="messages">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="messagesprefs" value="{tr}Apply{/tr}" />
	</div>

				{preference name=feature_messages}

	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>
		<table class="admin">
			<tr><td class="form">{tr}Users accept internal messages by default{/tr}:</td><td><input type="checkbox" name="allowmsg_by_default" {if $prefs.allowmsg_by_default eq 'y'}checked="checked"{/if}/></td></tr>
			<tr><td class="form">{tr}Users can opt-out internal messages{/tr}:</td><td><input type="checkbox" name="allowmsg_is_optional" {if $prefs.allowmsg_is_optional eq 'y'}checked="checked"{/if}/></td></tr>

			<tr><td class="form">{tr}Maximum mailbox size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_mailbox_size" value="{$prefs.messu_mailbox_size|escape}" /></td></tr>
			<tr><td class="form">{tr}Maximum mail archive size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_archive_size" value="{$prefs.messu_archive_size|escape}" /></td></tr>
			<tr><td class="form">{tr}Maximum sent box size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_sent_size" value="{$prefs.messu_sent_size|escape}" /></td></tr>
	</table>
	</fieldset>
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="messagesprefs" value="{tr}Apply{/tr}" />
	</div>
</form>
