<p>{tr _0=$available/1024/1024}Available space: %0 megabytes{/tr}</p>
<form class="simple" id="form{$ticket.id|escape}" target="vimeo{$ticket.id|escape}" method="post" action="{$ticket.endpoint|escape}" enctype="multipart/form-data" data-verify-action="{service controller=vimeo action=complete}">
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
	var $iframe = $('#vimeo' + ticket);
	var $form = $('#form' + ticket);
	var $file = $('#form' + ticket + ' input[type=file]');
	var $title = $('#form' + ticket + ' input[name=title]');
	
	$iframe.on('load', function () {
		var data = {
			title: $title.val(),
			galleryId: galleryId,
			ticket: ticket,
			file: $file.val()
		};
		$.post($.service('vimeo', 'complete'), data, function (data) {
		}, 'json')
	});
{/jq}
