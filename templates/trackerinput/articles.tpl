<div id="{$field.ins_id|escape}_container">
	<input type="hidden" name="{$field.ins_id|escape}[]" value="">
	<ul class="related_items">
		{foreach from=$data.labels item=label key=id}
			<li>{$label|escape}</li>
		{/foreach}
	</ul>
	{object_selector _class=selector _filter=$data.filter}
	{if $prefs.page_content_fetch eq 'y'}
		<button name="{$field.ins_id|escape}_add" class="add-more btn btn-default btn-sm" data-topic="{$field.options_map.topicId|escape}" data-type="{$field.options_map.type|escape}">{glyph name=plus} {tr}Add Article{/tr}</button>
	{/if}
</div>
{jq}
(function () {
	var container = $('#{{$field.ins_id}}_container')[0];
	var currents = [];

	var createItem = function (id, label, highlight) {
		if (!id) {
			return false;
		}

		var item = $('<li/>');
		item.text(label);

		item.prepend(
			$('<input type="hidden">')
				.attr('name', "{{$field.ins_id|escape}}[]")
				.val(id)
		);

		item.append(
			$('{{icon _id=cross}}')
				.css('cursor', 'pointer')
				.click(function () {
					$(this).closest('li').remove();
				})
		);

		$('ul.related_items li', container).removeClass('highlight');

		if (-1 === $.inArray(id, currents)) {
			currents.push(id);
			$('ul.related_items', container).append(item);
		} else if (highlight) {
			$('ul.related_items input', container)
				.filter(function () {
					return id === $(this).val();
				})
				.closest('li')
				.addClass('highlight');
		}
	};

	$('ul.related_items', container).empty();
	var labels = {{$data.labels|@json_encode}};
	$.each(labels, createItem);

	$('ul.related_items', container).sortList();
	$('.selector', container).change(function () {
		createItem($(this).val().substring("article:".length), $(this).data('label'), true);
		$('ul.related_items', container).sortList();
	});

	$('.add-more', container).click(function (e) {
		e.preventDefault();

		var url = prompt(tr('URL')), button = this;

		if (url) {
			$.ajax($.service('article', 'create_from_url'), {
				method: 'POST',
				dataType: 'json',
				success: function (data) {
					$(button).clearError();
					if (data.id) {
						createItem(data.id, data.articleTitle, true);
					}
				},
				error: function (jqxhr) {
					$(button).closest('form').showError(jqxhr);
				},
				data: {
					topicId: $(button).data('topic'),
					type: $(button).data('type'),
					errorfield: $(button).attr('name'),
					url: url
				}
			});
		}
	});
}());
{/jq}
