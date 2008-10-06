{title url="tiki-lastchanges.php?days=$days"}{tr}Last Changes{/tr}{/title}
<div class="navbar">
<a {if $days eq '1'}id="highlight"{/if} href="tiki-lastchanges.php?days=1">{tr}Today{/tr}</a>
<a {if $days eq '2'}id="highlight"{/if} href="tiki-lastchanges.php?days=2">{tr}Last{/tr} 2 {tr}days{/tr}</a>
<a {if $days eq '3'}id="highlight"{/if} href="tiki-lastchanges.php?days=3">{tr}Last{/tr} 3 {tr}days{/tr}</a>
<a {if $days eq '5'}id="highlight"{/if} href="tiki-lastchanges.php?days=5">{tr}Last{/tr} 5 {tr}days{/tr}</a>
<a {if $days eq '7'}id="highlight"{/if} href="tiki-lastchanges.php?days=7">{tr}Last{/tr} {tr}week{/tr}</a>
<a {if $days eq '14'}id="highlight"{/if} href="tiki-lastchanges.php?days=14">{tr}Last{/tr} 2 {tr}weeks{/tr}</a>
<a {if $days eq '31'}id="highlight"{/if} href="tiki-lastchanges.php?days=31">{tr}Last{/tr} {tr}month{/tr}</a>
<a {if $days eq '0'}id="highlight"{/if} href="tiki-lastchanges.php?days=0">{tr}All{/tr}</a>
</div>
{if $lastchanges or ($find ne '')}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-lastchanges.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="days" value="0" />
   </form>
   </td>
{if $findwhat != ""}
   <td>
   <a href="tiki-lastchanges.php" class="wiki">{tr}Search by Date{/tr}</a>
   </td>
{/if}   
</tr>
</table>
{/if}
<br />
{if $findwhat!=""}
{tr}Found{/tr} "<b>{$findwhat}</b>" {tr}in{/tr} {$cant_records} {tr}LastChanges{/tr} 
{/if}
<div align="left">
<table class="normal">
<tr>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Date{/tr}{/self_link}</th>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='object'}{tr}Page{/tr}{/self_link}</th>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='action'}{tr}Action{/tr}{/self_link}</th>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='user'}{tr}User{/tr}{/self_link}</th>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='ip'}{tr}Ip{/tr}{/self_link}</th>
<th class="heading">{self_link _sort_arg='sort_mode' _sort_field='comment'}{tr}Comment{/tr}{/self_link}</th>
<th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$lastchanges}
<tr class="{cycle}">
<td>{$lastchanges[changes].lastModif|tiki_short_datetime}</td>
<td><a href="{$lastchanges[changes].pageName|sefurl}" class="tablename" title="{$lastchanges[changes].pageName}">{$lastchanges[changes].pageName|truncate:$prefs.wiki_list_name_len:"...":true|escape}</a> 
</td>

<td>{tr}{$lastchanges[changes].action|escape}{/tr}</td>
<td>{$lastchanges[changes].user|userlink}</td>
<td>{$lastchanges[changes].ip}</td>
<td>{$lastchanges[changes].comment}</td>
<td>
{if $tiki_p_wiki_view_history eq 'y'} 
{if $lastchanges[changes].version}
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{icon _id='page_white_stack' alt='{tr}History{/tr}'}</a>{tr}v{/tr}{$lastchanges[changes].version}
&nbsp;<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;preview={$lastchanges[changes].version}' title="{tr}View{/tr}">v</a>&nbsp;
{if $tiki_p_rollback eq 'y'}
<a class="link" href='tiki-rollback.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;version={$lastchanges[changes].version}' title="{tr}Rollback{/tr}">b</a>&nbsp;
{/if}
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff={$lastchanges[changes].version}' title="{tr}Compare{/tr}">c</a>&nbsp;
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;diff2={$lastchanges[changes].version}' title="{tr}Diff{/tr}">d</a>&nbsp;
{if $tiki_p_wiki_view_source eq 'y'}
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}&amp;source={$lastchanges[changes].version}' title="{tr}Source{/tr}">s</a>{/if}
{elseif $lastchanges[changes].versionlast}
<a class="link" href='tiki-pagehistory.php?page={$lastchanges[changes].pageName|escape:"url"}'>{icon _id='page_white_stack' alt='{tr}History{/tr}'}</a>
{/if}
{/if}
</td>

</tr>
{sectionelse}
<tr><td class="even" colspan="7">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
{pagination_links cant=$cant_records step=$prefs.maxRecords offset=$offset}{/pagination_links}
</div>
