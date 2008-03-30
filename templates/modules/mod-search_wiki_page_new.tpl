{* $Id$ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Search Wiki PageName{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="search_wiki_page_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
    {tr}Exact&nbsp;match{/tr}<input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
  </form>
{/tikimodule}
