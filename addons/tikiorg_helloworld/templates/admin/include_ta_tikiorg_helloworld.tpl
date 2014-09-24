{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Welcome to the Tiki Sample Hello World Addon{/tr}{/remarksbox}
<form action="tiki-admin.php?page=ta_tikiorg_helloworld" method="post">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
	{tabset name="admin_ta_tikiorg_helloworld"}
	{tab name="{tr}Main features{/tr}"}
		{preference name=ta_tikiorg_helloworld_on}
		{preference name=ta_tikiorg_helloworld_boldtext}
	{/tab}
	{/tabset}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>