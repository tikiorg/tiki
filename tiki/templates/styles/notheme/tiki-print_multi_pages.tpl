{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme/tiki-print_multi_pages.tpl,v 1.2 2003-08-01 10:31:19 redflo Exp $ *}

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
