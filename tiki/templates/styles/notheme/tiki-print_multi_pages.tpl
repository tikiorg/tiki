{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme/tiki-print_multi_pages.tpl,v 1.3 2003-08-14 00:48:27 zaufi Exp $ *}

{include file="header.tpl"}
<div id="tiki-clean">
  <h1>Wiki Pages</h1>
  {section name=ix loop=$pages}
    <h2>{$pages[ix].pageName}</h2>
    <div class="wikitext">{$pages[ix].parsed}</div>
  <hr/>
  {/section}
</div>
{include file="footer.tpl"}
