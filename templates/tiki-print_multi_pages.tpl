{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-print_multi_pages.tpl,v 1.5.10.1 2007-10-24 14:20:27 sylvieg Exp $ *}

<div id="tiki-clean">
  <h1>Wiki Pages</h1>
  {section name=ix loop=$pages}
    {if $prefs.feature_page_title ne 'n'}<h2>{$pages[ix].pageName}</h2>{/if}
    <div class="wikitext">{$pages[ix].parsed}</div>
  <hr/>
  {/section}
</div>
{include file="footer.tpl"}
