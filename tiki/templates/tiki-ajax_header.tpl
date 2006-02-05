{if $feature_ajax eq 'y'}
<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/cpaint/tiki-ajax.js" type="text/javascript"></script>
<script type="text/javascript">var maxRecords = {$maxRecords};</script>
<script type="text/javascript">var directPagination = '{$direct_pagination}';</script>
{/if}
