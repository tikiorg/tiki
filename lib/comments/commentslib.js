//$Id$

$(document).ready(function() {
	// when previewing a comment set the page anchor to the comment preview
	$('#comments_previewComment').click(function() {
		action = $('#editpostform').attr('action');
		$('#editpostform').attr('action', action.replace('#comments', '#form'));
	});
});
