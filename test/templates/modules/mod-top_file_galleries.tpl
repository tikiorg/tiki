{* $Id$ *}

{if $prefs.feature_file_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` File Galleries{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top File Galleries{/tr}" assign="tpl_module_title"}
{/if}
{/if}
{tikimodule title=$tpl_module_title name="top_file_galleries" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modTopFileGalleries}
<li><a class="linkmodule" href="tiki-list_file_gallery.php?galleryId={$modTopFileGalleries[ix].galleryId}">{$modTopFileGalleries[ix].name}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
