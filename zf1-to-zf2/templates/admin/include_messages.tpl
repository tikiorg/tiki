{* $Id$ *}

<form class="form-horizontal" action="tiki-admin.php?page=messages" method="post" name="messages">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-default btn-sm" name="messagesprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_messages visible="always"}
	</fieldset>

	<fieldset>
		<legend>{tr}Settings{/tr}</legend>

		{preference name=allowmsg_by_default}
		{preference name=allowmsg_is_optional}
		{preference name=messu_mailbox_size}
		{preference name=messu_archive_size}
		{preference name=messu_sent_size}
		{preference name=user_selector_realnames_messu}
		{preference name=messu_truncate_internal_message}

	</fieldset>

	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 text-center">
			<input type="submit" class="btn btn-default btn-sm" name="messagesprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</div>
</form>
