{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-messages.tpl,v 1.1.2.1 2008-03-16 17:43:12 luciash Exp $ *}

<div class="cbox">
	<div class="cbox-title">
	{tr}User Messages{/tr}
	{help url="Inter-User Messages" desc="{tr}Messages{/tr}"}
	</div>
	<div class="cbox-data">
		<form action="tiki-admin.php?page=messages" method="post" name="messages">
			<table class="admin">
				<tr><td class="form">{tr}Users can opt-out internal messages{/tr}:</td><td><input type="checkbox" name="allowmsg_is_optional" {if $prefs.allowmsg_is_optional eq 'y'}checked="checked"{/if}/></td></tr>
				<tr><td class="form">{tr}Users accept internal messages by default{/tr}:</td><td><input type="checkbox" name="allowmsg_by_default" {if $prefs.allowmsg_by_default eq 'y'}checked="checked"{/if}/></td></tr>

				<tr><td class="form">{tr}Maximum mailbox size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_mailbox_size" value="{$prefs.messu_mailbox_size|escape}" /></td></tr>
				<tr><td class="form">{tr}Maximum mail archive size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_archive_size" value="{$prefs.messu_archive_size|escape}" /></td></tr>
				<tr><td class="form">{tr}Maximum sent box size (messages, 0=unlimited){/tr}:</td><td><input type="text" name="messu_sent_size" value="{$prefs.messu_sent_size|escape}" /></td></tr>

				<tr><td colspan="2" class="button"><input type="submit" name="messagesprefs" value="{tr}Apply{/tr}" /></td></tr>
			</table>
		</form>
	</div>
</div>
