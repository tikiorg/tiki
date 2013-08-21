{if $errMsg}
	{remarksbox title='{tr}Vimeo Setup Error{/tr}' type='error'}
		<p>{$errMsg}</p>
	{/remarksbox}
	{$disabled=true}
{else}
	{remarksbox title='{tr}Vimeo Info{/tr}' type='info'}
		<p>{tr _0=$availableMB}Available space: %0 megabytes{/tr}</p>
		{if $availableSD eq '0'}<p>{tr}No standard definition uploads available currently{/tr}</p>{/if}
		{if $availableHD eq '0'}<p>{tr}No high definition uploads available currently{/tr}</p>{/if}
	{/remarksbox}
	{if $availableSD eq '0' and $availableHD eq '0'}
		{$disabled=true}
	{/if}
{/if}
<form class="simple no-ajax vimeo_upload" id="form{$ticket.id|escape}" target="vimeo{$ticket.id|escape}"
			method="post" action="{$ticket.endpoint|escape}" enctype="multipart/form-data"
			data-verify-action="{service controller=vimeo action=complete}">
	<label>
		{tr}Title{/tr}
		<input type="text" name="title" required{if $disabled} disabled="disabled"{/if}/>
	</label>
	<label>
		{tr}Video{/tr}
		<input type="file" name="file_data"{if $disabled} disabled="disabled"{/if}/>
	</label>
	<input type="hidden" name="ticket_id" value="{$ticket.id|escape}"/>
	<input type="hidden" name="chunk_id" value="0"/>
	<input type="submit" value="Upload"{if $disabled} disabled="disabled"{/if}/>
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

	$form.submit(function(){
		$(this).modal(tr("Uploading..."));
	});

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
				$form.modal();
			}, 'json');
		}
	});
{/jq}
