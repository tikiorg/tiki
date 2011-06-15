<ol class="tracker-item-files" id="{$field.ins_id|escape}-files">
	{foreach from=$field.files item=info}
		<li data-file-id="{$info.fileId|escape}">
			{$info.name|escape}
			<label>{icon _id=cross}</label>
		</li>
	{/foreach}
</ol>
<input id="{$field.ins_id|escape}-input" type="text" name="{$field.ins_id|escape}" value="{$field.value|escape}"/>
{if $field.canUpload}
	<fieldset id="{$field.ins_id|escape}-drop" class="file-drop">
		<legend>{tr}Upload files{/tr}</legend>
		{if $field.limit}
			{remarksbox _type=info title="{tr}Attached files limitation{/tr}"}
				{tr 0=$field.limit}The amount of files that can be attached is limited to <strong>%0</strong>. Additional files uploaded will still be uploaded to the server and searchable, but they will not be attached to this item. Make sure you remove the files no longer required before you save your changes.{/tr}
			{/remarksbox}
		{/if}
		<p>{tr}Drop files from your desktop here or browse for them{/tr}</p>
		<input class="ignore" type="file" name="{$field.ins_id|escape}[]" accept="{$field.filter|escape}" multiple="multiple"/>
	</fieldset>
{/if}
{if $prefs.fgal_tracker_existing_search eq 'y'}
	<fieldset>
		<legend>{tr}Existing files{/tr}</legend>
		<input type="text" id="{$field.ins_id|escape}-search" placeholder="{tr}Search query{/tr}"/>
		<ol class="results tracker-item-files">
		</ol>
	</fieldset>
{/if}
{if $prefs.fgal_upload_from_source eq 'y' and $field.canUpload}
	<fieldset>
		<legend>{tr}Upload from URL{/tr}</legend>
		<label>{tr}URL:{/tr} <input id="{$field.ins_id|escape}-url" type="url"/></label>
	</fieldset>
{/if}
{jq}
(function () {
var $drop = $('#{{$field.ins_id|escape}}-drop');
var $files = $('#{{$field.ins_id|escape}}-files');
var $field = $('#{{$field.ins_id|escape}}-input');
var $search = $('#{{$field.ins_id|escape}}-search');
var $url = $('#{{$field.ins_id|escape}}-url');

$field.hide();

var handleFiles = function (files) {
	$.each(files, function (k, file) {
		var reader = new FileReader();
		var li = $('<li/>').appendTo($files);

		li.text(file.name + ' (...)');

		$(window).queue('process-upload', function () {
			reader.onloadend = function (e) {
				var xhr, provider;

				xhr = jQuery.ajaxSettings.xhr();
				if (xhr.upload) {
					xhr.upload.addEventListener('progress', function (e) {
						li.text(file.name + ' (' + Math.round(e.position / e.total * 100) + '%)');
					}, false);
				}
				provider = function () {
					return xhr;
				};

				var data = e.target.result;
				data = data.substr(data.indexOf('base64') + 7);

				$.ajax({
					type: 'POST',
					url: 'tiki-ajax_services.php?controller=file&action=upload',
					xhr: provider,
					dataType: 'json',
					success: function (data) {
						var fileId = data.fileId;
						li.text(data.name);

						$field.input_csv('add', ',', fileId);

						li.append($('<label>{{icon _id=cross}}</label>'));
						li.find('img').click(function () {
							$field.input_csv('delete', ',', fileId);
							$(this).closest('li').remove();
						});
					},
					error: function () {
						li.remove();
					},
					complete: function () {
						$(window).dequeue('process-upload');
					},
					data: {
						name: file.name,
						size: file.size,
						type: file.type,
						data: data,
						galleryId: {{$field.galleryId|escape}}
					}
				});
			};
			reader.readAsDataURL(file);
		});
	});
	$(window).dequeue('process-upload');
};

$files.find('input').hide();
$files.find('img').click(function () {
	var fileId = $(this).closest('li').data('file-id');
	$field.input_csv('delete', ',', fileId);
	$(this).closest('li').remove();
});

$drop.bind('dragenter', function (e) {
	e.preventDefault();
	e.stopPropagation();
	$drop.addClass('highlight');
	return false;
});
$drop.bind('dragexit', function (e) {
	e.preventDefault();
	e.stopPropagation();
	$drop.removeClass('highlight');
	return false;
});
$drop.bind('dragover', function (e) {
	e.preventDefault();
	e.stopPropagation();
	return false;
});
$drop.bind('drop', function (e) {
	e.preventDefault();
	e.stopPropagation();
	$drop.removeClass('highlight');

	var dataTransfer = e.dataTransfer;
	if (! dataTransfer) {
		dataTransfer = e.originalEvent.dataTransfer;
	}

	if (dataTransfer && dataTransfer.files) {
		handleFiles(dataTransfer.files);
	}
	return false;
});

$drop.find('input').change(function () {
	if (this.files) {
		handleFiles(this.files);
		$(this).val('');
	}
});

$url.keypress(function (e) {
	if (e.which === 13) {
		var url = $(this).val();
		$(this).attr('disabled', 1).clearError();

		$.ajax({
			type: 'POST',
			url: 'tiki-ajax_services.php',
			dataType: 'json',
			data: {
				controller: 'file',
				action: 'remote',
				galleryId: "{{$field.galleryId|escape}}",
				url: url
			},
			success: function (data) {
				var fileId = data.fileId, li = $('<li/>');
				li.text(data.name);

				$field.input_csv('add', ',', fileId);

				li.append($('<label>{{icon _id=cross}}</label>'));
				li.find('img').click(function () {
					$field.input_csv('delete', ',', fileId);
					$(this).closest('li').remove();
				});
				$files.append(li);
				$url.val('');
			},
			error: function (jqxhr) {
				var data = $.parseJSON(jqxhr.responseText);
				$url.showError(data.message);
			},
			complete: function () {
				$url.attr('disabled', 0);
			}
		});

		return false;
	}
});

$search.keypress(function (e) {
	if (e.which === 13) {
		var results = $(this).parent().find('.results');
		$search.attr('disabled', 1);
		results.empty();

		$.getJSON('tiki-searchindex.php', {
			"filter~type": "file",
			"filter~content": $(this).val(),
			"filter~filetype": "{{$field.filter|escape}}",
			"filter~gallery_id": "{{$field.galleryId|escape}}",
		}, function (data) {
			$search.attr('disabled', 0).clearError();
			$.each(data, function () {
				var item = $('<li/>').append(this.link), icon = $('<label>{{icon _id=add}}</label>'), data = this;
				item.append(icon);
				icon.click(function () {
					var li = $('<li/>');
					li.text(item.text());
					li.append($('<label>{{icon _id=cross}}</label>'));
					li.find('img').click(function () {
						$field.input_csv('delete', ',', data.object_id);
						$(this).closest('li').remove();
					});

					$files.append(li);
					$field.input_csv('add', ',', data.object_id);
				});

				results.append(item);
			});

			if (results.is(':empty')) {
				$search.showError(tr('No results'));
			}
		});
		return false;
	}
});
}());
{/jq}
