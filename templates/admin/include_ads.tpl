{* $Id$ *}
<form action="tiki-admin.php?page=ads" onreset="return(confirm("{tr}Cancel Edit{/tr}"))" class="admin form-horizontal" method="post">
	{include file='access/include_ticket.tpl'}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link tips" href="tiki-list_banners.php" title=":{tr}Banners listing{/tr}">
				{icon name="list"} {tr}Banners{/tr}
			</a>
			{include file='admin/include_apply_top.tpl'}
		</div>
	</div>

	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_banners visible="always"}
	</fieldset>

	<fieldset class="table">
		<legend>{tr}Plugins{/tr}</legend>
		{preference name=wikiplugin_banner}
	</fieldset>

	<fieldset>
		<legend>{tr}Site ads and banners{/tr}{help url="Banners"}</legend>

		{preference name=sitead_publish}
		{preference name=feature_sitead}
		<div class="adminoptionbox" id="feature_sitead_childcontainer">
			{preference name=sitead_publish}
		</div>
	</fieldset>
	{include file='admin/include_apply_bottom.tpl'}
</form>
