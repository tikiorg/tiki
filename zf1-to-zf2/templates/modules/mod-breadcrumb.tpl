{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="breadcrumb" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$breadCrumb nonums=$nonums}
	{section name=ix loop=$breadCrumb}
		<li>
			<a class="linkmodule" href="{$breadCrumb[ix]|sefurl}">
				{if ($maxlen > 0 && strlen($breadCrumb[ix]) > $maxlen)}
					{if $namespaceoption eq 'n'}
						{$data=$prefs.namespace_separator|explode:$breadCrumb[ix]}
						{if empty($data['1'])}
							{$data['0']|truncate:$maxlen:"...":true|escape}
						{else}
							{$data['1']|truncate:$maxlen:"...":true|escape}
						{/if}
					{else}
						{$breadCrumb[ix]|truncate:$maxlen:"...":true|escape}
					{/if}
				{else}
					{if $namespaceoption eq 'n'}
						{$data=$prefs.namespace_separator|explode:$breadCrumb[ix]}
						{if empty($data['1'])}
							{$data['0']|escape}
						{else}
							{$data['1']|escape}
						{/if}
					{else}
						{$breadCrumb[ix]|escape}
					{/if}
				{/if}
			</a>
		</li>
	{/section}
{/modules_list}
{/tikimodule}
