{* $Id$ *}
<form method="get" action="{$filter_action|escape}" class="filter form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label">{tr}Content{/tr}</label>
		<div class="col-sm-4">
			<input type="search" name="filter~content" class="form-control" value="{$filter_content|escape}">
		</div>
	</div>
	{if $prefs.search_show_sort_order eq 'y'}
		<div class="form-group">
			<label class="col-sm-2 control-label">{tr}Sort By{/tr}</label>
			<div class="col-sm-3">
				<select name="sort_mode" class="sort_mode form-control">
					{$sort_found = false}
					{foreach from=$sort_modes key=k item=t}
						<option value="{$k|escape}"{if $k eq $sort_mode} selected="selected"{$sort_found = true}{/if}>{$t|escape}</option>
					{/foreach}
				</select>
			</div>
			{if preg_match('/desc$/',$sort_mode)}
				{icon name='sort-down' class='sort_invert' title="{tr}Sort direction{/tr}" href='#'}
			{else}
				{icon name='sort-up' class='sort_invert' title="{tr}Sort direction{/tr}" href='#'}
			{/if}
		</div>
	{else}
		<input type="hidden" name="sort_mode" value="{$sort_mode}">
		{/if}
		{if $prefs.feature_search_show_object_filter eq 'y'}
			<div class="form-group">
				<label class="col-sm-2 control-label" for="filter-type">{tr}Type{/tr}</label>
				<div class="col-sm-4">
					<select name="filter~type" id="filter-type" class="form-control">
						<option value="">{tr}Any{/tr}</option>
						{foreach from=$filter_types key=k item=t}
							<option value="{$k|escape}"{if $k eq $filter_type} selected="selected"{/if}>{$t|escape}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{else}
			{if is_array($filter_type)}
				{foreach from=$filter_type item=t}
					<input type="hidden" name="filter~type[]" value="{$t|escape}">
				{/foreach}
			{else}
				<input type="hidden" name="filter~type" value="{$filter_type|escape}">
			{/if}
		{/if}

		{if $prefs.feature_categories eq 'y' and $tiki_p_view_category eq 'y' and $prefs.search_show_category_filter eq 'y'}
			<div class="form-group">
				<label class="col-sm-2 control-label" for="filter-categories">{tr}Categories{/tr}</label>
				<div class="col-sm-4">
					<a class="category-lookup btn btn-default margin-bottom-sm" href="#">{tr}Lookup{/tr}</a>
					<input type="text" name="filter~categories" id="filter-categories" class="category-wizard form-control" value="{$filter_categories|escape}">
				</div>
				<div class="col-sm-5 checkbox">
					<label for="filter-deep">
						<input type="checkbox" name="filter~deep" id="filter-deep" {if $filter_deep} checked="checked"{/if}> {tr}Deep search{/tr}
					</label>
				</div>
			</div>

			<div class="category-picker" title="{tr}Select Categories{/tr}" style="display:none;">
				{$filter_category_picker}
			</div>
		{/if}
		{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and $prefs.search_show_tag_filter eq 'y'}
			<div class="form-group">
				<label class="col-sm-2 control-label" for="filter-tags">{tr}Tags{/tr}</label>
				<div class="col-sm-4">
					<a class="tag-lookup btn btn-default" href="#">{tr}Lookup{/tr}</a>
					<input type="text" name="filter~tags" class="tag-wizard" id="filter-tags" value="{$filter_tags|escape}">
				</div>
				<div class="tag-picker" title="{tr}Select Tags{/tr}" style="display:none;">
					{$filter_tags_picker}
				</div>
			</div>
		{/if}
		{if isset($filter.tracker_id)}
			<div class="form-group">
				<label class="col-sm-2 control-label" for="filter-tracker_id">{tr}Tracker{/tr}</label>
				<div class="col-sm-4">
					{object_selector type=tracker _simplevalue=$filter.tracker_id _simplename="filter~tracker_id" _simpleid="filter-tracker_id"}
				</div>
			</div>
		{/if}
		{if $prefs.feature_multilingual eq 'y'}
			{if $prefs.search_default_interface_language neq 'y'}
				<div class="form-group">
					<label class="col-sm-2 control-label" for="filter-language">{tr}Language{/tr}</label>
					<div class="col-sm-4">
						<select name="filter~language" class="form-control" id="filter-language">
							<option value="">{tr}Any{/tr}</option>
							{foreach from=$filter_languages item=l}
								<option value="{$l.value|escape}"{if $filter_language eq $l.value} selected="selected"{/if}>{$l.name|escape}</option>
							{/foreach}
						</select>
					</div>
					<div class="col-sm-5">
						<label for="filter-language-unspecified-checkbox" class="checkbox-inline">
							<input type="checkbox" id="filter-language-unspecified-checkbox" name="filter~language_unspecified"{if $filter_language_unspecified} checked="checked"{/if}>
							{tr}Include objects without a specified language{/tr}
						</label>
					</div>
				</div>
			{else}
				<input type="hidden" name="filter~language" value="{$prefs.language}">
				<input type="hidden" name="filter~language_unspecified" value="1">
			{/if}
		{/if}

	<div class="text-center">
		<input type="submit" class="btn btn-primary" value="{tr}Search{/tr}">
		{if $prefs.tracker_tabular_enabled eq 'y' && ! empty($smarty.get.tabularId)}
			<input type="hidden" name="tabularId" value="{$smarty.get.tabularId|escape}">
			<button class="tabular-export btn btn-default">
				{icon name=export} {tr}Export{/tr}
			</button>
			{jq}
				$(document).on('click', '.tabular-export', function (e) {
					var href = $.service('tabular', 'export_search_csv', {
						tabularId: "{{$smarty.get.tabularId}}"
					});
					e.preventDefault();
					document.location.href = href + '&' + $(this).closest('form').serialize();
				});
			{/jq}
		{elseif $prefs.tracker_tabular_enabled eq 'y' && ! empty($filter.tracker_id)}
			<button class="tabular-export btn btn-default">
				{icon name=export} {tr}Export{/tr}
			</button>
			{jq}
				$(document).on('click', '.tabular-export', function (e) {
					var href = $.service('tabular', 'export_search_csv', {
						trackerId: "{{$filter.tracker_id}}"
					});
					e.preventDefault();
					$.openModal({
						remote: href + '&' + $(this).closest('form').serialize()
					});
				});
			{/jq}
		{/if}
		{if $prefs.storedsearch_enabled eq 'y' and $user}
			<input type="hidden" name="storeAs" value=""/>
			<a href="{service controller=search_stored action=select modal=true}" id="store-query" class="btn btn-default">{tr}Save Search{/tr}</a>
			<a href="{service controller=search_stored action=list}" class="btn btn-link">{tr}View Saved Searches{/tr}</a>
			{jq}
				$('#store-query').clickModal({
					success: function (data) {
						var form = $(this).closest('form')[0];

						$(form.storeAs).val(data.queryId);
						$(form).attr('method', 'post');
						$(form).submit();
					}
				});
			{/jq}
		{/if}
		<a href="{bootstrap_modal controller=search action=help}">{tr}Search Help{/tr} {icon name='help'}</a>
	</div>
</form>
{jq}
	$('.filter:not(.init)').addClass('init').each(function () {

{{if $prefs.feature_categories eq 'y'}}
		var categoryInput = $('.category-wizard', this).fancy_filter('init', {
			map: {{$filter_categmap|json_encode}}
		});

		var categoryPicker = $('.category-picker', this).dialog({
			maxHeight: $(window).height()-50,
			width: 'auto',
			autoOpen: false,
			modal: true,
			buttons: {
				"{tr}Add to filter{/tr}": function () {
					$(':checked', this).each(function () {
						categoryInput.fancy_filter('add', {
							token: $(this).val(),
							label: $(this).parent().text(),
							join: ' or '
						});
					});
					$(this).dialog('close');
				},
				"{tr}Cancel{/tr}": function () {
					$(this).dialog('close');
				}
			},
			close: function () {
				$(':checked', this).prop('checked', false);
			}
		});

		$('.category-lookup', this).click(function () {
			categoryPicker.dialog('open');
			return false;
		});
{{/if}}

{{if $prefs.feature_freetags eq 'y'}}
		var tagInput = $('.tag-wizard', this).fancy_filter('init', {
			map: {{$filter_tagmap}}
		});

		$('.tag-picker a', this).click(function () {
			$(this).toggleClass('highlight');

			return false;
		});
		var tagPicker = $('.tag-picker', this).dialog({
			autoOpen: false,
			modal: true,
			maxHeight: $window.height() - 50,
			buttons: {
				"{tr}Add to filter{/tr}": function () {
					$('.highlight', this).each(function () {
						tagInput.fancy_filter('add', {
							token: $(this).attr('href'),
							label: $(this).text(),
							join: ' and '
						});
					});
					$(this).dialog('close');
				},
				"{tr}Cancel{/tr}": function () {
					$(this).dialog('close');
				}
			},
			close: function () {
				$(':checked', this).prop('checked', false);
			}
		});

		$('.tag-lookup', this).click(function () {
			tagPicker.dialog('open');
			return false;
		});
{{/if}}

{{if $prefs.search_show_sort_order eq 'y'}}
		var $invert = $(".sort_invert", this);
		var $sort_mode = $(".sort_mode", this);
{{if not $sort_found}}
		var opts = $sort_mode.prop("options");
		for (var o = 0; o < opts.length; o++) {	// sort_mode not in intially rendered list, so try and find the opposite direction
			var tofind = "{{$sort_mode}}";
			tofind = tofind.replace(/(:?asc|desc)$/, "");
			if (opts[o].value.search(tofind) === 0) {
				opts[o].value = "{{$sort_mode}}";
				$sort_mode.prop("selectedIndex", o).trigger("chosen:updated");
				break;
			}
		}
{{/if}}

		$sort_mode.change(function () {	// update direction arrow
			$(".icon", $invert).setIcon($(this).val().search(/desc$/) > -1 ? "sort-down" : "sort-up");
		});

		$invert.click(function () {	// change the value of the option to opposite direction
			var v = $sort_mode.prop("options")[$sort_mode.prop("selectedIndex")].value;
			if (v.search(/desc$/) > -1) {
				$sort_mode.prop("options")[$sort_mode.prop("selectedIndex")].value = v.replace(/desc$/, "asc");
				$(".icon", $invert).setIcon("sort-up");
			} else {
				$sort_mode.prop("options")[$sort_mode.prop("selectedIndex")].value = v.replace(/asc$/, "desc");
				$(".icon", $invert).setIcon("sort-down");
			}
			$(this).parents("form").submit();
			return false;
		});
{{/if}}

	});
{/jq}
