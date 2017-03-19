{* $Id$ *}
<form action="tiki-admin.php?page=ads" onreset="return(confirm("{tr}Cancel Edit{/tr}"))" class="admin form-horizontal" method="post">
	{include file='access/include_ticket.tpl'}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link tips" href="tiki-list_banners.php" title=":{tr}Banners listing{/tr}">
				{icon name="list"} {tr}Banners{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
			</div>
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
		<legend>{tr}Site Ads and Banners{/tr}{help url="Banners"}</legend>

		{preference name=sitead_publish}
		{preference name=feature_sitead}
		<div class="adminoptionbox" id="feature_sitead_childcontainer">
			{preference name=sitead_publish}
		</div>
	</fieldset>

	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
