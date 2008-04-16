{* $Id$ *}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Most popular tags{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="freetags_most_popular" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if ($type ne 'cloud')}<ul class="freetag">{/if}
{foreach from=$most_popular_tags item=tag}
{if ($type ne 'cloud')}<li class="freetag">{/if}<a class="freetag_{$tag.size}" title="{tr}List everything tagged{/tr} {$tag.tag}" href="tiki-browse_freetags.php?tag={$tag.tag}">{$tag.tag}</a>&nbsp;{if ($type ne 'cloud')}</li>{/if}
{/foreach}
{if ($type ne 'cloud')}</ul>{/if}
{/tikimodule}
