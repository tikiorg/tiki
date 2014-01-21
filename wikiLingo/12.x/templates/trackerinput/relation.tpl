<div id="{$field.ins_id|escape}_container">
	<input type="hidden" name="{$field.ins_id|escape}[]" value="">
	<ul class="related_items">
		{foreach from=$data.labels item=label key=id}
			<li>{$label|escape}</li>
		{/foreach}
	</ul>
	<textarea name="{$field.ins_id|escape}">{$field.value|escape}</textarea>
	{object_selector _class=selector _filter=$data.filter}
</div>
{jq}
(function () {
	var inverts = {{$field.inverts|@json_encode}};
	var container = $('#{{$field.ins_id}}_container')[0];
	var currents = [];

	var createItem = function (id, label, highlight) {
		if (!id) {
			return false;
		}

		var item = $('<li/>');
		item.text(label);

		if (-1 === $.inArray(id, inverts)) {
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
		}
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
	$('textarea', container).remove();
	var labels = {{$data.labels|@json_encode}};
	$.each(labels, createItem);

	$('ul.related_items', container).sortList();
	$('.selector', container).change(function () {
		createItem($(this).val(), $(this).data('label'), true);
		$('ul.related_items', container).sortList();
	});
}());
{/jq}
