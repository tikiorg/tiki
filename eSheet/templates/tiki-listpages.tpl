{* $Id$ *}

{title admpage="wiki" help="Using+Wiki+Pages#List_Pages"}{tr}Pages{/tr}{/title}

{include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}

<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
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

<div id="tiki-listpages-content">
	{include file="tiki-listpages_content.tpl"}
</div>

