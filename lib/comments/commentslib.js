//$Id$

$(document).ready(function() {

	comments_fill_field('anonymous_name');
	comments_fill_field('anonymous_email');
	comments_fill_field('anonymous_website');

	// when previewing a comment set the page anchor to the comment preview
	$('#comments_previewComment').click(function() {
		action = $('#editpostform').attr('action');
		$('#editpostform').attr('action', action.replace('#comments', '#form'));
	});

	$('#comments_postComment').click(function() {
		comments_anonymous_fields();
	});
	$('#comments_previewComment').click(function() {
		comments_anonymous_fields();
	});
});

// save anonymous name, website and email in a cookie
function comments_anonymous_fields() {
	if ($('#anonymous_name').length) {
		setCookie('anonymous_name', $('#anonymous_name').val());
	}
	if ($('#anonymous_email').length) {
		setCookie('anonymous_email', $('#anonymous_email').val());
	}
	if ($('#anonymous_website').length) {
		setCookie('anonymous_website', $('#anonymous_website').val());
	}
}

// if field content is saved in a cookie fill it
function comments_fill_field(id) {
	var field_content = getCookie(id);
	if (field_content) {
		$('#' + id).val(field_content);
	}
}
