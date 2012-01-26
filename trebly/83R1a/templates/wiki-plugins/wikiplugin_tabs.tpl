{* $Id: wikiplugin_tabs.tpl 37384 2011-09-18 16:44:41Z sept_7 $ 
 * smarty template for tabs wiki plugin 
 *}
{if $is_slideshow eq 'y'}
	{foreach from=$tabs key=i item=tab}
		{$tabcontent[$i]}
	{/foreach}
{else}
~np~{tabset toggle=$toggle name=$tabsetname|escape}
	{section name=ix loop=$tabs}{tab name=$tabs[ix]|escape}{$tabcontent[ix]}{/tab}{/section}
{/tabset}~/np~
{/if}
