<input id="{$field.ins_id|escape}-input" type="text" name="{$field.ins_id|escape}" value="{$field.value|escape}"/>
<div id="{$field.ins_id|escape}-drop" style="width: 200px; height: 200px; background: red;">{tr}Drop files here{/tr}</div>
<ol id="{$field.ins_id|escape}-files">
	{foreach from=$field.files item=info}
		<li>{$info.name|escape}</li>
	{/foreach}
</ol>
{jq}
var $drop = $('#{{$field.ins_id|escape}}-drop');
var $files = $('#{{$field.ins_id|escape}}-files');
var $field = $('#{{$field.ins_id|escape}}-input');

$drop.bind('dragenter', function (e) {
	e.preventDefault();
	e.stopPropagation();
	$drop.css('background', 'orange');
	return false;
});
$drop.bind('dragexit', function (e) {
	e.preventDefault();
	e.stopPropagation();
	$drop.css('background', 'red');
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
	$drop.css('background', 'red');

	var dataTransfer = e.dataTransfer;
	if (! dataTransfer) {
		dataTransfer = e.originalEvent.dataTransfer;
	}

	$.each(dataTransfer.files, function (k, file) {
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
						li.text(data.name);

						var values = $field.val().split(',');
						if (values[0] === '') {
							values.shift();
						}
						values.push(data.fileId);
						$field.val(values.join(','));
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
	return false;
});

{/jq}
