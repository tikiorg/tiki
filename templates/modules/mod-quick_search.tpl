{tikimodule error=$module_params.error title=$tpl_module_title name=$module_params.name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form class="mod_quick_search" method="get" action="tiki-searchindex.php">
	<label>{tr}Search Terms{/tr} <input type="text" name="filter~content" value="{$qs_prefill.content|escape}"/></label>

	{if $qs_types}
		<label>{tr}Type{/tr}
			<select name="filter~type">
				<option value="">{tr}Any type{/tr}</option>
				{foreach from=$qs_types item=label key=val}
					<option value="{$val|escape}"{if $qs_prefill.type eq $val} selected="selected"{/if}>{$label|escape}</option>
				{/foreach}
			</select>
		</label>
	{elseif $qs_prefill}
		<input type="hidden" name="filter~type" value="{$qs_prefill.type|escape}"/>
	{/if}

	<div class="submit">
		<input type="submit" value="{tr}Search{/tr}"/>
		<input type="hidden" name="save_query" value="{$moduleId|escape}"/>
	</div>
	<div class="results">
	</div>
</form>
{/tikimodule}
{jq}
$('.mod_quick_search:not(.done)').addClass('done').submit(function () {
	var query = $(this).serialize();
	var results = $('.results', this).empty();

	$.getJSON($(this).attr('action'), query, function (data) {
		var ol = $('<ol/>');
		results.append(ol);

		$.each(data, function (k, item) {
			ol.append($('<li/>').append($(item.link)));
		});
	});

	return false;
})
{{if $qs_prefill.trigger}}
	.submit()
{{/if}}
;
{/jq}
