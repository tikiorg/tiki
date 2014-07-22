<div class="files-field uninitialized {if $data.replaceFile}replace{/if}" data-galleryid="{$field.galleryId|escape}" data-firstfile="{$field.firstfile|escape}" data-filter="{$field.filter|escape}">
{if $field.limit}
	{remarksbox _type=info title="{tr}Attached files limitation{/tr}"}
		{tr _0=$field.limit}The amount of files that can be attached is limited to <strong>%0</strong>. The latest files will be preserved.{/tr}
	{/remarksbox}
{/if}
<ol class="tracker-item-files current-list">
	{foreach from=$field.files item=info}
		<li data-file-id="{$info.fileId|escape}">
			{if $prefs.vimeo_upload eq 'y' and $field.options_map.displayMode eq 'vimeo'}
				<img src="img/icons/vimeo.png" width="16" height="16">
			{elseif $field.options_map.displayMode eq 'img'}
				<img src="tiki-download_file.php?fileId={$info.fileId|escape}&display&y=24" height="24">
			{else}
				<img src="tiki-download_file.php?fileId={$info.fileId|escape}&icon" width="32" height="32">
			{/if}
			{$info.name|escape}
			<label>
				{icon _id=cross alt="{tr}Remove{/tr}"}
			</label>
		</li>
	{/foreach}
</ol>
<input class="input" type="text" name="{$field.ins_id|escape}" value="{$field.value|escape}">
{if $field.canUpload}
	{if $field.options_map.displayMode eq 'vimeo'}
		<fieldset>
			<legend>{tr}Upload files{/tr}</legend>
			{wikiplugin _name='vimeo' fromFieldId=$field.fieldId|escape fromItemId=$item.itemId|escape galleryId=$field.galleryId|escape}{/wikiplugin}
		</fieldset>
	{else}
		<fieldset id="{$field.ins_id|escape}-drop" class="file-drop">
			<legend>{tr}Upload files{/tr}</legend>
			<p style="display:none;">{tr}Drop files from your desktop here or browse for them{/tr}</p>
			<input class="ignore" type="file" name="{$field.ins_id|escape}[]" accept="{$field.filter|escape}" multiple="multiple">
		</fieldset>
	{/if}
{/if}
{if $prefs.fgal_tracker_existing_search eq 'y'}
	<fieldset>
		<legend>{tr}Existing files{/tr}</legend>
		<input type="text" class="search" placeholder="{tr}Search query{/tr}">
		{if $prefs.fgal_elfinder_feature eq 'y'}
			{button href='tiki-list_file_gallery.php' _text="{tr}Browse files{/tr}"
				_onclick="return openElFinderDialog(this, {ldelim}defaultGalleryId:{if !isset($field.options_array[8]) or $field.options_array[8] eq ''}{if empty($field.options_array[0])}0{else}{$field.options_array[0]|escape}{/if}{else}{$field.options_array[8]|escape}{/if},deepGallerySearch:{if empty($field.options_array[6])}0{else}{$field.options_array[6]|escape}{/if},getFileCallback:function(file,elfinder){ldelim}window.handleFinderFile(file,elfinder){rdelim},eventOrigin:this{rdelim});"
				title="{tr}Browse files{/tr}"}
		{/if}
		<ol class="results tracker-item-files">
		</ol>
	</fieldset>
{/if}
{if $prefs.fgal_upload_from_source eq 'y' and $field.canUpload}
	<fieldset>
		{if $prefs.vimeo_upload eq 'y' and $field.options_map.displayMode eq 'vimeo'}
			<legend>{tr}Link to existing Vimeo URL{/tr}</legend>
			<label>
				{tr}URL:{/tr} <input class="url vimeourl" name="vimeourl" placeholder="http://vimeo.com/..." data-mode="vimeo">
				<input type="hidden" class="reference" name="reference" value="1">
			</label>
		{else}
			<legend>{tr}Upload from URL{/tr}</legend>
			<label>
				{tr}URL:{/tr} <input class="url" name="url" placeholder="http://">
				<input type="hidden" class="reference" name="reference" value="0">
			</label>
		{/if}
		{tr}Type or paste the URL and press ENTER{/tr}
	</fieldset>
{/if}
</div>
{jq}
$('.files-field.uninitialized').removeClass('uninitialized').each(function () {
var $self = $(this);
var $drop = $('.file-drop', this);
var $files = $('.current-list', this);
var $field = $('.input', this);
var $search = $('.search', this);
var $url = $('.url', this);
var $fileinput = $drop.find('input');
var replaceFile = $(this).is('.replace');

$field.hide();

var handleFiles = function (files) {
	$fileinput.clearError();
	var uploadUrl = $.service('file', 'upload');
	$.each(files, function (k, file) {
		var reader = new FileReader();
		var li = $('<li/>').appendTo($files);

		li.text(file.name + ' (...)');

		$(window).queue('process-upload', function () {
			reader.onloadend = function (e) {
				var xhr, provider, sendData, data;

				xhr = jQuery.ajaxSettings.xhr();
				if (xhr.upload) {
					xhr.upload.addEventListener('progress', function (e) {
						if (e.lengthComputable) {
							li.text(file.name + ' (' + Math.round(e.loaded / e.total * 100) + '%)');
						}
					}, false);
				}
				provider = function () {
					return xhr;
				};

				sendData = {
					type: 'POST',
					url: uploadUrl,
					xhr: provider,
					dataType: 'json',
					success: function (data) {
						var fileId = data.fileId;
						li.text(data.name);

						$field.input_csv('add', ',', fileId);

						if(data.type.substring(0,6) == 'image/') {
							li.prepend($('<img src="tiki-download_file.php?fileId=' + fileId + '&display&height=24" height="24">'));
						} else if(data.type == 'application/pdf') {
							li.prepend($('<img height="16" width="16" title="application/pdf" alt="application/pdf" src="img/icons/mime/pdf.png">'));
						} else if(data.type.indexOf("sheet") != -1) {
							li.prepend($('<img height="16" width="16" title="'+ data.type +'" alt="'+ data.type +'" src="img/icons/mime/xls.png">'));
						} else if(data.type.indexOf("zip") != -1) {
							li.prepend($('<img height="16" width="16" title="'+ data.type +'" alt="'+ data.type +'" src="img/icons/mime/zip.png">'));
						} else if (data.type.substring(0,6) == 'video/') {
							li.prepend($('<img height="16" width="16" title="'+ data.type +'" alt="'+ data.type +'" src="img/icons/mime/flv.png">'));
						} else if (data.type.indexOf("word") != -1) {
							li.prepend($('<img height="16" width="16" title="'+ data.type +'" alt="'+ data.type +'" src="img/icons/mime/doc.png">'));
						} else {
							li.prepend($('<img height="16" width="16" title="'+ data.type +'" alt="'+ data.type +'" src="img/icons/mime/default.png">'));
						}
						li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
						li.find('img.icon').click(function () {
							$field.input_csv('delete', ',', fileId);
							$(this).closest('li').remove();
						});
									
						if (replaceFile && $self.data('firstfile') > 0) {	
							li.prev('li').remove();
						}

						if (! $self.data('firstfile')) {
							$self.data('firstfile', fileId);
						}
					},
					error: function (jqxhr) {
						$fileinput.showError(jqxhr);
						li.remove();
					},
					complete: function () {
						$(window).dequeue('process-upload');
					}
				};

				if (window.FormData) {
					sendData.processData = false;
					sendData.contentType = false;
					sendData.cache = false;

					sendData.data = new FormData;
					sendData.data.append('fileId', replaceFile ? $self.data('firstfile') : null);
					sendData.data.append('galleryId', $self.data('galleryid'));
					sendData.data.append('data', file);
				} else {
					data = e.target.result;
					sendData.data = {
						name: file.name,
						size: file.size,
						type: file.type,
						data: data.substr(data.indexOf('base64') + 7),
						fileId: replaceFile ? $self.data('firstfile') : null,
						galleryId: $self.data('galleryid') 
					};
				}

				$.ajax(sendData);
			};
			reader.readAsDataURL(file);
		});
	});
	$(window).dequeue('process-upload');
};

$files.find('input').hide();
$files.find('img.icon').click(function () {
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
		var $clone;
		if (this.files) {
			handleFiles(this.files);
			$fileinput.val('');
			$clone = $fileinput.clone(true);
			$fileinput.replaceWith($clone);

			$fileinput = $clone;
		}
	});
}

$url.keypress(function (e) {
	if (e.which === 13) {
		var $this = $(this);
		var url = $this.val();
		$this.attr('disabled', true).clearError();

		$.ajax({
			type: 'POST',
			url: $.service('file', 'remote'),
			dataType: 'json',
			data: {
				galleryId: $self.data('galleryid'),
				url: url,
				reference: $this.next('.reference').val()
			},
			success: function (data) {
				var fileId = data.fileId, li = $('<li/>');
				li.text(data.name);

				$field.input_csv('add', ',', fileId);

				li.prepend($('<img src="tiki-download_file.php?fileId=' + fileId + '&display&height=24" height="24">'));
				li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
				li.find('img.icon').click(function () {
					$field.input_csv('delete', ',', fileId);
					$this.closest('li').remove();
				});
				$files.append(li);
				$this.val('');
			},
			error: function (jqxhr) {
				$this.showError(jqxhr);
			},
			complete: function () {
				$this.removeAttr('disabled');
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
			"filter~filetype": $self.data('filter'),
			"filter~gallery_id": $self.data('galleryid')
		}, function (data) {
			$search.removeAttr('disabled').clearError();
			$.each(data, function () {
				var item = $('<li/>').append(this.link), icon = $('<label>{{icon _id=add}}</label>'), data = this;
				item.append(icon);
				icon.click(function () {
					var li = $('<li/>');
					li.text(item.text());
					li.prepend($('<img src="tiki-download_file.php?fileId=' + data.object_id + '&display&height=24" height="24">'));
					li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
					li.find('img.icon').click(function () {
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
	var hash = "";
	if (typeof file === "string") {
		var m = file.match(/target=([^&]*)/);
		if (!m || m.length < 2) {
			return false;	// error?
		}
		hash = m[1];
	} else {
		hash = file.hash;
	}
	$.ajax({
		type: 'GET',
		url: $.service('file_finder', 'finder'),
		dataType: 'json',
		data: {
			cmd: "tikiFileFromHash",
			hash: hash
		},
		success: function (data) {
			var fileId = data.fileId, li = $('<li/>');

			var eventOrigin = $("body").data("eventOrigin");
			if (eventOrigin) {
				var $ff = $(eventOrigin).parents(".files-field");
				$field = $(".input", $ff);
				$files = $(".current-list", $ff);
			}

			li.text(data.name);

			$field.input_csv('add', ',', fileId);

			li.prepend($('<img src="tiki-download_file.php?fileId=' + fileId + '&display&height=24" height="24">'));
			li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
			li.find('img.icon').click(function () {
				$field.input_csv('delete', ',', fileId);
				$(this).closest('li').remove();
			});

			$files.append(li);
		},
		error: function (jqxhr) {
		},
		complete: function () {
			$(window).data("elFinderDialog").dialog("close");
			$($(window).data("elFinderDialog")).remove();
			$(window).data("elFinderDialog", null);
			return false;
		}
	});
};
handleVimeoFile = function (link, data) {
	var fileId = data.fileId, li = $('<li/>');

	var eventOrigin = link;
	if (eventOrigin) {
		var $ff = $(eventOrigin).parents(".files-field");
		$field = $(".input", $ff);
		$files = $(".current-list", $ff);
	}

	li.text(data.file);

	$field.input_csv('add', ',', fileId);

	li.prepend($('<img src="img/icons/vimeo.png" height="16">'));
	li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
	li.find('img.icon').click(function () {
		$field.input_csv('delete', ',', fileId);
		$(this).closest('li').remove();
	});

	$files.append(li);
};
});
{/jq}
{if $prefs.vimeo_upload eq 'y' and $field.options_map.displayMode eq 'vimeo' and $prefs.feature_jquery_validation eq 'y'}
	{jq}
		$.validator.addMethod("isVimeoUrl", function(value, element) {
		    return this.optional(element) || value.match(/http[s]?\:\/\/(?:www\.)?vimeo\.com\/\d+$/);
		}, tr("* URL should be in the format: https://vimeo.com/nnnnnnn"));
		$.validator.addClassRules({
			vimeourl : { isVimeoUrl : true }
		});
	{/jq}
{/if}
