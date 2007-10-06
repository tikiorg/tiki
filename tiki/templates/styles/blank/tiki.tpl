{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}<table dir="rtl" ><tr><td>{/if}

{$mid_data}

{if $prefs.feature_bidi eq 'y'}</td></tr></table>{/if}
{include file="footer.tpl"}
