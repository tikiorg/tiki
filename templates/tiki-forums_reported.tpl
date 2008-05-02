{popup_init src="lib/overlib.js"}
{*Smarty template*}
<h1><a class="pagetitle" href="tiki-forums_reported.php?forumId={$forumId}">{tr}Reported messages for{/tr}: {$forum_info.name}</a>
</h1>
<a class="link" href="tiki-view_forum.php?forumId={$forumId}">{tr}back to forum{/tr}</a>
<br />
<h2>{tr}List of messages{/tr} ({$cant})</h2>
{* FILTERING FORM *}
{if $items or ($find ne '')}
<form action="tiki-forums_reported.php" method="post">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr>
<td>
	<small>{tr}Find{/tr}</small>
	<input size="8" type="text" name="find" value="{$find|escape}" />
	<input type="submit" name="filter" value="{tr}Filter{/tr}" />
</td>
</tr>
</table>	
</form>
{/if}
{*END OF FILTERING FORM *}

{*LISTING*}
<form action="tiki-forums_reported.php" method="post">
<input type="hidden" name="forumId" value="{$forumId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<table class="normal">
<tr>
{if $items}
<th class="heading" ></th>
{/if}
<th class="heading">{tr}Message{/tr}</th>
<th class="heading">{tr}Reported by{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$items}
<tr>
	<td style="text-align:center;" class="{cycle advance=false}">
	  <input type="checkbox" name="msg[{$items[ix].threadId}]" />
	</td>
  
	<td class="{cycle advance=false}" style="text-align:left;">
		<a class="link" href="tiki-view_forum_thread.php?topics_offset=0&amp;topics_sort_mode=commentDate_desc&amp;topics_threshold=0&amp;topics_find=&amp;forumId={$items[ix].forumId}&amp;comments_parentId={$items[ix].parentId}">{$items[ix].title}</a>
	</td>
	
	<td class="{cycle}" style="text-align:left;">
		{$items[ix].user|default:'{tr}Anonymous{/tr}'}
	</td>

</tr>
{sectionelse}
<tr>
	<td class="{cycle advance=false}" colspan="2">
	{tr}No records to display{/tr}
	</td>
</tr>	
{/section}
</table>
{if $items}
{tr}Perrom action with checked:{/tr} <input type="submit" name="del" value=" {tr}Delete{/tr} " />
{/if}

</form>
{* END OF LISTING *}

{* PAGINATION *}
<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-forums_reported.php?forumId={$forumId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-forums_reported.php?forumId={$forumId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-forums_reported.php?forumId={$forumId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
{* END OF PAGINATION *}
