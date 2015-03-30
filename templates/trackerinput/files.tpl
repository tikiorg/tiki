<div class="files-field uninitialized {if $data.replaceFile}replace{/if}" data-galleryid="{$field.galleryId|escape}" data-firstfile="{$field.firstfile|escape}" data-filter="{$field.filter|escape}" data-limit="{$field.limit|escape}">
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
			<a href="{service controller=file action=uploader galleryId=$field.galleryId limit=$limit|default:100 type=$field.filter}" class="btn btn-default upload-files">{tr}Upload Files{/tr}</a>
		{/if}
	{/if}
	{if $prefs.fgal_tracker_existing_search eq 'y'}
		{if $prefs.fgal_elfinder_feature eq 'y'}
			{button href='tiki-list_file_gallery.php' _text="{tr}Browse files{/tr}"
				_onclick="return openElFinderDialog(this, {ldelim}defaultGalleryId:{if !isset($field.options_array[8]) or $field.options_array[8] eq ''}{if empty($field.options_array[0])}0{else}{$field.options_array[0]|escape}{/if}{else}{$field.options_array[8]|escape}{/if},deepGallerySearch:{if empty($field.options_array[6])}0{else}{$field.options_array[6]|escape}{/if},getFileCallback:function(file,elfinder){ldelim}window.handleFinderFile(file,elfinder){rdelim},eventOrigin:this{rdelim});"
				title="{tr}Browse files{/tr}"}
		{else}
			<a href="{service controller=file action=browse galleryId=$galleryId limit=$limit|default:100 type=$field.filter}" class="btn btn-default browse-files">{tr}Browse Files{/tr}</a>
		{/if}
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
		var $files = $('.current-list', this);
		var $warning = $('.alert', this);
		var $field = $('.input', this);
		var $url = $('.url', this);
		var replaceFile = $(this).is('.replace');

		function toggleWarning() {
			var limit = $self.data('limit');
			if (limit) {
				if ($files.children().length > limit) {
					$warning.show();
					$files.children().css('text-decoration', 'line-through');
					$files.children().slice(-5).css('text-decoration', '');
				} else {
					$files.children().css('text-decoration', '');
					$warning.hide();
				}
			}
		}

		function addFile(fileId, type, name) {
			var li = $('<li>').appendTo($files);
			li.text(name);

			$field.input_csv('add', ',', fileId);

			li.prepend($.fileTypeIcon(fileId, { type: type, name: name }));
			li.append($('<label>{{icon _id=cross alt="{tr}Remove{/tr}"}}</label>'));
			li.find('img.icon').click(function () {
				$field.input_csv('delete', ',', fileId);
				$(this).closest('li').remove();
				toggleWarning();
			});

			if (replaceFile && $self.data('firstfile') > 0) {
				li.prev('li').remove();
			}

			if (! $self.data('firstfile')) {
				$self.data('firstfile', fileId);
			}

			toggleWarning();
		}

		$field.hide();
		toggleWarning();

		$self.find('.btn.upload-files').clickModal({
			success: function (data) {
				$.each(data.files, function (k, file) {
					addFile(file.fileId, file.type, file.name);
				});

				$.closeModal();
			}
		});

		$self.find('.btn.browse-files').on('click', function () {
			if (! $(this).data('initial-href')) {
				$(this).data('initial-href', $(this).attr('href'));
			}

			// Before the dialog handler triggers, replace the href with one including current files
			$(this).attr('href', $(this).data('initial-href') + '&file=' + $field.val());
		});
		$self.find('.btn.browse-files').clickModal({
			size: 'modal-lg',
			success: function (data) {
				$files.empty();
				$field.val('');

				$.each(data.files, function (k, file) {
					addFile(file.fileId, file.type, file.name);
				});

				$.closeModal();
			}
		});

		// Delete for previously existing files
		$files.find('input').hide();
		$files.find('img.icon').click(function () {
			var fileId = $(this).closest('li').data('file-id');
			$field.input_csv('delete', ',', fileId);
			$(this).closest('li').remove();
			toggleWarning();
		});

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
						addFile(data.fileId, data.type, data.name);
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
					var eventOrigin = $("body").data("eventOrigin");
					if (eventOrigin) {
						var $ff = $(eventOrigin).parents(".files-field");
						$field = $(".input", $ff);
						$files = $(".current-list", $ff);
					}

					addFile(data.fileId, data.type, data.name);
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
			var eventOrigin = link;
			if (eventOrigin) {
				var $ff = $(eventOrigin).parents(".files-field");
				$field = $(".input", $ff);
				$files = $(".current-list", $ff);
			}

			addFile(data.fileId, data.type, data.name);
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
