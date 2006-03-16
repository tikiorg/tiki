{*Smarty template*}
<h1><a class="pagetitle" href="tiki-userfiles.php">{tr}User Files{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Files" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit user files{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-userfiles.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit quiz stats tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' />
</a>
{/if}</h1>

<!-- this bar is created by a ref to {include file=tiki-mytiki_bar.tpl} :) -->
{include file=tiki-mytiki_bar.tpl}
<h2>{tr}User Files{/tr}</h2>

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
			<small>{tr}quota{/tr}</small>
		</td>
	</tr>
</table>
</div>

<form action="tiki-userfiles.php" method="post">
<input type="submit" name="delete" value="{tr}delete{/tr}" />
<table class="normal">
<tr>
<td style="text-align:center;" class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-userfiles.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}size{/tr}</a></td>
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
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-userfiles.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>


<h2>{tr}Upload file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-userfiles.php" method="post">
<table class="normal">
<!--
<tr>
  <td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td>
</tr>
-->
<!--You've got to see how easy it is to add more to the smarty code to get more upload areas
made the input size legible for longer file names-->
<tr>
  <td class="formcolor">{tr}Upload file{/tr}:</td><td class="formcolor">
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="80" name="userfile1" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="80" name="userfile2" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="80" name="userfile3" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="80" name="userfile4" type="file" /><br />
  <input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="80" name="userfile5" type="file" /><br />
  <input style="font-size:9px;" type="submit" name="upload" value="{tr}upload{/tr}" />
  </td>
</tr>
<tr>

</tr>
</table>
</form>

