/*
 * jQuery File Upload connector for Tiki
 *
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * $Id$
 */

$(function () {

	var url = $.service("file", "upload_multiple"),	// upload handler:
		uploadButton = $('<button/>')
			.addClass('btn btn-primary')
			.prop('disabled', true)
			.text('Processing...')
			.on('click', function () {
				var $this = $(this),
					data = $this.data();
				$this
					.off('click')
					.text('Abort')
					.on('click', function () {
						$this.remove();
						data.abort();
					});
				data.submit().always(function () {
					$this.remove();
				});
			});
	$('#fileupload').fileupload({
		url: url,
		dataType: 'json',
		autoUpload: false,
		//maxFileSize: 999000,
		// Enable image resizing, except for Android and Opera,
		// which actually support image resizing, but fail to
		// send Blob objects via XHR requests:
		disableImageResize: /Android(?!.*Chrome)|Opera/
			.test(window.navigator.userAgent),
		previewMaxWidth: 100,
		previewMaxHeight: 100,
		previewCrop: true,

		singleFileUploads: true,	// make optional one day


		filesContainer: $('div.files'),
		uploadTemplateId: null,
		downloadTemplateId: null,
		uploadTemplate: null,
		downloadTemplate: null

	}).on('fileuploadadd', function (e, data) {

		data.context = $('<div/>').appendTo('#files');

		$.each(data.files, function (index, file) {
			var node = $('<p/>')
				.append($('<span/>').text(file.name));
			if (!index) {
				node
					.append('<br>')
					.append(uploadButton.clone(true).data(data));
			}
			node.appendTo(data.context);
		});
	}).on('fileuploadprocessalways', function (e, data) {
		var index = data.index,
			file = data.files[index],
			context = data.context;

		if (!context.length) {	// context seems to fail here
			context = $("div:contains("+file.name+")", "#files");
		}
		var node = $(context.children()[index]);

		if (file.preview) {
			node
				.prepend('<br>')
				.prepend(file.preview);
		}
		if (file.error) {
			node
				.append('<br>')
				.append($('<span class="text-danger"/>').text(file.error));
		}
		if (index + 1 === data.files.length) {
			context.find('button')
				.text('Upload')
				.prop('disabled', !!data.files.error);
		}
	}).on('fileuploadprogressall', function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('.progress-bar', '#progress').css(
			'width',
			progress + '%'
		);
	}).on('fileuploaddone', function (e, data) {
		$.each(data.result.files, function (index, file) {

			var context = data.context;

			if (file.fileId) {
				var display = file.type.match(/^image\//) ? "display" : "dl",
					link = $('<a>')
					.attr('target', '_blank')
					.prop('href', display + file.fileId);

				$(context.children()[index])
					.wrap(link)
					.find("span").text(file.info.name);

				$(context)
					.append("<code>" + file.syntax + "</code>");

			} else if (file.error) {
				var error = $('<span class="text-danger"/>').text(file.error);

				$(context.children()[index])
					.append('<br>')
					.append(error);
			}
		});

	}).on('fileuploadfail', function (e, data) {
		$.each(data.files, function (index) {
			var error = $('<span class="text-danger"/>').text('File upload failed.' + data.errorThrown),
				context = data.context;

			if (!context.length) {	// context seems to fail here too
				context = $("div:contains("+data.files[0].name+")", "#files");
			}
			$(context.children()[index])
				.append('<br>')
				.append(error);
		});
		return false;
	}).parents("form").off("submit").on("submit", function (e) {
		// submitting the form seems to happen automatically resulting in a white page - not sure how still but this stops it...
		return false;

	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');

});
