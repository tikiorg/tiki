{tikimodule title="{tr}Most popular tags{/tr}" name="admin_menu" flip=$module_params.flip decorations=$module_params.decorations}
{foreach from=$most_popular_tags item=tag}
<a class="freetag_{$tag.size}" href="tiki-browse_freetags.php?tag={$tag.tag}">{$tag.tag}</a> 
{/foreach}
{/tikimodule}
