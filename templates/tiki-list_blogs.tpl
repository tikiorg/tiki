<h2><a class="pagetitle" href="tiki-list_blogs.php">{tr}Blogs{/tr}</a></h2>
{if $tiki_p_create_blogs eq 'y'}
<a class="link" href="tiki-edit_blog.php">edit blog</a>
{/if}
<br/><br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_blogs.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'title_desc'}title_asc{else}title_desc{/if}">{tr}Title{/tr}</a></td>
<td class="heading">{tr}Description{/tr}</td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'public_desc'}public_asc{else}public_desc{/if}">{tr}Public{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'posts_desc'}posts_asc{else}posts_desc{/if}">{tr}Posts{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'activity_desc'}activity_asc{else}activity_desc{/if}">{tr}Activity{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].title|truncate:20:"(...)":true}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].public}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].posts}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].hits}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].activity}&nbsp;</td>
<td class="odd">
{if $tiki_p_blog_post eq 'y'}
{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y') or ($listpages[changes].public eq 'y')}
<a class="link" href="tiki-blog_post.php?blogId={$listpages[changes].blogId}">{tr}Post{/tr}</a>
{/if}
{/if}
{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
<a class="link" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">{tr}Edit{/tr}</a>
{/if}
{if ($user and $listpages[changes].user eq $user) or ($tiki_p_blog_admin eq 'y')}
<a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].blogId}">{tr}Remove{/tr}</a>
{/if}
<a class="link" href="tiki-view_blog.php?blogId={$listpages[changes].blogId}">{tr}Read{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].title|truncate:20:"(...)":true}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].description}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].public}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].posts}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].hits}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].activity}&nbsp;</td>
<td class="even">
{if $tiki_p_blog_post eq 'y'}
{if ($user and $listpages[changes].user eq $user) or $tiki_p_blog_admin eq 'y' or $listpages[changes].public eq 'y'}
<a class="link" href="tiki-blog_post.php?blogId={$listpages[changes].blogId}">{tr}Post{/tr}</a>
{/if}
{/if}
{if ($user and $listpages[changes].user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="link" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">{tr}Edit{/tr}</a>
{/if}
{if ($user and $listpages[changes].user eq $user) or $tiki_p_blog_admin eq 'y'}
<a class="link" href="tiki-list_blogs.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].blogId}">{tr}Remove{/tr}</a>
{/if}
<a class="link" href="tiki-view_blog.php?blogId={$listpages[changes].blogId}">{tr}Read{/tr}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_blogs.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_blogs.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
