{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.2 2006-02-05 16:10:07 amette Exp $ *}
{if $feature_ajax eq 'y'}
<script type="text/javascript">var maxRecords = {$maxRecords};</script>
<script type="text/javascript">var directPagination = '{$direct_pagination}';</script>
<script type="text/javascript">
	var ajax_cols = new Array()
	{section name=ix start=0 loop=$ajax_cols}
		ajax_cols[{$smarty.section.ix.index}] = '{$ajax_cols[ix]}'
	{/section}
</script>
<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/cpaint/tiki-ajax.js" type="text/javascript"></script>
<div id="ajaxDebug"></div>
{/if}
