{* $Id$ *}
{if $prefs.feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript" src="lib/ajax/tiki-ajax.js"></script>
<script type='text/javascript' src="lib/ajax/autosave.js"></script>

<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>

{/if}
