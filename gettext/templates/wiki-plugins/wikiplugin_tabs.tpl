{* smarty template for tabs wiki plugin *}
{tabset name=$tabsetname|escape}
	{section name=ix loop=$tabs}{tab name=$tabs[ix]|escape}{$tabcontent[ix]}{/tab}{/section}
{/tabset}
