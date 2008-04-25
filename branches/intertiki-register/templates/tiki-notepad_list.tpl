<h1><a class="pagetitle" href="tiki-notepad_list.php">{tr}Notes{/tr}</a></h1>
{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
{/if}

<div class="navbar"><span class="button2"><a class="linkbut" href="tiki-notepad_write.php">{tr}Write a note{/tr}</a></span></div>

<div style="text-align:center;">
 <div style="height:20px; width:200px; border:1px solid black; background-color:#666666; text-align:left; margin:0 auto;">
    <div style="background-color:red; height:100%; width:{$cellsize}px;"> 
    </div>
  </div>
<small>{tr}quota{/tr}&nbsp;{$percentage}%</small>
</div>

{if count($channels) > 0}
<h2>{tr}Notes{/tr}</h2>
<table>
<tr><td class="findtable">
   <form method="get" action="tiki-notepad_list.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<form action="tiki-notepad_list.php" method="post">
<table class="normal">
<tr>
<td style="text-align:center;" class="heading"><input type="submit" name="delete" value="{tr}x{/tr} " /></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'parse_mode_desc'}parse_mode_asc{else}parse_mode_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
<td style="text-align:right;" class="heading" ><a class="tableheading" href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a></td>
<td style="text-align:center;" class="heading" >{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td style="text-align:center;" class="{cycle advance=false}">
<input type="checkbox" name="note[{$channels[user].noteId}]" />
</td>
<td class="{cycle advance=false}"><a class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}">{$channels[user].name}</a></td>
<td class="{cycle advance=false}">{$channels[user].parse_mode}</td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$channels[user].lastModif|tiki_short_datetime}</td>
<td style="text-align:right;"  class="{cycle advance=false}">{$channels[user].size|kbsize}</td>
<td style="text-align:center;"  class="{cycle}">
<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}" class="link">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
<a href="tiki-notepad_write.php?noteId={$channels[user].noteId}" class="link">{icon _id='page_edit'}</a>
<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}&amp;save=1" class="link">{icon _id='disk' alt="{tr}Save{/tr}"}</a>
<a style="margin-left:10px;" class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}&amp;remove=1">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
</td>
</tr>
{sectionelse}
<tr>
	<td class="formcolor" colspan="4">{tr}No notes yet{/tr}</td>
</tr>
{/section}
<tr>
	<td class="formcolor" colspan="4"><input type="submit" name="merge" value="{tr}merge selected notes into{/tr}" /><input type="text" name="merge_name" size="20" /></td>
</tr>
</table>
</form>
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-notepad_list.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{/if}

<h2>{tr}Upload file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-notepad_list.php" method="post">
<table class="normal"><tr>
<td class="formcolor">{tr}Upload file{/tr}:</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile1" type="file" /><input style="font-size:9px;" type="submit" name="upload" value="{tr}Upload{/tr}" /></td>
</tr>
</table>
</form>
