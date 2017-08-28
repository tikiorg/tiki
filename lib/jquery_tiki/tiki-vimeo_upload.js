/*
 * jQuery File Upload connector to upload Vimeo files
 *
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * $Id$
 */


$(function () {

	$('#fileupload').fileupload({
		url: uploadlinksecure,
		type: 'PUT',
		multipart: false,
		maxNumberOfFiles: 1,
		maxFileSize: maxFileSize,
		acceptFileTypes: /video\/.*/i,
		autoUpload: false,
		dropZone: null, // could not get dropzone to trigger upload right after
		replaceFileInput: false
	}).on('fileuploadadd', function (e, data) {
		if (!$('#vimeofiletitle').val()) {
			e.preventDefault();
			alert(tr("A title is mandatory"));
			$('#fileupload').val('');
			return false;
		}
		var file = data.files[0];
		data.submit();
	}).on('fileuploadsubmit', function (e, data) {
		total_bytes = data.files[0].size;
	}).on('fileuploadsend', function (e, data) {
		data.headers = {}; // Prevent jQuery fileupload from sending Content-Disposition header which is blocked by Vimeo
	}).on('fileuploadprogress', function () {
		// Get upload progress
		checkProgress(true);
		updateProgressBar();
	}).on('fileuploaddone', function () {
		checkProgress(false);
		if (current_bytes >= total_bytes) {
			console.log('completing');
			completeVimeoUpload();
		} else {
			console.log('error');
		}
	});

});
