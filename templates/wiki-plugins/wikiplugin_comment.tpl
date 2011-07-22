<div id="comment-container" data-target="tiki-ajax_services.php?controller=comment&amp;action=list&amp;type={$wikiplugin_comment_objectType|escape:'url'}&amp;objectId={$wikiplugin_comment_objectId|escape:'url'}"></div>
{jq}
var id = '#comment-container';
$(id).comment_load($(id).data('target'));
{/jq}
