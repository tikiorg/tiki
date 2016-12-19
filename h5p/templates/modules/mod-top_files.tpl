{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="top_files" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modTopFiles nonums=$nonums}
	{section name=ix loop=$modTopFiles}
		<li>
		{if $prefs.feature_shadowbox eq 'y' and $modTopFiles[ix].type|substring:0:5 eq 'image'}
			<a class="linkmodule" href="{$modTopFiles[ix].fileId|sefurl:preview}" data-box="shadowbox[modTopFiles];type=img">
		{else}
			<a class="linkmodule" href="{$modTopFiles[ix].fileId|sefurl:file}">
		{/if}
		{$modTopFiles[ix].filename|escape}</a></li>
	{/section}
{/modules_list}
{/tikimodule}
