<p>{tr _0=$available}Available space: %0 megabytes{/tr}</p>
<form class="simple no-ajax vimeo_upload" id="form{$ticket.id|escape}" target="vimeo{$ticket.id|escape}"
			method="post" action="{$ticket.endpoint|escape}" enctype="multipart/form-data"
			data-verify-action="{service controller=vimeo action=complete}">
	<label>
		{tr}Title{/tr}
		<input type="text" name="title" required/>
	</label>
	<label>
		{tr}Video{/tr}
		<input type="file" name="file_data"/>
	</label>
	<input type="hidden" name="ticket_id" value="{$ticket.id|escape}"/>
	<input type="hidden" name="chunk_id" value="0"/>
	<input type="submit" value="Upload"/>
</form>
<iframe style="display: none;" id="vimeo{$ticket.id|escape}" name="vimeo{$ticket.id|escape}" src="about:blank">
</iframe>
{jq}
	var ticket = {{$ticket.id|json_encode}};
	var galleryId = {{$galleryId|json_encode}};
	var fieldId = {{$fieldId|json_encode}};
	var itemId = {{$itemId|json_encode}};
	var $iframe = $('#vimeo' + ticket);
	var $form = $('#form' + ticket);
	var $file = $('input[type=file]', $form);
	var $title = $('input[name=title]', $form);

	$iframe.on('load', function () {
		var data = {
			title: $title.val(),
			galleryId: galleryId,
			ticket: ticket,
			file: $file.val(),
			fieldId: fieldId,
			itemId: itemId
		};
		if (data.file) {
			$.post($.service('vimeo', 'complete'), data, function(data) {
				$(".vimeo_upload").trigger("vimeo_uploaded", [data]);
			}, 'json');
		}
	});
{/jq}
