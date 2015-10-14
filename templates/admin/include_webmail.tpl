<form class="form-horizontal" action="tiki-admin.php?page=webmail" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="button" class="btn btn-link" href="tiki-webmail.php" title="{tr}Webmail{/tr}">
				{icon name="inbox"} {tr}Webmail{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="webmail" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_webmail visible="always"}
	</fieldset>


	<fieldset>
		<legend>{tr}Settings{/tr}</legend>
		{preference name=webmail_view_html}
		{preference name=webmail_max_attachment}
		{preference name=webmail_quick_flags}
	</fieldset>
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="webmail" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>
