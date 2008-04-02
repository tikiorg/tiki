{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Most popular tags{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="freetags_most_popular" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<ul class="freetag">
{foreach from=$most_popular_tags item=tag}
<li class="freetag"><a class="freetag_{$tag.size}" title="{tr}List everything tagged{/tr} {$tag.tag}" href="tags/{$tag.tag}">{$tag.tag}</a></li>
{/foreach}
</ul>
{/tikimodule}
