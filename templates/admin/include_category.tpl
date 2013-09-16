{* $Id$ *}
<div class="navbar">
	{button href="tiki-browse_categories.php" _text="{tr}Browse categories{/tr}"}
	{button href="tiki-admin_categories.php" _text="{tr}Administer categories{/tr}"}
	{button href="tiki-edit_categories.php" _text="{tr}Organize Objects{/tr}" _title="{tr}Organize Objects{/tr}"}	
</div>
{if !empty($feedbacks) || !empty($assignWikiCategories)}
	{remarksbox type="feedback" title="{tr}Feedback{/tr}"}
		{tr}Ok{/tr}
	{/remarksbox}
{/if}

<form action="tiki-admin.php?page=category" method="post">
	<input type="hidden" name="categorysetup" />
	<div class="input_submit_container clear" style="text-align: right;">
		<input type="submit" class="btn btn-default" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_categories visible="always"}
	</fieldset>	

	<fieldset class="admin">
		<legend>{tr}Plugins{/tr}</legend>
		{preference name=wikiplugin_category}
		{preference name=wikiplugin_catpath}
		{preference name=wikiplugin_catorphans}
	</fieldset>
	
	<fieldset>
	
		<legend>
			{tr}Features{/tr}{help url="Category"}
		</legend>
		
		{preference name=feature_categorypath}
		<div class="adminoptionboxchild" id="feature_categorypath_childcontainer">
			{preference name=categorypath_excluded}
			{preference name=categorypath_format}
		</div>
		{preference name=category_sort_ascii}
		<fieldset>
			<legend>
				{tr}Category objects{/tr}
			</legend>
			{preference name=feature_categoryobjects}
			{preference name=category_morelikethis_algorithm}
			{preference name=category_morelikethis_mincommon}
			{preference name=category_morelikethis_mincommon_orless}
			{preference name=category_morelikethis_mincommon_max}
		</fieldset>

		{preference name=feature_category_transition}
		{preference name=categories_used_in_tpl}
		<div class="adminoptionboxchild" id="categories_used_in_tpl_childcontainer">
				{preference name=feature_areas}
				{preference name=areas_root}
		</div>
		{preference name=category_jail}
		{preference name=category_defaults}
		{if !empty($prefs.category_defaults)}{button href="tiki-admin.php?page=category&amp;assignWikiCategories=y" _text="{tr}Re-apply the last saved category defaults to the wiki pages{/tr}"}{/if}
		{preference name=category_autogeocode_within}
		{preference name=category_autogeocode_replace}
		{preference name=category_autogeocode_fudge}

		{preference name=category_i18n_sync}
		<div class="adminoptionboxchild category_i18n_sync_childcontainer blacklist whitelist required">
			{preference name=category_i18n_synced}
		</div>

		{preference name=feature_wiki_mandatory_category}
		{preference name=feature_blog_mandatory_category}
		{preference name=feature_image_gallery_mandatory_category}

		
	</fieldset>

	<fieldset>
		<legend>
			{tr}Plugins{/tr}{help url="Plugins"}
		</legend>
		{preference name=wikiplugin_category}
		{preference name=wikiplugin_catpath}
	</fieldset>

	<fieldset>
		<legend>{tr}Permissions{/tr}</legend>
		{preference name=feature_search_show_forbidden_cat}
	</fieldset>

	<div class="input_submit_container clear" style="text-align: center;">
		<input type="submit" class="btn btn-default" value="{tr}Change preferences{/tr}" />
	</div>
</form>
