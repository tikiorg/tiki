function save_draft() {
    var form = document.getElementById('editpageform');

    var pageName = form.page.value;

    var pageDesc = '';
    if (form.description) {
	pageDesc = form.description.value;
    }

    var pageData = form.edit.value;

    var pageComment;
    if (form.comment) {
	pageComment = form.comment.value;
    }

    var args = new Array(pageName, pageDesc, pageData, pageComment);
    return xajax.call('save_draft', args, 1);
}
