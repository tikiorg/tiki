{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To configure your directory, find "Admin Directory" under "Directory" on the application menu, or{/tr} <a class="rbox-link" href="tiki-directory_admin.php">{tr}Click Here{/tr}</a>.{/remarksbox}

<form action="tiki-admin.php?page=directory" method="post">
    <div class="row">
        <div class="form-group col-lg-12 clearfix">
            <div class="pull-right">
                <input type="submit" class="btn btn-default btn-sm" name="directory" value="{tr}Change preferences{/tr}">
            </div>
        </div>
    </div>


    <fieldset class="table">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_directory visible="always"}
	</fieldset>		

	<fieldset class="table">
		<legend>{tr}Directory{/tr}</legend>
		{preference name=directory_columns}
		{preference name=directory_links_per_page}
		{preference name=directory_validate_urls}
		{preference name=directory_cool_sites}
		{preference name=directory_country_flag}
		{preference name=directory_open_links}
	</fieldset>
    <br>{* I cheated. *}
    <div class="row">
        <div class="form-group col-lg-12 text-center">
            <input type="submit" class="btn btn-default btn-sm" name="directory" value="{tr}Change preferences{/tr}">
        </div>
    </div>
</form>
