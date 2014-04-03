flp.Link.prototype.show = function(table) {
    $('<div></div>')
        .html(table)
        .dialog()
        .addClass('tablesorter tablesorter-dropbox')
        .tablesorter();
};