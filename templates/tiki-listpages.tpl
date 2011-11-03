{* $Id$ *}
{title admpage="wiki" help="Using+Wiki+Pages#List_Pages"}{tr}Pages{/tr}{/title}



{tabset name='tabs_wikipages'}
	{tab name="{tr}List Wiki Pages{/tr}"}

		{include file='find.tpl' find_show_languages='y' find_show_languages_excluded='y' find_show_categories_multi='y' find_show_num_rows='y'}

<form name="checkform" method="get" action="{$smarty.server.PHP_SELF}">
	<input type="hidden" name="offset" value="{$offset|escape}" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	<input type="hidden" name="maxRecords" value="{$maxRecords|escape}" />
</form>
		{if $error}
<div class="simplebox highlight">
			{$error}
</div>
		{/if}

{if $mapview}
{wikiplugin _name="googlemap" type="objectlist" width="400" height="400"}{/wikiplugin}
{/if}

<div id="tiki-listpages-content">
		{if $aliases}
	<div class="aliases">
		<strong>{tr}Page aliases found:{/tr}</strong>
			{foreach from=$aliases item=alias}
		<a href="{$alias.toPage|sefurl}" title="{$alias.fromPage|escape}" class="alias">{$alias.toPage|escape};</a>
			{/foreach}
	</div>
		{/if}
		{include file='tiki-listpages_content.tpl' clean='n'}
</div>


	{/tab}
	{if $tiki_p_edit == 'y'}
		{tab name="{tr}Create a Wiki Page{/tr}"}
<div class="center" style="text-align: center">
	<strong>{tr}Insert name of the page you wish to create{/tr}</strong>
	<form method="get" action="tiki-editpage.php">
		<input id="pagename" type="text" size="30" name="page" /><br />
		<input class="submit button" type="submit" name="quickedit" value="{tr}Create Page{/tr}" />
	</form>
</div>
		{/tab}
	{/if}
{/tabset}



