<div class="files-field uninitialized">
{if $field.limit}
	{remarksbox _type=info title="{tr}Attached files limitation{/tr}"}
		{tr _0=$field.limit}The amount of files that can be attached is limited to <strong>%0</strong>. The latest files will be preserved.{/tr}
	{/remarksbox}
{/if}
<ol class="tracker-item-files current-list">
	{foreach from=$field.files item=info}
		<li data-file-id="{$info.fileId|escape}">
			{$info.name|escape}
			<label>{icon _id=cross}</label>
		</li>
	{/foreach}
</ol>
<input class="input" type="text" name="{$field.ins_id|escape}" value="{$field.value|escape}"/>
{if $field.canUpload}
	<fieldset id="{$field.ins_id|escape}-drop" class="file-drop">
		<legend>{tr}Upload files{/tr}</legend>
		<p style="display:none;">{tr}Drop files from your desktop here or browse for them{/tr}</p>
		<input class="ignore" type="file" name="{$field.ins_id|escape}[]" accept="{$field.filter|escape}" multiple="multiple"/>
	</fieldset>
{/if}
{if $prefs.fgal_tracker_existing_search eq 'y'}
	<fieldset>
		<legend>{tr}Existing files{/tr}</legend>
		<input type="text" class="search" placeholder="{tr}Search query{/tr}"/>
		{if $prefs.fgal_elfinder_feature eq 'y'}
			{button href='tiki-list_file_gallery.php' _text="{tr}Browse files{/tr}"
				_onclick="return openElFinderDialog(this, {ldelim}defaultGalleryId:{if !isset($field.options_array[8]) or $field.options_array[8] eq ''}{if empty($field.options_array[0])}0{else}{$field.options_array[0]|escape}{/if}{else}{$field.options_array[8]|escape}{/if},deepGallerySearch:{if empty($field.options_array[6])}0{else}{$field.options_array[6]|escape}{/if},getFileCallback:function(file,elfinder){ldelim}window.handleFinderFile(file,elfinder){rdelim}{rdelim});"
				title="{tr}Browse files{/tr}"}
		{/if}
		<ol class="results tracker-item-files">
		</ol>
	</fieldset>
{/if}
{if $prefs.fgal_upload_from_source eq 'y' and $field.canUpload}
	<fieldset>
		<legend>{tr}Upload from URL{/tr}</legend>
		<label>{tr}URL:{/tr} <input class="url" name="url" placeholder="http://"/></label>
		{tr}Type or paste the URL and press ENTER{/tr}
	</fieldset>
{/if}
</div>
{jq}
$('.files-field.uninitialized').removeClass('uninitialized').each(function () {
var $drop = $('.file-drop', this);
var $files = $('.current-list', this);
var $field = $('.input', this);
var $search = $('.search', this);
var $url = $('.url', this);
var $fileinput = $drop.find('input');

$field.hide();

var handleFiles = function (files) {
	$fileinput.clearError();
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
					url: $.service('file', 'upload'),
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
									
						if ({{$field.firstfile|escape}} > 0) {
							li.prev('li').remove();
						}
					},
					error: function (jqxhr) {
						$fileinput.showError(jqxhr);
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
						fileId: {{$field.firstfile|escape}},
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

if (typeof FileReader !== 'undefined') {
	$drop.find('> p').show();
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
	$fileinput.change(function () {
		if (this.files) {
			handleFiles(this.files);
			$(this).val('');
		}
	});
}

$url.keypress(function (e) {
	if (e.which === 13) {
		var url = $(this).val();
		$(this).attr('disabled', true).clearError();

		$.ajax({
			type: 'POST',
			url: $.service('file', 'remote'),
			dataType: 'json',
			data: {
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
				$url.showError(jqxhr);
			},
			complete: function () {
				$url.removeAttr('disabled');
			}
		});

		return false;
	}
});

$search.keypress(function (e) {
	if (e.which === 13) {
		var results = $(this).parent().find('.results');
		$search.attr('disabled', true);
		results.empty();

		$.getJSON('tiki-searchindex.php', {
			"filter~type": "file",
			"filter~content": $(this).val(),
			"filter~filetype": "{{$field.filter|escape}}",
			"filter~gallery_id": "{{$field.gallerySearch|escape}}"
		}, function (data) {
			$search.removeAttr('disabled').clearError();
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
window.handleFinderFile = function (file, elfinder) {
	var m = file.match(/target=([^&]*)/);
	if (!m || m.length < 2) {
		return false;	// error?
	}
	$.ajax({
		type: 'GET',
		url: $.service('file_finder', 'finder'),
		dataType: 'json',
		data: {
			cmd: "tikiFileFromHash",
			hash: m[1]
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
		},
		error: function (jqxhr) {
		},
		complete: function () {
			$(window).data("elFinderDialog").dialog("close");
			$(window).data("elFinderDialog", null);
			return false;
		}
	});
};
});
{/jq}
