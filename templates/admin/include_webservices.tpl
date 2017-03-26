{* $ID:$ *}
<form method="post" action="tiki-admin.php?page=webservices" class="form-horizontal">
	{include file='access/include_ticket.tpl'}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link" href="tiki-admin_webservices.php" title="{tr}List{/tr}">
				{icon name="admin"} {tr}Webservices{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_webservices visible="always"}
	</fieldset>

	<fieldset>
		<legend>{tr}Options{/tr}</legend>
		{preference name=webservice_consume_defaultcache}
	</fieldset>


	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
