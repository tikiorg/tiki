{* $Id$ *}
{if $prefs.feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript" src="lib/ajax/tiki-ajax.js"></script>
{if $prefs.feature_ajax_autosave eq 'y'}<script type='text/javascript' src="lib/ajax/autosave.js"></script>{/if}

<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>

{/if}
