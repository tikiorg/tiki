{* $Id$ *}
<h1><a class="pagetitle" href="tiki-list_posts.php">{tr}Blogs{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Blogs" target="tikihelp" class="tikihelp" title="{tr}Blogs{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-list_posts.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}List Posts Tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit Tpl{/tr}'}</a>{/if}
</h1>

<div class="navbar">
<a class="linkbut" href="tiki-edit_blog.php">{tr}Edit Blog{/tr}</a>
<a class="linkbut" href="tiki-blog_post.php">{tr}Post{/tr}</a>
<a class="linkbut" href="tiki-list_blogs.php">{tr}List Blogs{/tr}</a>
</div>

<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-list_posts.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'postId_desc'}postId_asc{else}postId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading">{tr}Blog Title{/tr}</td>
<td class="heading"><a class="tableheading" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading">{tr}Size{/tr}</td>
<td class="heading"><a class="tableheading" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].postId}&nbsp;</td>
<td class="odd">&nbsp;<a class="blogname" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}" title="{$listpages[changes].blogTitle}">{$listpages[changes].blogTitle|truncate:$prefs.blog_list_title_len:"...":true}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].created|tiki_short_datetime}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
<a class="link" href="tiki-blog_post.php?postId={$listpages[changes].postId}">{icon _id='page_edit'}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].postId}&nbsp;</td>
<td class="even">&nbsp;<a class="blogname" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}" title="{$listpages[changes].blogTitle}">{$listpages[changes].blogTitle|truncate:$prefs.blog_list_title_len:"...":true}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].created|tiki_short_datetime}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-blog_post.php?postId={$listpages[changes].postId}">{icon _id='page_edit'}</a>
<a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].postId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_posts.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_posts.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-list_posts.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
