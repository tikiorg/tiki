<div id="{$field.ins_id|escape}_container">
	<ul>
		{foreach from=$context.labels item=label key=id}
			<li>{$label|escape}</li>
		{/foreach}
	</ul>
	<textarea name="{$field.ins_id|escape}">{$field.value|escape}</textarea>
	{object_selector _class=selector _filter=$context.filter}
</div>
{jq}
(function () {
	var container = $('#{{$field.ins_id}}_container')[0];
	var createItem = function (id, label) {
		var item = $('<li/>')
			.text(label);

		item.prepend(
			$('<input type="hidden"/>')
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

		$('ul', container).append(item);
	};

	$('ul', container).empty();
	$('textarea', container).remove();
	var labels = {{$context.labels|@json_encode}};
	$.each(labels, createItem);
	$('.selector', container).change(function () {
		createItem($(this).val(), $(this).data('label'));
	});
}());
{/jq}
