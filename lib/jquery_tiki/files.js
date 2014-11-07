
(function ($) {
	var handleFiles, ProgressBar, FileList;

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

	handleFiles = function (input) {
		var files = input.files,
			galleryId = input.galleryId,
			progressBar = input.progress,
			fileList = input.list;

		var uploadUrl = $.service('file', 'upload');
		$.each(files, function (k, file) {
			var reader = new FileReader();
			progressBar.updateSegment(k, 0, file.size);

			$(window).queue('process-upload', function () {
				reader.onloadend = function (e) {
					var xhr, provider, sendData, data;

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

					$.ajax(sendData);
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
			}
		});

		list = new FileList({
			list: $form.parent().find('.file-uploader-result')[0]
		});

		list.clearErrors();

		handleFiles({
			galleryId: $form.data('gallery-id'),
			files: files,
			progress: progress,
			list: list
		});
	}

	$(document).on('change', 'form.file-uploader input[type=file]', function () {
		var $clone, $form = $(this).closest('form'), progress, list;
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
})(jQuery);
