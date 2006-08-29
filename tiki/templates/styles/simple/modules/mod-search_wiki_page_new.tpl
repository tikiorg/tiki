{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-search_wiki_page_new.tpl,v 1.2 2006-08-29 20:19:15 sylvieg Exp $ *}

{tikimodule title="{tr}Search Wiki PageName{/tr}" name="search_wiki_page" flip=$module_params.flip decorations=$module_params.decorations}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
    {tr}Exact&nbsp;match{/tr}<input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>
    <button type="submit" class="wikiaction" name="search">{tr}go{/tr}</button> 
  </form>
{/tikimodule}
