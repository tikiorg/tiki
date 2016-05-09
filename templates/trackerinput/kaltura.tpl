<ol>
	{foreach from=$data.movies item=movie}
		<li>
			<label>
				<input type="checkbox" name="{$field.ins_id|escape}[]" value="{$movie.id|escape}" checked="checked">
				{$movie.name|escape}
			</label>
		</li>
	{/foreach}
</ol>
<a class="add-kaltura-media btn btn-default btn-sm" href="{service controller=kaltura action=upload}" data-target-name="{$field.ins_id|escape}[]">{tr}Add Media{/tr}</a>
{foreach from=$data.extras item=entryId}
	<input type="hidden" name="{$field.ins_id|escape}[]" value="{$entryId|escape}">
{/foreach}
{if $data.extras|count}
	<span class="highlight">+{$data.extras|count}</span>
{/if}
{jq}
$('.add-kaltura-media').click(function () {
	var link = this;
	$("#bootstrap-modal").hide();
	$(this).serviceDialog({
		title: $(link).text(),
		width: 710,
		height: 450,
		hideButtons: true,
		success: function (data) {
			$("#bootstrap-modal").show();
			$.each(data.entries, function (k, entry) {
				var hidden = $('<input type="hidden">')
					.attr('name', $(link).data('target-name'))
					.attr('value', entry)
					;
				$(link).parent().append(hidden);
			});
			$(link).parent().find('span').remove();
			$(link).parent().append($('<span class="highlight"/>')
				.text('+' + $(this).parent().find('input').size()));

		},
		close: function () {
			$("#bootstrap-modal").show();
		}
	});
	return false;
});
{/jq}
