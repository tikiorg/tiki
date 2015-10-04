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
			.text(tr('Processing...'))
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
					$this.next("button").text(tr("Clear"));
					$this.remove();
				});
			}),
		cancelButton = $('<button/>')
			.addClass('btn btn-default')
			.text(tr('Cancel'))
			.on('click', function () {
				$(this).parents("div:first").remove();
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

		data.context = $('<div/>').addClass("margin-bottom-md").appendTo('#files');

		$.each(data.files, function (index, file) {
			var node = $('<p/>')
				.append($('<span/>').text(file.name));
			node.appendTo(data.context);
			if (!index) {
				data.context
					.append(uploadButton.clone(true).data(data))
					.append(cancelButton.clone(true).data(data));
			}
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
			node.prepend(file.preview);
		}
		if (file.error) {
			node.append($('<span class="text-danger"/>').text(file.error));
		}
		if (index + 1 === data.files.length) {
			context.find('button:first')
				.text('Upload')
				.prop('disabled', !!data.files.error);

			var $progdiv = $('<div class="progress margin-bottom-xs"/>')
				.append('<div class="progress-bar progress-bar-success"/>');

			context.find("canvas").after($progdiv);

			if ($("input[name=autoupload]:checked").length) {
				context.find('button:first').click();
			}
		}
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

				var match = location.search.match(/filegals_manager=([^&]+)/);
				if (match) {
					$(context).find("a").click(function () {
						window.opener.insertAt(match[1], file.syntax);
						checkClose();
						return false;
					}).attr("title", tr("Click here to use the file"));
				} else {
					$(context).find("a")
						.after("<code>" + file.syntax + "</code><br>");

					if (jqueryTiki.colorbox) {
						context.find("a").colorbox({photo: true});
					}
				}

			} else if (file.error) {
				var error = $('<span class="text-danger"/>').text(file.error);

				$(context.children()[index])
					.append('<br>')
					.append(error);
			}
		});

		e.preventDefault();

	}).on('fileuploadfail', function (e, data) {
		$.each(data.files, function (index) {
			var error = $('<span class="text-danger"/>').text(tr('File upload failed: ') + data.errorThrown),
				context = data.context;

			$(context.children()[index])
				.append(error);
		});
		return false;
	}).parents("form").off("submit").on("submit", function (e) {
		// submitting the form seems to happen automatically resulting in a white page - not sure how still but this stops it...
		return false;

	}).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');

});
