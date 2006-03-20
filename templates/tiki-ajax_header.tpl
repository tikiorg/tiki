{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.6 2006-03-20 21:39:19 lfagundes Exp $ *}
{if $feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript" src="lib/ajax/tiki-ajax.js"></script>

<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>

{/if}
