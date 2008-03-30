{* $Id$ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Search Wiki PageName{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="search_wiki_page" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
    <input type="hidden" name="exact_match" value="On"/>
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
  </form>
{/tikimodule}
