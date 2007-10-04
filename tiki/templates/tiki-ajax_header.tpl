{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.7 2007-10-04 22:17:38 nyloth Exp $ *}
{if $prefs.feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript" src="lib/ajax/tiki-ajax.js"></script>

<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>

{/if}
