<div id="{$field.ins_id|escape}_container">
	<input type="hidden" name="{$field.ins_id|escape}[]" value="">
	{if $data.readonly}
		<ul>
			{foreach from=$data.labels item=label key=id}
				<input type="hidden" name="{$field.ins_id|escape}[]" value={$id}>
				<li>{object_link type="article" id=$id} <i class="fa fa-info-circle" data-toggle="tooltip" title="You cannot edit this article as it was generated automatically via the rss feed."></i></li>
			{/foreach}
		</ul>
	{else}
		<ul class="related_items">
			{foreach from=$data.labels item=label key=id}
				<li>{$label|escape}</li>
			{/foreach}
		</ul>
		<h5>{tr}Existing Article{/tr}</h5>
		{object_selector _class=selector _filter=$data.filter}
		{if $prefs.page_content_fetch eq 'y'}
			<h5>{tr}New Article{/tr}</h5>
			<div class="form-inline">
				<input class="form-control" type="url" name="{$field.ins_id|escape}_add" placeholder="{tr}Article URL{/tr}" size="50">
				<button name="{$field.ins_id|escape}_add" class="add-more btn btn-default" data-topic="{$field.options_map.topicId|escape}" data-type="{$field.options_map.type|escape}">{icon name="add"} {tr}Add Article{/tr}</button>
			</div>
		{/if}
	{/if}
</div>
{jq}
	$('[data-toggle="tooltip"]').tooltip();

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
				$('{{icon name='delete'}}')
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

		var urlField = $('input[type=url]', container), add = $('.add-more', container);
		urlField.keypress(function (e) {
			if (e.which == 13) {
				e.preventDefault();
				add.click();
			}
		});

		add.click(function (e) {
			e.preventDefault();

			var url = urlField.val(), button = this;

			if (url) {
				$.ajax($.service('article', 'create_from_url'), {
					method: 'POST',
					dataType: 'json',
					success: function (data) {
						$(button).clearError();
						if (data.id) {
							createItem(data.id, data.articleTitle, true);
							urlField.val('');
						}
					},
					error: function (jqxhr) {
						$(button).closest('form').showError(jqxhr);
					},
					data: {
						topicId: $(button).data('topic'),
						type: $(button).data('type'),
						errorfield: urlField.attr('name'),
						url: url
					}
				});
			}
		});
	}());
{/jq}
