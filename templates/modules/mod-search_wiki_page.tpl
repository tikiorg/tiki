{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-search_wiki_page.tpl,v 1.3 2003-08-07 20:56:53 zaufi Exp $ *}

<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Search File{/tr}" module_name="search_wiki_page"}
</div>
<div class="box-data">
<form class="forms" method="post" action="tiki-listpages.php">
<input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
<input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
</form>
</div>
</div>
