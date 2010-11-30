{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_metrics.tpl,v 1.62.2.8 2007/11/14 15:41:15 sylvieg Exp $ *}

<h1>
	<a class="pagetitle" href="tiki-admin_metrics.php">{tr}Admin Metrics{/tr}</a>

	{if $prefs.feature_help eq 'y'}
		<a href="{$prefs.helpurl}Metrics+Admin" target="tikihelp" class="tikihelp" title="{tr}Admin Metrics{/tr}"><img border='0' src='pics/icons/help.png' alt="{tr}Help{/tr}" width="16" height="16" /></a>
	{/if}

	{if $prefs.feature_view_tpl eq 'y'}
		<a href="tiki-edit_templates.php?template=tiki-admin_metrics.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Metrics Template{/tr}"><img src="pics/icons/shape_square_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
	{/if}
</h1>

<div class="navbar">
	{button href="#metrics" _text="{tr}Metrics{/tr}"}
	{button href="#tabs" _text="{tr}Tabs{/tr}"}
	{button href="#assign" _text="{tr}Assign Metrics{/tr}"}
	{button href="#assigned" _text="{tr}Assigned Metrics{/tr}"}
	{button href="#editcreate" _text="{tr}Edit/Create Metrics{/tr}"}
	{button href="#editcreatetab" _text="{tr}Edit/Create Tab{/tr}"}
</div>

<h2>{tr}Metrics{/tr}</h2>
<table class="normal" id="metrics">
	<tr class="first">
		<td class="heading">{tr}Name{/tr}</td>
		<td class="heading">{tr}Range{/tr}</td>
		<td class="heading">{tr}Data Type{/tr}</td>
		<td class="heading">{tr}Query{/tr}</td>
		<td class="heading">{tr}Action{/tr}</td>
	</tr>
	{if !empty($metrics_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$metrics_list key=i item=metric}
			<tr class="{cycle}">
				<td class="first">{$metric.metric_name|escape}</td>
				<td>{$metric.metric_range|escape}</td>
				<td>{$metric.metric_datatype|escape}</td>
				<td>{$metric.metric_query|escape}</td>
				<td>
					<a class="link" href="tiki-admin_metrics.php?metric_edit={$i|escape:'url'}#editcreate" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?assign_metric_new={$i|escape:'url'}#assign" title="{tr}Assign{/tr}"><img src="pics/icons/accept.png" width="16" height="16" alt="{tr}Assign{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?metric_remove={$i|escape:'url'}" title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td colspan="5" class="odd"><b>{tr}No records found{/tr}</b></td>
		</tr>
	{/if}
</table>
<br />

<h2>{tr}Tabs{/tr}</h2>
<table class="normal" id="tabs">
	<tr class="first">
		<td class="heading">{tr}Name{/tr}</td>
		<td class="heading">{tr}Weight{/tr}</td>
		<td class="heading">{tr}Action{/tr}</td>
	</tr>
	{if !empty($tabs_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$tabs_list key=i item=tab}
			<tr class="{cycle}">
				<td class="first">{$tab.tab_name|escape}</td>
				<td>{$tab.tab_order|escape}</td>
				<td>
					<a class="link" href="tiki-admin_metrics.php?tab_edit={$i|escape:'url'}#editcreatetab" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?tab_remove={$i|escape:'url'}" title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td colspan="3" class="odd"><b>{tr}No records found{/tr}</b></td>
		</tr>
	{/if}
</table>
<br/>

<h2>{tr}Assigned Metrics{/tr}</h2>
<table class="normal" id="assigned_metrics">
	<tr class="first">
		<td class="heading">{tr}Metric Name{/tr}</td>
		<td class="heading">{tr}Tab Name{/tr}</td>
		<td class="heading">{tr}Action{/tr}</td>
	</tr>
	{if !empty($metrics_assigned_list)}
		{cycle print=false values="odd,even"}
		{foreach from=$metrics_assigned_list key=i item=assigned_item}
			<tr class="{cycle}">
				<td class="first">{$metrics_list[$assigned_item.metric_id].metric_name|escape}</td>
				<td>{$tabs_list[$assigned_item.tab_id].tab_name|escape}</td>
				<td>
					<a class="link" href="tiki-admin_metrics.php?assign_metric_edit={$i|escape:'url'}#assign" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" width="16" height="16" alt="{tr}Edit{/tr}" /></a>
					<a class="link" href="tiki-admin_metrics.php?assign_remove={$i|escape:'url'}" title="{tr}Unassign{/tr}"><img src="pics/icons/cross.png" width="16" height="16" alt="{tr}Delete{/tr}" /></a>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td colspan="3" class="odd"><b>{tr}No records found{/tr}</b></td>
		</tr>
	{/if}
</table>
<br/>

<h2 id="assign">
	{if $assign_metric eq ''}
		{tr}Assign metric{/tr}
	{else}
		{tr}Edit this assigned metric:{/tr} {$assign_name}
	{/if}
</h2>
{if $preview eq 'y'}
	<h3>{tr}Preview{/tr}</h3>
	{$preview_data}
{/if}

<form method="post" action="tiki-admin_metrics.php#assign">
	<input type="hidden" name="assigned_id" value="{$assigned_id}" />
	<table class="formcolor">
		<tr class="{cycle}">
			<td>{tr}Metric Name{/tr}</td>
			<td>
				<select name="assign_metric">
					{foreach from=$metrics_list key=i item=metric}
						<option value="{$i|escape}" {if $assign_metric eq $i}selected="selected"{/if}>{$metric.metric_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Tab for metric{/tr}</td>
			<td>
				<select name="assign_tab">
					{foreach from=$tabs_list key=i item=tab}
						<option value="{$i|escape}" {if $assign_tab eq $i}selected="selected"{/if}>{$tab.tab_name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="assign" value="{tr}Assign{/tr}" /></td>
		</tr>
	</table>
</form>
<br />

<h2 id="editcreate">
	{if !isset($metric_id)}
		{tr}Create new metric{/tr}
	{else}
		{tr}Edit this metric:{/tr} {$metric_name|escape}
	{/if}
</h2>

<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Hints{/tr}</div>  
	<div class="rbox-data" name="tip">
		<ul>
			<li>{tr}For list-based metrics, include the "LIMIT #" in your query.{/tr}</li>
		</ul>
	</div>
</div>
<br/>

<form name="editmetric" method="post" action="tiki-admin_metrics.php#editcreate">
	<table id="admin_metrics_add" class="formcolor">
		<tr class="{cycle}">
			<td>{tr}Name (must be unique){/tr}</td>
			<td><input type="text" name="metric_name" value="{$metric_name|escape}" /></td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Range{/tr}</td>
			<td>
				<select name="metric_range">
					{foreach from=$metric_range_all key=rangeid item=rangename}
						<option value="{$rangeid}" {if (isset($metric_range) && ($metric_range eq $rangename))}selected="selected"{/if}>{$rangename}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Data Type{/tr}</td>
			<td>
				<select name="metric_datatype">
				{foreach from=$metric_datatype_all key=datatypeid item=datatypename}
					<option value="{$datatypeid}" {if (isset($metric_datatype) && ($metric_datatype eq $datatypename))}selected="selected"{/if}>{$datatypename}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}DSN{/tr}</td>
			<td>
				<select name="metric_dsn">
					<option value="local" {if isset($metric_dsn) && $metric_dsn eq 'local'}selected="selected"{/if}>{tr}Local (Tiki database){/tr}</option>
					{foreach from=$dsn_list key=datatypeid item=dsn}
						<option value="{$dsn.name}" {if isset($metric_dsn) && $metric_dsn eq $dsn.name}selected="selected"{/if}>{$dsn.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Query{/tr}</td>
			<td>
				<textarea id="metric_query" name="metric_query" rows="10">{$metric_query|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="metric_submit" value="{if !isset($metric_id)}Create Metric{else}Edit Metric{/if}" /></td>
		</tr>
	</table>
	<input type="hidden" name="metric_id" value="{$metric_id|escape}">
</form>


<h2 id="editcreatetab">
	{if !isset($tab_id)}
		{tr}Create new tab{/tr}
	{else}
		{tr}Edit this tab:{/tr} {$tab_name|escape}
	{/if}
</h2>

<form name="edittab" method="post" action="tiki-admin_metrics.php#editcreatetab">
	<table id="admin_metrics_add_tab" class="formcolor">
		<tr class="{cycle}">
			<td>{tr}Name (must be unique){/tr}</td>
			<td><input type="text" name="tab_name" value="{$tab_name|escape}" /></td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Weight (must be integer){/tr}</td>
			<td><input type="text" name="tab_order" value="{$tab_order|escape}" /></td>
		</tr>
		<tr class="{cycle}">
			<td>{tr}Content{/tr}</td>
			<td>
				<textarea id="tab_content" name="tab_content" rows="10">{$tab_content|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="tab_submit" value="{if !isset($tab_id)}Create Tab{else}Edit Tab{/if}" /></td>
		</tr>
	</table>
	<input type="hidden" name="tab_id" value="{$tab_id|escape}">
</form>
