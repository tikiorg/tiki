{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* Index we display a wiki page here *}
{if $feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}

{include file=$mid}

{if $feature_bidi eq 'y'}</td></tr></table>{/if}
{include file="footer.tpl"}
