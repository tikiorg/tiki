{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="breadcrumb" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $module_params.nonums != 'y'}<ol>{else}<ul>{/if}
	{section name=ix loop=$breadCrumb}
		<li>
			<a class="linkmodule" href="{$breadCrumb[ix]|sefurl}">
				{if ($maxlen > 0 && strlen($breadCrumb[ix]) > $maxlen)}
					{$breadCrumb[ix]|truncate:$maxlen:"...":true|escape}
				{else}
					{$breadCrumb[ix]|escape}
				{/if}
			</a>
		</li>
	{/section}
	{if $module_params.nonums != 'y'}</ol>{else}</ul>{/if}
{/tikimodule}
