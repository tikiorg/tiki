{* smarty template for tabs wiki plugin *}
{if $is_slideshow eq 'y'}
	{foreach from=$tabs key=i item=tab}
		{$tabcontent[$i]}
	{/foreach}
{else}
~np~{tabset name=$tabsetname|escape}
	{section name=ix loop=$tabs}{tab name=$tabs[ix]|escape}~/np~{$tabcontent[ix]}~np~{/tab}{/section}
{/tabset}~/np~
{/if}