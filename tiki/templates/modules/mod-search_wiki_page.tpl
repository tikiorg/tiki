{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-search_wiki_page.tpl,v 1.5 2003-11-20 23:49:04 mose Exp $ *}

<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{tr}Search Wiki PageName{/tr}" module_name="search_box"}
</div>
<div class="box-data">
<form class="forms" method="post" action="tiki-listpages.php">
<input name="find" size="14" type="text" accesskey="s" value="{$find}"/>
<input type="submit" class="wikiaction" name="search" value="{tr}go{/tr}"/> 
</form>
</div>
</div>
