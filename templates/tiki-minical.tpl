{*Smarty template*}
<a class="pagetitle" href="tiki-minical.php?view={$view}">{tr}Mini Calendar{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/>
[<a class="link" href="#add">{tr}Add{/tr}</a>]
[<a class="link" href="tiki-minical_prefs.php">{tr}Prefs{/tr}</a>]
[<a class="link" href="tiki-minical.php?view=daily">{tr}Daily{/tr}</a> | 
<a class="link" href="tiki-minical.php?view=weekly">{tr}Weekly{/tr}</a>]
<br/><br/>

<!-- Time here -->
<!--[{$pdate_h|date_format:"%H"}:{$pdate_h|date_format:"%M"}]-->

{cycle values="odd,even" print=false}

{if $view eq 'daily'}
<b><a class="link" href="tiki-minical.php?view={$view}&amp;day={$yesterday|date_format:"%d"}&amp;mon={$yesterday|date_format:"%m"}&amp;year={$yesterday|date_format:"%Y"}">&lt;</a>
{$pdate|tiki_long_date} 
<a class="link" href="tiki-minical.php?view={$view}&amp;day={$tomorrow|date_format:"%d"}&amp;mon={$tomorrow|date_format:"%m"}&amp;year={$tomorrow|date_format:"%Y"}">&gt;</a>
</b>
<table clas="normal" width="97%" >
{section name=ix loop=$slots}
<tr>
	<td class="{cycle}">
	    <table>
	    <tr>
	    <td>
    	{$slots[ix].start|tiki_short_time}<!--<br/>{$slots[ix].end|tiki_short_time}-->
    	</td>
    	<td>
    	{section name=jj loop=$slots[ix].events}
    	<a title="{$slots[ix].events[jj].start|tiki_short_time}:{$slots[ix].events[jj].description}" class="link" href="tiki-minical.php?view={$view}&amp;eventId={$slots[ix].events[jj].eventId}">{$slots[ix].events[jj].title}</a>
    	[<a class="link" href="tiki-minical.php?view={$view}&amp;remove={$slots[ix].events[jj].eventId}">x</a>]
    	<br/>
    	{/section}
    	</td>
    	</tr>
    	</table>
	</td>
</tr>
{/section}
</table>
{/if}

{if $view eq 'weekly'}
<a class="link" href="tiki-minical.php?view={$view}&amp;day={$prev_week_start|date_format:"%d"}&amp;mon={$prev_week_start|date_format:"%m"}&year={$prev_week_start|date_format:"%Y"}">&lt;</a>
<b>{$week_start|date_format:"%b"} {$week_start|date_format:"%d"}-{$week_end|date_format:"%b"} {$week_end|date_format:"%d"}</b>
<a class="link" href="tiki-minical.php?view={$view}&amp;day={$next_week_start|date_format:"%d"}&amp;mon={$next_week_start|date_format:"%m"}&year={$next_week_start|date_format:"%Y"}">&gt;</a>
<table class="normal" width="97%" >
{section name=ix loop=$slots}
<tr>
	<td class="{cycle}">
	    <table width="100%">
	    <tr>
	    <td width="7%">
    	{$slots[ix].start|date_format:"%a"}<br/>
    	{$slots[ix].start|date_format:"%d"}
    	</td>
    	<td>
    	{section name=jj loop=$slots[ix].events}
    	{$slots[ix].events[jj].start|tiki_short_time}: <a title="{$slots[ix].events[jj].start|tiki_short_time}:{$slots[ix].events[jj].description}" class="link" href="tiki-minical.php?view={$view}&amp;eventId={$slots[ix].events[jj].eventId}">{$slots[ix].events[jj].title}</a>
    	[<a class="link" href="tiki-minical.php?view={$view}&amp;remove={$slots[ix].events[jj].eventId}">x</a>]
    	<br/>
    	{/section}
    	</td>
    	</tr>
    	</table>
	</td>
</tr>
{/section}
</table>
{/if}

<a name="add"></a>
<h3>{tr}Add an event{/tr}</h3>
<form action="tiki-minical.php" method="post">
<input type="hidden" name="eventId" value="{$eventId}" />
<input type="hidden" name="duration" value="2" />
<input type="hidden" name="description" value="dummy" />
<table class="normal">
  <tr><td class="formcolor">{tr}Title{/tr}</td>
      <td class="formcolor"><input type="text" name="title" value="{$info.title}" /><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
  <tr>
  	  <td class="formcolor">{tr}Start{/tr}</td>
  	  <td class="formcolor">
  	  {html_select_date time=$ev_pdate}
  	  {html_select_time time=$ev_pdate_h display_seconds=false use_24_hours=true}
  	  </td>
  </tr>
</table>
</form>

  
 
