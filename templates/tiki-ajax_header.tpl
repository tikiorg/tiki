{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.5 2006-03-20 06:15:12 lfagundes Exp $ *}
{if $feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript" src="lib/ajax/tiki-ajax.js"></script>

<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>
{/if}

{* <script type="text/javascript">
  var maxRecords = {$maxRecords};
  var directPagination = '{$direct_pagination}';

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

</script> *}


