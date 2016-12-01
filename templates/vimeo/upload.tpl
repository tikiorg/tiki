{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
{if $errMsg}
	{remarksbox title="{tr}Vimeo Setup Error{/tr}" type='error'}
		<p>{$errMsg}</p>
	{/remarksbox}
	{$disabled=true}
{else}
	{remarksbox title="{tr}Vimeo Info{/tr}" type='info'}
		<p>{tr _0=$availableMB}Available space: %0 megabytes{/tr}</p>
		{if $availableSD eq '0'}<p>{tr}No standard definition uploads available currently{/tr}</p>{/if}
		{if $availableHD eq '0'}<p>{tr}No high definition uploads available currently{/tr}</p>{/if}
	{/remarksbox}
	{if $availableSD eq '0' and $availableHD eq '0'}
		{$disabled=true}
	{/if}
{/if}
<form class="simple no-ajax vimeo_upload" id="form{$ticket.id|escape}" target="vimeo{$ticket.id|escape}"
			method="post" action="{$ticket.endpoint_secure|escape}" enctype="multipart/form-data"
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
	<input type="submit" class="btn btn-default btn-sm" value="Upload"{if $disabled} disabled="disabled"{/if}/>
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
		$(this).parents(".ui-dialog").tikiModal(tr("Uploading..."));
		return true;
	});

	$iframe.on('load', function () {
		if ($file.val()) {
			var updata = {
				title: $title.val(),
				galleryId: galleryId,
				ticket: ticket,
				file: $file.val(),
				fieldId: fieldId,
				itemId: itemId
			};
			if (updata.file.indexOf("C:\\fakepath\\") === 0) {
				updata.file = updata.file.substr(12);	// webkit fakepath?
			}
			$file.val("");	// empty file value so it doesn't get added twice (mainly in webkit)
			$.post($.service('vimeo', 'complete'), updata, function(data) {
				$form.parents(".ui-dialog").tikiModal();
				if (data.err) {
					alert("Upload Error:\n" + data.err);
				} else {
					$(".vimeo_upload").trigger("vimeo_uploaded", [data]);
				}
			}, 'json')
			.error(function (e) {
				alert(tr("An error occurred uploading your video.") + "\n" + e.statusText + " (" + e.status + ")");
				$form.parents(".ui-dialog").tikiModal();
				$(".vimeo_upload").trigger("vimeo_uploaded", [{}]);	// get vimeo_uploaded to close the dialog
			});
		} else {
			$form.parents(".ui-dialog").tikiModal();
		}
	});
{/jq}
{/block}
