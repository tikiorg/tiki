{*Smarty template*}
<a class="pagetitle" href="tiki-minical.php">{tr}Mini Calendar{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/>

{cycle values="odd,even" print=false}
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
    	{$slots[ix].events[jj].title}<br/>
    	{/section}
    	</td>
    	</tr>
    	</table>
	</td>
</tr>
{/section}
</table>


<h3>{tr}Add an event{/tr}</h3>
<form action="tiki-minical.php" method="post">
<input type="hidden" name="eventId" value="{$eventId}" />
<input type="hidden" name="duration" value="2" />
<input type="hidden" name="description" value="dummy" />
<table class="normal">
  <tr><td class="formcolor">{tr}Title{/tr}</td>
      <td class="formcolor"><input type="text" name="title" value="{$info.title}" /></td>
  </tr>
  <tr>
  	  <td class="formcolor">{tr}Start{/tr}</td>
  	  <td class="formcolor">
  	  {html_select_time time=$pdate_h display_seconds=false use_24_hours=true}
  	  </td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>

  
 
