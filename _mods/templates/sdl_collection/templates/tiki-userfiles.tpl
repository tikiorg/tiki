{*Smarty template*}
<a class="pagetitle" href="tiki-userfiles.php">{tr}User Files{/tr}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<h3>{tr}User Files{/tr}</h3>

<div align="center">
<table border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table border='0' height='20' cellpadding='0' cellspacing='0' 
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
			<small>{tr}Quota{/tr}</small>
		</td>
	</tr>
</table>
</div>

<form action="tiki-userfiles.php" method="post">
<input type="submit" name="delete" value="{tr}Delete{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this file?{/tr}')"/>
<table class="normal">
<tr>
<td style="text-align:center;" class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td style="text-align:center;" class="{cycle advance=false}">
<input type="checkbox" name="userfile[{$channels[user].fileId}]" />
</td>
<td class="{cycle advance=false}">{$channels[user].filename|iconify}<a class="link" href="tiki-download_userfile.php?fileId={$channels[user].fileId}">{$channels[user].filename}</a></td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td style="text-align:right;" class="{cycle}">{$channels[user].filesize|kbsize}</td>
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
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-userfiles.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h3>{tr}Upload File{/tr}</h3>
<form enctype="multipart/form-data" action="tiki-userfiles.php" method="post">
<table class="normal">
<!--
<tr>
  <td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td>
</tr>
-->
<tr>
  <td class="formcolor">{tr}Upload File{/tr}:</td><td class="formcolor">
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile1" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile2" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile3" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile4" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile5" type="file" /><br />
  <input style="font-size:9px;" type="submit" name="upload" value="{tr}Upload{/tr}" />
  </td>
</tr>
<tr>

</tr>
</table>
</form>

