<h1><a class="pagetitle" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}">{tr}Admin HTML Page Dynamic Zones{/tr}</a>




<br />
<h2>{tr}Page{/tr}: {$pageName}</h2><br /><br />
<a class="linkbut" href="tiki-admin_html_pages.php">{tr}Admin HTML pages{/tr}</a>
<a class="linkbut" href="tiki-admin_html_pages.php?pageName={$pageName|escape:"url"}">{tr}Edit this page{/tr}</a>
<a class="linkbut" href="tiki-page.php?pageName={$pageName|escape:"url"}">{tr}View page{/tr}</a></h1>

{if $zone}
<h2>{tr}Edit zone{/tr}</h2>
<form action="tiki-admin_html_page_content.php" method="post">
<input type="hidden" name="pageName" value="{$pageName|escape}" />
<input type="hidden" name="zone" value="{$zone|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Zone{/tr}:</td><td class="formcolor">{$zone}</td></tr>
<tr><td class="formcolor">{tr}Content{/tr}:</td><td class="formcolor">
{if $type eq 'ta'}
<textarea rows="5" cols="60" name="content">{$content|escape}</textarea>
{else}
<input type="text" name="content" value="{$content|escape}" />
{/if}
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}

<h2>{tr}Dynamic zones{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_html_page_content.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="pageName" value="{$pageName|escape}" />
   </form>
   </td>
</tr>
</table>
<form action="tiki-admin_html_page_content.php" method="post">
<input type="hidden" name="pageName" value="{$pageName|escape}" />
<input type="hidden" name="zone" value="{$zone|escape}" />
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'zone_desc'}zone_asc{else}zone_desc{/if}">{tr}zone{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'content_desc'}content_asc{else}content_desc{/if}">{tr}content{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].zone}</td>
<!--<td class="odd">{$channels[user].content|truncate:250:"(...)":true}</td>-->
<td class="{cycle advance=false}">
{if $channels[user].type eq 'ta'}
<textarea name="{$channels[user].zone|escape}" cols="20" rows="4">{$channels[user].content|escape}</textarea>
{else}
<input type="text" name="{$channels[user].zone|escape}" value="{$channels[user].content|escape}" />
{/if}
</td>
<td class="{cycle advance=true}">
   <a title="{tr}Edit{/tr}" class="link" href="tiki-admin_html_page_content.php?pageName={$pageName|escape:"url"}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;zone={$channels[user].zone}">{icon _id='page_edit'}</a>
</td>
</tr>
{/section}
</table>
<div align="center">
<input type="submit" name="editmany" value="{tr}Mass update{/tr}" />
</div>
</form>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_html_page_content.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_html_page_content.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_html_page_content.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

