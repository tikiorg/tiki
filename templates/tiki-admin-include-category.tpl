{* $Id$ *}
<div class="navbar">
	{button href="tiki-browse_categories.php" _text="{tr}Browse categories{/tr}"}
	{button href="tiki-admin_categories.php" _text="{tr}Administer categories{/tr}"}
</div>

<form action="tiki-admin.php?page=category" method="post">
	<input type="hidden" name="categorysetup" />
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset>
		<legend>
			{tr}Features{/tr}{if $prefs.feature_help eq 'y'} {help url="Category"}{/if}
		</legend>
		{preference name=feature_categorypath}
		<div class="adminoptionboxchild" id="feature_categorypath_childcontainer">
			{preference name=categorypath_excluded}
			<br />
			<em>{tr}Separate category IDs with a comma (,){/tr}.</em>
		</div>
		{preference name=feature_categoryobjects}
		{preference name=feature_category_use_phplayers}
		{preference name=categories_used_in_tpl}
	</fieldset>

	<fieldset>
		<legend>{tr}Permissions{/tr}</legend>
		{preference name=feature_search_show_forbidden_cat}
		<br />
		<em>{tr}Will improve performance, but may show forbidden results{/tr}.</em>
		{preference name=feature_category_reinforce}
	</fieldset>

	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
