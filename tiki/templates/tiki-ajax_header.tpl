{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-ajax_header.tpl,v 1.4 2006-03-20 04:45:53 lfagundes Exp $ *}
{if $feature_ajax eq 'y'}

{$xajax_js}

<script type="text/javascript">
  var maxRecords = {$maxRecords};
  var directPagination = '{$direct_pagination}';

  {literal}
  function loadComponent(url, template, htmlelement) {
      xajaxRequestUri = url;
      xajax_loadComponent(template, htmlelement);      
  }
  {/literal}

</script>


<div id="ajaxLoading">{tr}Loading...{/tr}</div>
<div id="ajaxDebug"></div>
{/if}

{* <script type="text/javascript">
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


