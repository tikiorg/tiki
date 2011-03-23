{tikimodule error=$module_params.error title=$tpl_module_title name=$module_params.name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form class="mod_quick_search" method="get" action="tiki-searchindex.php">
	<label>{tr}Search Terms{/tr} <input type="text" name="filter~content" value="{$prefill.content|escape}"/></label>
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
{{if $prefill.trigger}}
	.submit()
{{/if}}
;
{/jq}
