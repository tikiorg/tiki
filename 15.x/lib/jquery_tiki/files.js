
(function ($) {
	var handleFiles, ProgressBar, FileList, FileListInline;

	$.fileTypeIcon = function (fileId, file) {
		if(file.type.substring(0,6) == 'image/') {
			return $('<img src="tiki-download_file.php?fileId=' + fileId + '&display&height=24" height="24">');
		} else if(file.type == 'application/pdf') {
			return $('<img height="16" width="16" title="application/pdf" alt="application/pdf" src="img/icons/mime/pdf.png">');
		} else if(file.type.indexOf("sheet") != -1) {
			return $('<img height="16" width="16" title="'+ file.type +'" alt="'+ file.type +'" src="img/icons/mime/xls.png">');
		} else if(file.type.indexOf("zip") != -1) {
			return $('<img height="16" width="16" title="'+ file.type +'" alt="'+ file.type +'" src="img/icons/mime/zip.png">');
		} else if (file.type.substring(0,6) == 'video/') {
			return $('<img height="16" width="16" title="'+ file.type +'" alt="'+ file.type +'" src="img/icons/mime/flv.png">');
		} else if (file.type.indexOf("word") != -1) {
			return $('<img height="16" width="16" title="'+ file.type +'" alt="'+ file.type +'" src="img/icons/mime/doc.png">');
		} else {
			return $('<img height="16" width="16" title="'+ file.type +'" alt="'+ file.type +'" src="img/icons/mime/default.png">');
		}
	};

	ProgressBar = function (options) {
		var bar = this;
		this.segments = [];
		this.updateSegment = function (number, current, total) {
			bar.segments[number] = [current, total];
			bar.update();
		};
		this.update = function () {
			var total = 0, current = 0;
			$.each(bar.segments, function (k, item) {
				current += item[0];
				total += item[1];
			});

			options.progress(current, total);

			if (current === total) {
				options.done();
			}
		};
	};

	FileList = function (options) {
		var $list = $(options.list);
		this.clearErrors = function () {
			$list.find('.text-danger').remove();
		};
		this.addError = function (file) {
			var $li = $('<li>').appendTo($list);

			$li.text(file.name);
			$li.addClass('text-danger');
			$li.append(' (' + tr('uploading failed') + ')');
		};
		this.addFile = function (fileId, file) {
			var $li = $('<li>').appendTo($list);

			$li
				.text(file.name)
				.prepend($.fileTypeIcon(fileId, file));
			$('<input type="hidden" name="file[]">')
				.attr('value', fileId)
				.appendTo($li)
		};
	};

	FileListInline = function (options) {
		var $list = $(options.list),
			$form = $(options.form),
			$files = [];
		this.clearErrors = function () {
			$list.find('.text-danger').remove();
		};
		this.addError = function (file) {
			var $li = $('<li>').appendTo($form);

			$li.text(file.name);
			$li.addClass('text-danger');
			$li.append(' (' + tr('uploading failed') + ')');
		};
		this.success = function(data){
			$form.trigger('submit', [{files: $files}]);
		};
		this.addFile = function (fileId, file) {
			file.fileId = fileId;
			$files.push(file);

			var action = $form.attr('data-action');
			$.ajax(action, {
				type: 'POST',
				dataType: 'json',
				data: {
					files: [fileId]
				},
				success: this.success
			});
		};
	};

	handleFiles = function (input) {
		var files = input.files,
			accept = input.accept,
			galleryId = input.galleryId,
			progressBar = input.progress,
			fileList = input.list;

		var uploadUrl = $.service('file', 'upload');
		$.each(files, function (k, file) {
			var reader = new FileReader();
			progressBar.updateSegment(k, 0, file.size);
			window.lastFile = file;

			$(window).queue('process-upload', function () {
				reader.onloadend = function (e) {
					var xhr, provider, sendData, data, valid = true;

					xhr = jQuery.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.addEventListener('progress', function (e) {
							if (e.lengthComputable) {
								progressBar.updateSegment(k, e.loaded, e.total);
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
							fileList.addFile(data.fileId, file);
						},
						error: function (jqxhr) {
							progressBar.updateSegment(k, 0, 0);
							fileList.addError(file);
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
						sendData.data.append('galleryId', galleryId);
						sendData.data.append('data', file);
					} else {
						data = e.target.result;
						sendData.data = {
							name: file.name,
							size: file.size,
							type: file.type,
							data: data.substr(data.indexOf('base64') + 7),
							galleryId: galleryId
						};
					}

					if (accept) {
						valid = file.type.match(new RegExp( ".?(" + accept.replace('*', '.*') + ")$", "i"));
					}

					if (valid) {
						$.ajax(sendData);
					} else {
						sendData.error(null);
					}
				};
				reader.readAsDataURL(file);
			});
		});
		$(window).dequeue('process-upload');
	};

	$(document).on('submit', 'form.file-uploader', function (e) {
		e.preventDefault();
	});

	function doUpload($form, files) {
		var progress, list;
		
		progress = new ProgressBar({
			progress: function (current, total) {
				var percentage = Math.round(current / total * 100);

				$form.find('.progress').removeClass('hidden');
				$form.find('.progress-bar')
					.attr('aria-valuenow', percentage)
					.width(percentage + '%');
				$form.find('.progress-bar .sr-only .count')
					.text(percentage);
			},
			done: function () {
				$form.find('.progress').addClass('hidden');
				$form.find('.progress-bar')
					.attr('aria-valuenow', 0)
					.width('0%');
				$form.find('.progress-bar .sr-only .count')
					.text('0%');
			}
		});

		if($form.is(".inline")){
			list = new FileListInline({
				list: $form.parent().find('.file-uploader-result ul')[0],
				form: $form
			});
		} else {
			list = new FileList({
				list: $form.parent().find('.file-uploader-result ul')[0]
			});
		}

		list.clearErrors();

		handleFiles({
			accept: $form.find(':file').attr('accept'),
			galleryId: $form.data('gallery-id'),
			files: files,
			progress: progress,
			list: list
		});
	}

	$(document).on('change', '.file-uploader input[type=file]', function () {
		var $clone, $form = $(this).closest('.file-uploader'), progress, list;
		if (this.files) {
			doUpload($form, this.files);

			$(this).val('');
			$clone = $(this).clone(true);
			$(this).replaceWith($clone);
		}
	});

	if (window.FileReader) {
		$(document).ready(function () {
			$('.drop-message').show();
		});
		$(document).on('dragenter', '.file-uploader', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('border', '2px dashed gray');
			return false;
		});
		$(document).on('dragexit', '.file-uploader', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).css('border', '');
			return false;
		});
		$(document).on('dragover', '.file-uploader', function (e) {
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
		$(document).on('drop', '.file-uploader', function (e) {
			var $form = $(this);

			e.preventDefault();
			e.stopPropagation();
			$(this).css('border', '');

			var dataTransfer = e.dataTransfer;
			if (! dataTransfer) {
				dataTransfer = e.originalEvent.dataTransfer;
			}

			if (dataTransfer && dataTransfer.files) {
				doUpload($form, dataTransfer.files);
			}
			return false;
		});
	}

	function browserRemoveFile(link)
	{
		var list = $(link).closest('ul');
		$(link).closest('li').remove();

		list.closest('.file-browser').trigger('selection-update');
	}

	function browserAddFile(link)
	{
		var content = $(link).closest('.media-body').clone(true),
			icon = $(link).closest('.media, .panel').find('.media-object, .panel-body a').children('img').clone(true).width('16px'),
			nav = $(link).closest('.file-browser').find('.selection ul'),
			item = $('<li>'),
			a = $('<a>').text($(link).text()),
			id = $(link).data('object'),
			limit = nav.closest('form').data('limit'),
			current = nav.find('input[type=hidden]').filter(function () {
				return parseInt($(this).val(), 10) === id;
			});

		if (current.length > 0) {
			// Already in the list
			browserRemoveFile(current[0]);
			return;
		}

		if (limit === 1) {
			nav.empty();
		} else if (nav.children('li').length >= limit) {
			alert(nav.closest('form').data('limit-reached-message'));
			return;
		}

		a
			.prepend(' ')
			.prepend(icon);
		item.append(a);
		nav.append(item);

		item.append($('<input type="hidden" name="file[]">')
			.attr('value', id));

		nav.closest('.file-browser').trigger('selection-update');
	}

	$(document).on('selection-update', '.file-browser', function (e) {
		var selection = $('.selection input[type=hidden]', this).map(function () {
			return parseInt($(this).val(), 10);
		});

		$('.gallery-list .media-heading a, .gallery-list .panel-body a', this).each(function () {
			var id = $(this).data('object');
			$(this).closest('.media').toggleClass('bg-info', -1 !== $.inArray(id, selection));
			$(this).closest('.panel').toggleClass('panel-info', -1 !== $.inArray(id, selection));
		});
		$('.selection', this).toggleClass('hidden', selection.length === 0);
	});

	$(document).on('click', '.file-browser .gallery-list .pagination a', function (e) {
		e.preventDefault();
		$(this).closest('.modal').animate({ scrollTop: 0 }, 'slow');
		$(this).closest('.gallery-list')
			.tikiModal(tr('Loading...'))
			.load($(this).attr('href'), function () {
				$(this).tikiModal('');
				$(this).closest('.file-browser').trigger('selection-update');
			});
	});

	$(document).on('click', '.file-browser .gallery-list .media-heading a, .file-browser .gallery-list .panel-body a', function (e) {
		e.preventDefault();
		e.stopPropagation();
		browserAddFile(this);
	});
	$(document).on('click', '.file-browser .gallery-list .media, .file-browser .gallery-list .panel', function (e) {
		e.preventDefault();
		$('.media-heading a, .panel-body a', this).click();
	});

	$(document).on('click', '.file-browser .selection a', function (e) {
		e.preventDefault();
		browserRemoveFile(this);
	});

	$(document).on('submit', '.file-browser .form-inline', function (e) {
		e.preventDefault();
		$(this).closest('.file-browser').find('.gallery-list')
			.tikiModal(tr('Loading...'))
			.load($(this).attr('action'), $(this).serialize(), function () {
				$(this).tikiModal('');
				$(this).closest('.file-browser').trigger('selection-update');
			});
	});

	$(document).on('click', '.file-browser .submit .upload-files', function (e) {
		var $list = $(this).closest('.file-browser').find('.selection ul'),
			handler = $.clickModal({
				success: function (data) {
					$.each(data.files, function (k, file) {
						$('<li>')
							.append($('<a href="#">')
								.data('object', file.fileId)
								.data('type', 'file')
								.text(file.label))
							.append($('<input type="hidden" name="file[]">')
								.attr('value', file.fileId))
							.appendTo($list);

						$list.closest('.file-browser').trigger('selection-update');
					});
					$.closeModal();
				}
			});

		handler.apply(this, arguments);
	});

	// File selector component
	$(document).on('click', '.file-selector a', function () {
		if (! $(this).data('initial-href')) {
			$(this).data('initial-href', $(this).attr('href'));
		}

		// Before the dialog handler triggers, replace the href with one including current files
		$(this).attr('href', $(this).data('initial-href') + '&file=' + $(this).parent().children('input').val());
	});
	$(document).on('click', '.file-selector a', $.clickModal({
		size: 'modal-lg',
		success: function (data) {
			var files = [];
			$.each(data.files, function (k, f) {
				files.push(f.fileId);
			});
			$(this).parent().children('input').val(files.join(','));
			$(this).text($(this).text().replace(/\d+/, files.length));
			$.closeModal();
		}
	}));
})(jQuery);
