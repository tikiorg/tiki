<div id="comment-container" data-target="{service controller=comment action=list type=$wikiplugin_comment_objectType objectId=$wikiplugin_comment_objectId}"></div>
{jq}
var id = '#comment-container';
$(id).comment_load($(id).data('target'));
$(document).ajaxComplete(function(){$(id).tiki_popover();});
{/jq}
