{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_files" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastFiles nonums=$nonums}
{section name=ix loop=$modLastFiles}
	<li>
		{if $prefs.feature_shadowbox eq 'y' and $modLastFiles[ix].type|substring:0:5 eq 'image'}
			<a class="linkmodule" href="{$modLastFiles[ix].fileId|sefurl:preview}" rel="shadowbox[modLastFiles];type=img">
		{else}
			<a class="linkmodule" href="{$modLastFiles[ix].fileId|sefurl:file}">
		{/if}
			{$modLastFiles[ix].filename|escape}
		</a>
	</li>
{/section}
{/modules_list}
{/tikimodule}
