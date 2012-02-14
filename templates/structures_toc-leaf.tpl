{strip}
	{$leafspace}
	<li class="{if $toc_type eq 'fancy'}fancy{/if}toclevel">
		<div>
			{if $numbering}
				<span class="prefix">{$structure_tree.prefix}&nbsp;</span>
			{/if}
			{if $showdesc and $structure_tree.description}
				<span class="description">{$structure_tree.description|escape} :&nbsp;</span>
			{/if}
			<a href="{sefurl page=$structure_tree.pageName structure=$structurePageName page_ref_id=$structure_tree.page_ref_id}"
					class="link" title="
				{if $showdesc}
					{if $structure_tree.page_alias}
						{$structure_tree.page_alias|escape}
					{else}
						{$structure_tree.pageName|escape}
					{/if}
				{else}
					{$structure_tree.description|escape}
				{/if}">
				{if $hilite}<b>{/if}
				{if $structure_tree.page_alias|escape}
					{$structure_tree.page_alias|escape}
				{else}
					{$structure_tree.pageName|escape}
				{/if}
				{if $hilite}</b>{/if}
			</a>
			{if !$showdesc and $toc_type eq 'fancy'} : <span class="description">{$structure_tree.description|escape}</span>{/if}
		</div>
	{* no </li> here *}
{*else}						old "plain" formatting code here for reference - TODO remove before 9.0
	{$leafspace}
	<li class="toclevel">
		{if $numbering}{$structure_tree.prefix} {/if}
		<a href="{sefurl page=$structure_tree.pageName structure=$structurePageName page_ref_id=$structure_tree.page_ref_id}"
			class="link" title="
			{if $showdesc}
				{if $structure_tree.page_alias}
					{$structure_tree.page_alias}
				{else}
					{$structure_tree.pageName}
				{/if}
			{else}
				{$structure_tree.description|escape}
			{/if}">
			{if $hilite}<b>{/if}
			{if $showdesc}
				{$structure_tree.description}
			{else}
				{if $structure_tree.page_alias}
					{$structure_tree.page_alias}
				{else}
					{$structure_tree.pageName}
				{/if}
			{/if}
			{if $hilite}</b>{/if}
		</a>
	{ * no </li> here * }
{/if*}
{/strip}