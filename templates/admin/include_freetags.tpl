<form class="form-horizontal" action="tiki-admin.php?page=freetags" method="post">
	{ticket}
	<div class="t_navbar margin-bottom-md">
		<a role="link" class="btn btn-link tips" href="tiki-browse_freetags.php" title="{tr}Tags listing{/tr}">
			{icon name="list"} {tr}Browse Tags{/tr}
		</a>
		{if $prefs.freetags_multilingual eq 'y'}
			<a role="button" class="btn btn-link tips" href="tiki-freetag_translate.php" title=":{tr}Translate tags{/tr}">
				{icon name="language"} {tr}Translate Tags{/tr}
			</a>
		{/if}
		<button role="button" type="submit" class="btn btn-default timeout" name="cleanup">
			{icon name="trash"} {tr}Cleanup unused tags{/tr}
		</button>
		{include file='admin/include_apply_top.tpl'}
	</div>
	<fieldset>
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=feature_freetags visible="always"}
	</fieldset>

	<fieldset class="table">
		<legend>{tr}Plugins{/tr}</legend>
		{preference name=wikiplugin_freetagged}
		{preference name=wikiplugin_addfreetag}
	</fieldset>

	<fieldset>
		<legend>{tr}Tags{/tr}{help url="Tags"}</legend>
		{preference name=freetags_browse_show_cloud}

		<div class="adminoptionboxchild" id="freetags_browse_show_cloud_childcontainer">
			{preference name=freetags_browse_amount_tags_in_cloud}
		</div>

		{preference name=freetags_show_middle}

		<div class="adminoptionbox">
			<label for="freetags_cloud_colors" class="control-label col-md-4">{tr}Random tag cloud colors:{/tr}</label>
			<div class="col-md-8">
				<input type="text" class="form-control" name="freetags_cloud_colors" id="freetags_cloud_colors" value="{foreach from=$prefs.freetags_cloud_colors item=color name=colors}{$color}{if !$smarty.foreach.colors.last},{/if}{/foreach}" />
				<span class="help-block">{tr}Separate colors with a comma (,){/tr}</span>
			</div>
		</div>

		{preference name=freetags_browse_amount_tags_suggestion}
		{preference name=freetags_normalized_valid_chars}
		<div class="clearfix">
			<span class="adminoptionbox help-block col-md-8 col-md-push-4">
				<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('a-zA-Z0-9');return false;">
					{tr}Alphanumeric ASCII characters only{/tr}
				</a>
				({tr}No accents or special characters{/tr}.)
				<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('');return false;">
					{tr}Accept all characters{/tr}
				</a>
			</span>
		</div>
		{preference name=freetags_lowercase_only}
		{preference name=freetags_multilingual}
		{preference name=morelikethis_algorithm}
		{preference name=morelikethis_basic_mincommon}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Tag search page{/tr}</legend>
		{preference name=freetags_sort_mode}
		{preference name=freetags_preload_random_search}
		<span class="help-block col-md-8 col-md-push-4">{tr}When arriving on <a href="tiki-browse_freetags.php">tag search page</a>{/tr}.</span>
	</fieldset>
	{include file='admin/include_apply_bottom.tpl'}
</form>
