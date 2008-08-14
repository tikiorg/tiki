{* $Id$ *}

{title admpage="wiki"}{tr}Pages{/tr}{/title}

{include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}

<div align="center">
  <form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
    <input type="hidden" name="offset" value="{$offset|escape}" />
    <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
    <input type="hidden" name="find" value="{$find|escape}" />
    <input type="hidden" name="maxRecords" value="{$maxRecords|escape}" />
  </form>

  <div id="tiki-listpages-content">
    {include file="tiki-listpages_content.tpl"}
  </div>
</div>

