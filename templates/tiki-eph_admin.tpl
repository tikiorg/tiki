{*Smarty template*}
<h1><a class="pagetitle" href="tiki-eph_admin.php">{tr}Admin ephemerides{/tr}</a>
      {if $feature_help eq 'y'}
<a href="{$helpurl}Ephemerides" target="tikihelp" class="tikihelp" title="{tr}Ephemerides{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-eph_admin.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Ephemerides tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}</h1>


<table class="normal">
<tr>
	<td class="formcolor">
	<!-- Calendar -->
	  {include file=modules/mod-calendar.tpl}
	</td>
	
	<td class="formcolor">
	<!-- Form to upload/edit -->
	  <b>{$pdate|tiki_long_date}</b>
	  <form enctype="multipart/form-data" action="tiki-eph_admin.php" method="post">
		<input type="hidden" name="ephId" value="{$ephId|escape}" />
		<input type="hidden" name="day" value="{$day|escape}" />
		<input type="hidden" name="mon" value="{$mon|escape}" />
		<input type="hidden" name="year" value="{$year|escape}" />
		<table class="normal">
		<tr>
			<td class="formcolor">{tr}Title{/tr}</td>
			<td class="formcolor"><input size="20" type="text" name="title" value="{$info.title|escape}" /></td>
		</tr>
		<tr>
			<td class="formcolor">{tr}Text{/tr}</td>
			<td class="formcolor"><textarea rows="5" cols="20" name="textdata">{$info.textdata|escape}</textarea></td>
		
		</tr>
		<tr>
		  <td class="formcolor">{tr}Upload image{/tr}:</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="8" name="userfile1" type="file" /><input style="font-size:9px;" type="submit" name="upload" value="{tr}upload{/tr}" /></td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>


<h3>{tr}All ephemerides{/tr}</h3>
<a class="linkbut" href="tiki-eph.php">{tr}Browse{/tr}</a>
<form action="tiki-eph_admin.php" method="post">
<table class="normal">
<tr>
<td class="heading"><input type="submit" name="delete" value="{tr}x{/tr}" /></td>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}title{/tr}</a></td>
<td class="heading">{tr}data{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="ephitem[{$channels[user].ephId}]" />
</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-eph_admin.php?find={$find}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;ephId={$channels[user].ephId}">{$channels[user].title}</a></td>
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
</form>
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
