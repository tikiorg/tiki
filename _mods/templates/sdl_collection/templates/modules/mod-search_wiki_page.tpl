{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-search_wiki_page.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{tikimodule title="{tr}Search Wiki Pages{/tr}" name="search_box"}
  <form class="forms" method="post" action="tiki-listpages.php">
    <input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
    <input type="submit" class="wikiaction" name="search" value="{tr}Go{/tr}"/> 
  </form>
{/tikimodule}
