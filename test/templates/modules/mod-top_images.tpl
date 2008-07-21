{* $Id$ *}

{if $prefs.feature_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` Images{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top Images{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule title=$tpl_module_title name="top_images" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopImages}
<li><a class="linkmodule" href="tiki-browse_image.php?imageId={$modTopImages[ix].imageId}">{$modTopImages[ix].name}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
