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
	<fieldset>
		<legend>{tr}Categories{/tr}</legend>

		<input type="text" name="filter[categories]" class="wizard" value="{$filter_categories|escape}"/>
		
		<label>
			<input type="checkbox" name="filter[deep]"{if $filter_deep} checked="checked"{/if}/>
			{tr}Deep search{/tr}
		</label>
	</fieldset>
</form>
{jq}
	$('.filter .wizard:not(.init)').addClass('init').fancy_filter('init', {
		map: {{$filter_categmap}}
	});
{/jq}
