{*Smarty template*}
<a class="pagetitle" href="tiki-minical_prefs.php">{tr}Mini Calendar: Preferences{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/>
[<a class="link" href="tiki-minocal.php#add">{tr}Add{/tr}</a>]
[<a class="link" href="tiki-minical_prefs.php">{tr}Prefs{/tr}</a>]
[<a class="link" href="tiki-minical.php?view=daily">{tr}Daily{/tr}</a> | 
<a class="link" href="tiki-minical.php?view=weekly">{tr}Weekly{/tr}</a>]
<br/>
<h3>{tr}Preferences{/tr}</h3>
<form action="tiki-minical_prefs.php" method="post">
<table class="normal">
<tr>
	<td class="formcolor">{tr}Calendar Interval in daily view{/tr}</td>
	<td class="formcolor">
	<select name="minical_interval">
	<option value="300" {if $minical_interval eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
	<option value="600" {if $minical_interval eq 600}selected="selected"{/if}>10 {tr}minutes{/tr}</option>
	<option value="900" {if $minical_interval eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
	<option value="1800" {if $minical_interval eq 1800}selected="selected"{/if}>30 {tr}minutes{/tr}</option>
	<option value="3600" {if $minical_interval eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
	</select>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Start hour for days{/tr}</td>
	<td class="formcolor">
	<select name="minical_start_hour">
	{html_options output=$hours values=$hours selected=$minical_start_hour}
	</select>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}End hour for days{/tr}</td>
	<td class="formcolor">
	<select name="minical_end_hour">
	{html_options output=$hours values=$hours selected=$minical_end_hour}
	</select>
	</td>
</tr>
<tr>
	<td class="formcolor">&nbsp;</td>
	<td class="formcolor">
		<input type="submit" name="save" value="{tr}save{/tr}" />
	</td>
</tr>	
</table>
</form>
 
