{*Smarty template*}
<a class="pagetitle" href="tiki-eph.php">{tr}Ephemerides{/tr}</a><br /><br />
<table>
<tr>
	<td>
	<!-- Calendar -->
	  {include file=modules/mod-calendar.tpl}
	</td>
	
	<td>
	<!-- Form to upload/edit -->
	  <b>{$pdate|tiki_long_date}</b>
	</td>
</tr>
</table>

<h3>{tr}Ephemerides{/tr}</h3>
{if $tiki_p_admin eq 'y'}
<a class="linkbut" href="tiki-eph_admin.php">{tr}Admin{/tr}</a>
{/if}
<table>
<tr>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading">{tr}data{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].title}</td>
<td class="{cycle advance=false}">
	<table>
	<tr>
	{if $channels[user].filesize}
	<td><img alt="image" src="tiki-view_eph.php?ephId={$channels[user].ephId}" /></td>
	{/if}
	<td>{$channels[user].textdata}</td>
	<tr>
	</table>
</td>
</tr>
{/section}
</table>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-userfiles.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-userfiles.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-userfiles.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
 
