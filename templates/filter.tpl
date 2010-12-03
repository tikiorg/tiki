<form method="get" action="{$filter_action|escape}" class="filter">
	<label>
		{tr}Content{/tr}
		<input type="text" name="filter[content]" value="{$filter_content|escape}"/>
	</label>
	<label>
		{tr}Type{/tr}
		<select name="filter[type]">
			<option value="">{tr}Any{/tr}</option>
			{foreach from=$filter_types key=k item=t}
				<option value="{$k|escape}"{if $t eq $filter_type} selected="selected"{/if}>{$t|escape}</option>
			{/foreach}
		</select>
	</label>
	<input type="submit" value="{tr}Search{/tr}"/>
	{if $prefs.feature_categories eq 'y'}
		<fieldset>
			<legend>{tr}Categories{/tr}</legend>

			<input type="text" name="filter[categories]" class="wizard" value="{$filter_categories|escape}"/>

			<a class="category-lookup" href="#">{tr}Lookup{/tr}</a>
			
			<label>
				<input type="checkbox" name="filter[deep]"{if $filter_deep} checked="checked"{/if}/>
				{tr}Deep search{/tr}
			</label>
		</fieldset>

		<div class="category-picker" title="{tr}Select Categories{/tr}">
			{$filter_category_picker}
		</div>
	{/if}
	{if $prefs.feature_multilingual eq 'y'}
		<fieldset>
			<legend>{tr}Language{/tr}</legend>
			<select name="filter[language]">
				<option value="">{tr}Any{/tr}</option>
				{foreach from=$filter_languages item=l}
					<option value="{$l.value|escape}"{if $filter_language eq $l.value} selected="selected"{/if}>{$l.name|escape}</option>
				{/foreach}
			</select>
			<label>
				<input type="checkbox" name="filter[language_unspecified]"{if $filter_language_unspecified} checked="checked"{/if}/>
				{tr}Include objects without a specified language{/tr}
			</label>
		</fieldset>
	{/if}
</form>
{jq}
	$('.filter:not(.init)').addClass('init').each(function () {

{{if $prefs.feature_categories eq 'y'}}
		var categoryInput = $('.wizard', this).fancy_filter('init', {
			map: {{$filter_categmap}}
		});

		var categoryPicker = $('.category-picker', this).dialog({
			autoOpen: false,
			modal: true,
			buttons: {
				"{tr}Add to filter{/tr}": function () {
					$(':checked', this).each(function () {
						categoryInput.fancy_filter('add', {
							token: $(this).val(),
							label: $(this).parent().text()
						});
					});
					$(this).dialog('close');
				},
				"{tr}Cancel{/tr}": function () {
					$(this).dialog('close');
				}
			},
			close: function () {
				$(':checked', this).attr('checked', false);
			}
		});

		$('.category-lookup', this).click(function () {
			categoryPicker.dialog('open');
			return false;
		});
{{/if}}
	});
{/jq}
