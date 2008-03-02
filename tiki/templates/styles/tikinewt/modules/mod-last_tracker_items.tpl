{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-last_tracker_items.tpl,v 1.7.10.3 2007/08/13 09:04:37 marclaporte *}

{if $feature_trackers eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Last `$module_rows` Items{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Last Items{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="last_tracker_items" flip=$module_params.flip decorations=$module_params.decorations}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modLastItems}
<li><a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastItems[ix].itemId}">
{$modLastItems[ix].subject}
</a>
</li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
