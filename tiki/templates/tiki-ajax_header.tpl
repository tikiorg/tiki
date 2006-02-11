{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.3 2006-02-11 15:00:25 amette Exp $ *}
{if $feature_ajax eq 'y'}
<script type="text/javascript">var maxRecords = {$maxRecords};</script>
<script type="text/javascript">var directPagination = '{$direct_pagination}';</script>
<script type="text/javascript">
	var ajax_cols = new Array()
	{section name=i start=0 loop=$ajax_cols}
		ajax_data_{$smarty.section.i.index} = new Array();
	{/section}
	{section name=i start=0 loop=$ajax_cols}
		ajax_cols[{$smarty.section.i.index}] = ajax_data_{$smarty.section.i.index}
	{/section}
	{section name=i start=0 loop=$ajax_cols}
		{section name=j start=0 loop=$ajax_cols[i]}
		ajax_data_{$smarty.section.i.index}[{$smarty.section.j.index}] = '{$ajax_cols[i][j]}'
		{/section}
	{/section}
</script>
<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<script src="lib/cpaint/cpaint2.inc.compressed.js" type="text/javascript"></script>
<script src="lib/cpaint/tiki-ajax.js" type="text/javascript"></script>
<div id="ajaxDebug"></div>
{/if}
