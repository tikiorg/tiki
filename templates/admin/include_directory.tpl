<form class="form-horizontal" action="tiki-admin.php?page=directory" method="post">
	{ticket}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link tips" href="tiki-directory_admin.php" title=":{tr}Directories listing{/tr}">
				{icon name="list"} {tr}Directory{/tr}
			</a>
			{include file='admin/include_apply_top.tpl'}
		</div>
	</div>


	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_directory visible="always"}
	</fieldset>

	<fieldset>
		<legend>{tr}Directory{/tr}</legend>
		{preference name=directory_columns}
		{preference name=directory_links_per_page}
		{preference name=directory_validate_urls}
		{preference name=directory_cool_sites}
		{preference name=directory_country_flag}
		{preference name=directory_open_links}
	</fieldset>
	{include file='admin/include_apply_bottom.tpl'}
</form>
