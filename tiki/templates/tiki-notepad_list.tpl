{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_list.php">{tr}Notes{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/>
<div align="center">
<table border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table border='0' height='10' cellpadding='0' cellspacing='0' 
			       width='200' style='background-color:#666666;'>
				<tr>
					<td style='background-color:red;' width='{$cellsize}'>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td>
			<small>{$percentage}%</small>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<small>{tr}quota{/tr}</small>
		</td>
	</tr>
</table>
</div>
<br/>
<a class="link" href="tiki-notepad_write.php">[{tr}Write a note{/tr}]</a>
<table>
<tr><td class="findtable">
   <td class="findtable">
   <form method="get" action="tiki-notepad_list.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<form action="tiki-notepad_list.php" method="post">
<table class="normal">
<tr>
<td class="heading"><input type="submit" name="delete" value="{tr}x{/tr} " /></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
<td class="heading" >{tr}Size{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<input type="checkbox" name="note[{$channels[user].noteId}]" />
</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{$channels[user].name}</a>
(<a href="tiki-notepad_get?noteId={$channels[user].noteId}" class="link">save</a>)</td>
<td class="{cycle advance=false}">{$channels[user].lastModif|tiki_short_datetime}</td>
<td class="{cycle}">{$channels[user].size|kbsize}</td>
</tr>
{sectionelse}
<tr>
	<td class="heading" colspan="4">{tr}No notes yet{/tr}</td>
</tr>
{/section}
<tr>
	<td class="heading" colspan="4"><input type="submit" name="merge" value="{tr}merge notes into{/tr}" /><input type="text" name="merge_name" size="10" /></td>
</tr>
</table>
</form>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

<h3>{tr}Upload file{/tr}</h3>
<form enctype="multipart/form-data" action="tiki-notepad_list.php" method="post">
<table class="normal">
<!--
<tr>
  <td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td>
</tr>
-->
<tr>
  <td class="formcolor">{tr}Upload file{/tr}:</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="10000000000000"><input size="16" name="userfile1" type="file"><input style="font-size:9px;" type="submit" name="upload" value="{tr}upload{/tr}" /></td>
</tr>
</table>
</form>
