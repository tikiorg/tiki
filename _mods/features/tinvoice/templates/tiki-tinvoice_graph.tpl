<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_edit.php?id_emitter={$me_tikiid}">{tr}create new invoice{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}Invoices preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_list.php">{tr}list Invoices {/tr}</a>
</div>

<hr />
<div id="tinvoice-chart">
Choose your graph: <form id="filter" action="tiki-tinvoice_graph.php" onChange="submit();" method="post"><select name="filter">
		<option >Filter ---</option>
		<option {if $filter eq "Invoices"} selected {/if}>Invoices</option>
		<option  {if $filter eq "Paiements"} selected {/if}>Paiements</option>
		<option  {if $filter eq "Expenses"} selected {/if}>Expenses</option>
		<option  {if $filter eq "Clients"} selected {/if}>Clients</option>
		<option  {if $filter eq "Suppliers"} selected {/if}>Suppliers</option>
	</select>
<div class="button2" style="width: 150px; float: right;">{jscalendar id="start" date=$todate fieldname="todate" align="Bc" showtime='n'}
</div>

</form>
{if $filter && $filter eq 'Invoices'}<hr />
<h3>{tr}{$filter} Graphs{/tr}</h3>

<div class="navbar" style="height: 36px;">
	<div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut" {if $graphPeriod eq "week"} style="background-color: orange" {/if} 
	{ajax_href template=tiki-tinvoice_graph.tpl
	htmlelement=tinvoice-graph}tiki-tinvoice_graph.php?graphPeriod=week&todate={$todate}&filter={$filter}{/ajax_href}>{tr}Week{/tr}</a>&nbsp;
	</div><div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "month"} style="background-color: orange" {/if}
	{ajax_href template=tiki-tinvoice_graph.tpl
	htmlelement=tinvoice-graph}tiki-tinvoice_graph.php?graphPeriod=month&todate={$todate}&filter={$filter}{/ajax_href}>{tr}Month{/tr}</a>&nbsp;
	</div><div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "trimester"} style="background-color: orange" {/if} href="tiki-tinvoice_graph.php?graphPeriod=trimester&todate={$todate}&filter={$filter}" onclick="toggle('xtype');">{tr}Trimester{/tr}</a>&nbsp;
	<form id="xtype" method="get" action="tiki-tinvoice_graph.php" name="f" style="text-align: left; width: 220px; border: 1px solid black; display: {if $graphPeriod eq "trimester" || $graphPeriod eq "semester" || $graphPeriod eq "year"}block{else}none{/if};">
<input type="hidden" name="graphPeriod" value="{$graphPeriod}" />
<input type="hidden" name="todate" value="{$todate}" />
<input type="hidden" name="filter" value="{$filter}" />
<div class="calcheckbox"><span>View by</span><input type="radio" name="xtype" {if $xtype eq "day"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="day" id="xtype_1"  />
	<label for="xtype_1" class="linkbut">Day</label>
<input type="radio" name="xtype" {if $xtype eq "week"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="week" id="xtype_2"  />
	<label for="xtype_2" class="linkbut">Week</label>
<input type="radio" name="xtype" {if $xtype eq "month"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="month" id="xtype_3"  />
	<label for="xtype_3" class="linkbut">Month</label>
</div>
</form>
</div><div  class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "semester"} style="background-color: orange" {/if} href="tiki-tinvoice_graph.php?graphPeriod=semester&todate={$todate}&filter={$filter}" onclick="toggle('xtype');">{tr}Semester{/tr}</a>&nbsp;
	</div><div class="button2"  style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "year"} style="background-color: orange" {/if} href="tiki-tinvoice_graph.php?graphPeriod=year&todate={$todate}&filter={$filter}" onclick="toggle('xtype');">{tr}Year{/tr}</a>&nbsp;
	</div></div>
<div class="navbar">
	<a class="linkbut" href="tiki-tinvoice_graph.php?graphPeriod={$graphPeriod}&todate={$prev}&filter={$filter}" alt="{tr}previous{/tr}">{tr}<< Previous {$graphPeriod}{/tr}</a>&nbsp;
	<a class="linkbut" href="tiki-tinvoice_graph.php?graphPeriod={$graphPeriod}&todate={$next}&filter={$filter}" alt="{tr}next{/tr}">{tr}Next {$graphPeriod}>>{/tr}</a>&nbsp;
</div>

<div id="divchart" align="center">
	<img id="chart" src="tiki-tinvoice_chart.php?graphPeriod={$graphPeriod}&todate={$todate}&filter={$filter}"  border=0 alt="tinvoice graphs" />
</div>
{/if}
</div>
