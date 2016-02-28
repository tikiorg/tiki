{* $Id$ *}
{if !empty($feedbacks) || !empty($assignWikiCategories)}
	{remarksbox type="feedback" title="{tr}Feedback{/tr}"}
		{tr}Ok{/tr}
	{/remarksbox}
{/if}

<form class="form-horizontal" action="tiki-admin.php?page=category" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="categorysetup" />
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="link" class="btn btn-link" href="tiki-browse_categories.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}Browse Categories{/tr}
			</a>
			<a role="link" class="btn btn-link" href="tiki-admin_categories.php" title="{tr}Administration{/tr}">
				{icon name="cog"} {tr}Administer Categories{/tr}
			</a>
			<a role="link" class="btn btn-link" href="tiki-edit_categories.php" title="{tr}Organize Objects{/tr}">
				{icon name="sort"} {tr}Organize Objects{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_categories visible="always"}
	</fieldset>

	<fieldset class="table">
		<legend>{tr}Plugins{/tr} {help url="Plugins"}</legend>
		{preference name=wikiplugin_category}
		{preference name=wikiplugin_catpath}
		{preference name=wikiplugin_catorphans}
	</fieldset>

	<fieldset>
		<legend>
			{tr}Features{/tr}
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
		{preference name=categories_add_class_to_body_tag}
		{preference name=categories_cache_refresh_on_object_cat}
		
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
		{preference name=unified_add_to_categ_search}


	</fieldset>

	<fieldset>
		<legend>{tr}Performance{/tr}</legend>
		{preference name=feature_search_show_forbidden_cat}
		{preference name=category_browse_count_objects}
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
