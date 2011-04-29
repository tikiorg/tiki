{* smarty template for tabs wiki plugin *}
~np~{tabset name=$tabsetname|escape}
	{section name=ix loop=$tabs}{tab name=$tabs[ix]|escape}~/np~{$tabcontent[ix]}~np~{/tab}{/section}
{/tabset}~/np~
