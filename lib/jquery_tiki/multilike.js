$(".multilike_group").click(function(e) {
    e.preventDefault();
    var element = $(this);
    $.post($.service(
        'relation',
        'toggle_group',
        {
            relation:$(element).data('relation'),
            relation_prefix:$(element).data('relation_prefix'),
            target_type:$(element).data('target_type'),
            target_id:$(element).data('target_id'),
            source_type:"user",
            source_id:$(element).data('user')
        }
        ), function(data) {
            location.reload();
        },
        'json');
});
$(".multilike_many").click(function(e) {
    e.preventDefault();
    var element = $(this);
    $.post($.service(
        'relation',
        'toggle',
        {
            relation:$(element).data('relation'),
            target_type:$(element).data('target_type'),
            target_id:$(element).data('target_id'),
            source_type:"user",
            source_id:$(element).data('user')
        }
        ), function(data) {
            location.reload();
        },
        'json');
});