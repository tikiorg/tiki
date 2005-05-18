{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-search_wiki_page.tpl,v 1.8 2005-05-18 11:03:30 mose Exp $ *}

{tikimodule title="{tr}Search Wiki PageName{/tr}" name="search_wiki_page" flip=$module_params.flip decorations=$module_params.decorations}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
    <input type="hidden" name="exact_match" value="On"/>
    <input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
  </form>
{/tikimodule}
