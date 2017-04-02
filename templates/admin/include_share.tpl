{* $Id$ *}

<form class="form-horizontal" action="tiki-admin.php?page=share" method="post">
	{include file='access/include_ticket.tpl'}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{include file='admin/include_apply_top.tpl'}
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
	{include file='admin/include_apply_bottom.tpl'}
</form>
