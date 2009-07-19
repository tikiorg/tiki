{* $Id$ *}

{if $prefs.feature_file_galleries eq 'y'}
{if !isset($tpl_module_title)}
{if $nonums eq 'y'}
{eval var="{tr}Top `$module_rows` files{/tr}" assign="tpl_module_title"}
{else}
{eval var="{tr}Top files{/tr}" assign="tpl_module_title"}
{/if}
{/if}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_files" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $nonums != 'y'}<ol>{else}<ul>{/if}
{section name=ix loop=$modTopFiles}
<li>
{if $prefs.feature_shadowbox eq 'y' and $modLastFiles[ix].type|substring:0:5 eq 'image'}
	<a class="linkmodule" href="{$modTopFiles[ix].fileId|sefurl:preview}" rel="shadowbox[modTopFiles];type=img">
{else}
	<a class="linkmodule" href="{$modTopFiles[ix].fileId|sefurl:file}">
{/if}
{$modTopFiles[ix].filename}</a></li>
{/section}
{if $nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
{/if}
