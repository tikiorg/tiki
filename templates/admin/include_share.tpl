{* $Id$ *}

<form class="form-horizontal" action="tiki-admin.php?page=share" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="sharesetprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_share visible="always"}
	</fieldset>

	<fieldset>
		<legend>{tr}Site-wide features{/tr}</legend>

		<div class="admin featurelist">
			{preference name=share_display_links}
			{preference name=share_token_notification}
			{preference name=share_contact_add_non_existant_contact}
			{preference name=share_display_name_and_email}
			{preference name=share_can_choose_how_much_time_access}
			<div class="adminoptionboxchild" id="share_can_choose_how_much_time_access_childcontainer">
				{remarksbox type="remark" title="{tr}Default{/tr}"}
					{tr}If you don't want to limit it, an input box will be displayed; otherwise, it will be checkbox{/tr}
				{/remarksbox}
				{preference name=share_max_access_time}
			</div>
		</div>
	</fieldset>


	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="sharesetprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

</form>
