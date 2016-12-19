{* $Id$ *}
<form action="tiki-admin.php?page=ads" onreset="return(confirm("{tr}Cancel Edit{/tr}"))" class="admin form-horizontal" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link" href="tiki-list_banners.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}Banners{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
	<input type="hidden" name="adssetup">

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

		{preference name=sitead}
		{preference name=feature_sitead}
		<div class="adminoptionbox" id="feature_sitead_childcontainer">
			{remarksbox type="note" title="{tr}Note{/tr}"}
				{tr}<strong>Activate</strong> will display content for Admin only. Select <strong>Publish</strong> to display for all users.{/tr}
			{/remarksbox}
			{preference name=sitead_publish}
		</div>
	</fieldset>

	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
