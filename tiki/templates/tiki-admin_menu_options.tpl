<a class="pagetitle" href="tiki-admin_menu_options.php?menuId={$menuId}">Admin Menu: {$menu_info.name}</a><br/><br/>
<a href="tiki-admin_menus.php" class="link">{tr}List menus{/tr}</a>
<a href="tiki-admin_menus.php?menuId={$menuId}" class="link">{tr}Edit this menu{/tr}</a>
<h2>{tr}Preview menu{/tr}</h2>
<div align="center">
<div style="text-align:left;width:130px;" class="cbox">
<div class="cbox-title">{$menu_info.name}</div>
<div class="cbox-data">
{include file=tiki-user_menu.tpl}
</div>
</div>
</div>
<br/>
<table class="normal"><tr><td valign="top" class="odd">
<h2>{tr}Edit menu options{/tr}</h2>
<form action="tiki-admin_menu_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId}" />
<input type="hidden" name="menuId" value="{$menuId}" />
<table>
<tr><td class="form">{tr}Name{/tr}:</td><td><input id="menu_name" type="text" name="name" value="{$name}" /></td></tr>
<tr><td class="form">{tr}URL{/tr}:</td><td><input id="menu_url" type="text" name="url" value="{$url}" /></td></tr>
<tr><td class="form">{tr}Type{/tr}:</td><td>
<select name="type">
<option value="s" {if $type eq 's'}selected="selected"{/if}>{tr}section{/tr}</option>
<option velue="o" {if $type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}Position{/tr}:</td><td><input type="text" name="position" value="{$position}" /></td></tr>
<tr><td  class="form">&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
</td><td valign="top" class="even">
<h2>{tr}Some useful URLs{/tr}</h2>
<a class="link" href="javascript:setMenuCon('{$tikiIndex}','{tr}Home Page{/tr}');">{tr}Home Page{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-view_blog.php?blogId={$home_blog}','{tr}Home Blog{/tr}');">{tr}Home Blog{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-browse_gallery.php?galleryId={$home_gallery}','{tr}Home Image Gal{/tr}');">{tr}Home Image Gallery{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-list_file_gallery?galleryId={$home_file_gallery}','{tr}Home File Gal{/tr}');">{tr}Home File Gallery{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-chat.php','{tr}Chat{/tr}');">{tr}Chat{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-user_preferences.php','{tr}User preferences{/tr}');">{tr}User prefs{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-index.php','{tr}Wiki Home{/tr}');">{tr}Wiki Home{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-lastchanges.php','{tr}Last changes{/tr}');">{tr}Last changes{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-wiki_rankings.php','{tr}Rankings{/tr}');">{tr}Rankings{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-listpages.php','{tr}List pages{/tr}');">{tr}List pages{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-index.php?page=SandBox','{tr}Sandbox{/tr}');">{tr}Sandbox{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-galleries.php','{tr}List galleries{/tr}');">{tr}List image galleries{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-upload_image.php','{tr}Upload image{/tr}');">{tr}Upload image{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-galleries_rankings.php','{tr}Gallery Rankings{/tr}');">{tr}Gallery Rankings{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-browse_gallery.php?galleryId=','');">{tr}Browse a gallery{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-articles_home.php','{tr}Articles{/tr}');">{tr}Articles home{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-list_articles.php','{tr}All articles{/tr}');">{tr}List articles{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-cms_rankings.php','{tr}Rankings{/tr}');">{tr}Rankings{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-edit_submission.php','{tr}Submit{/tr}');">{tr}Submit{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-list_submissions.php','{tr}Submissions{/tr}');">{tr}Submissions{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-list_blogs.php','{tr}List Blogs{/tr}');">{tr}List Blogs{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-blog_rankings.php','{tr}Rankings{/tr}');">{tr}Rankings{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-edit_blog.php','{tr}Create blog{/tr}');">{tr}Create blog{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-blog_post.php','{tr}Post{/tr}');">{tr}Post{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-file_galleries.php','{tr}File galleries{/tr}');">{tr}File galleries{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-upload_file.php','{tr}Upload file{/tr}');">{tr}Upload file{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-file_galleries_rankings.php','{tr}Rankings{/tr}');">{tr}Rankings{/tr}</a>
<br/>
<a class="link" href="javascript:setMenuCon('tiki-forums.php','{tr}Forums{/tr}');">{tr}Forums{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-view_forum.php?forumId=','');">{tr}View a forum{/tr}</a>
<a class="link" href="javascript:setMenuCon('tiki-view_forum_thread.php?forumId=&amp;comments_parentId=','');">{tr}View a thread{/tr}</a>
</td></tr></table>
<h2>Menu options</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_menu_options.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionId_desc'}optionId_asc{else}optionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}url{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $channels[user].type eq 's'}
<tr>
<td class="odd">{$channels[user].menuId}</td>
<td class="odd">{$channels[user].position}</td>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].url}</td>
<td class="odd">{$channels[user].type}</td>
<td class="odd">
   <a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
   
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].menuId}</td>
<td class="even">{$channels[user].position}</td>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].url}</td>
<td class="even">{$channels[user].type}</td>
<td class="even">
   <a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">{tr}edit{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_menu_options.php?find={$find}&amp;menuId={$menuId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_menu_options.php?find={$find}&amp;menuId={$menuId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

