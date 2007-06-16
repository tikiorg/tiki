<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_edit.php?id_emitter={$me_tikiid}">{tr}create new invoice{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}Invoices preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_list.php">{tr}list Invoices {/tr}</a>
</div>

<hr />
<div class="navbar" style="height: 36px;"><div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut" {if $graphPeriod eq "week"} style="background-color: orange" {/if} href="tiki-tinvoice_list.php?graphPeriod=week&todate={$todate}">{tr}Week{/tr}</a>&nbsp;
	</div><div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "month"} style="background-color: orange" {/if} href="tiki-tinvoice_list.php?graphPeriod=month&todate={$todate}">{tr}Month{/tr}</a>&nbsp;
	</div><div class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "trimester"} style="background-color: orange" {/if} href="tiki-tinvoice_list.php?graphPeriod=trimester&todate={$todate}" onclick="toggle('xtype');">{tr}Trimester{/tr}</a>&nbsp;
	<form id="xtype" method="get" action="tiki-tinvoice_list.php" name="f" style="text-align: left; width: 220px; border: 1px solid black; display: {if $graphPeriod eq "trimester" || $graphPeriod eq "semester" || $graphPeriod eq "year"}block{else}none{/if};">
<input type="hidden" name="graphPeriod" value="{$graphPeriod}" />
<input type="hidden" name="todate" value="{$todate}" />
<div class="calcheckbox"><span>View by</span><input type="radio" name="xtype" {if $xtype eq "day"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="day" id="xtype_1"  />
	<label for="xtype_1" class="linkbut">Day</label>
<input type="radio" name="xtype" {if $xtype eq "week"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="week" id="xtype_2"  />
	<label for="xtype_2" class="linkbut">Week</label>
<input type="radio" name="xtype" {if $xtype eq "month"}checked=checked {/if}  onclick="document.forms['xtype'].submit();" value="month" id="xtype_3"  />
	<label for="xtype_3" class="linkbut">Month</label>
</div>
</form>
</div><div  class="button2" style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "semester"} style="background-color: orange" {/if} href="tiki-tinvoice_list.php?graphPeriod=semester&todate={$todate}" onclick="toggle('xtype');">{tr}Semester{/tr}</a>&nbsp;
	</div><div class="button2"  style="text-align: center; width: 70px; float: left;"><a class="linkbut"  {if $graphPeriod eq "year"} style="background-color: orange" {/if} href="tiki-tinvoice_list.php?graphPeriod=year&todate={$todate}" onclick="toggle('xtype');">{tr}Year{/tr}</a>&nbsp;
	</div><div class="button2"  style="text-align: center; width: 100px; float: left;"><select name="graphFilter">
		<option >Filter ---</option>
		<option >Invoices</option>
		<option >Paiements</option>
		<option >Expenses</option>
	</select>
</div><div class="button2" style="text-align: center; width: 125px; float: left;">{jscalendar id="start" date=$todate fieldname="todate" align="Bc" showtime='n'}
</div>
</div>

<div class="navbar">
	<a class="linkbut" href="tiki-tinvoice_list.php?graphPeriod={$graphPeriod}&todate={$prev}" alt="{tr}previous{/tr}">{tr}<< Previous {$graphPeriod}{/tr}</a>&nbsp;
	<a class="linkbut" href="tiki-tinvoice_list.php?graphPeriod={$graphPeriod}&todate={$next}" alt="{tr}next{/tr}">{tr}Next {$graphPeriod}>>{/tr}</a>&nbsp;
</div>

<div align="center">
	<img src="tiki-tinvoice_chart.php?graphPeriod={$graphPeriod}&todate={$todate}"  border=0 alt="tinvoice graphs" />
</div>
<hr />
