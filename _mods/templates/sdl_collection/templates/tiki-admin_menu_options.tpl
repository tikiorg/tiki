<a class="pagetitle" href="tiki-admin_menu_options.php?menuId={$menuId}">{tr}Admin Menu{/tr}: {$menu_info.name}</a><br /><br />
<a href="tiki-admin_menus.php" class="linkbut">[{tr}List menus{/tr}</a>
<a href="tiki-admin_menus.php?menuId={$menuId}" class="linkbut">{tr}|Edit this menu{/tr}]</a>
<table width="95%"><tr><td valign="top" align="left">
<h2>{tr}Preview menu{/tr}</h2>
<div class="box">
<div class="box-title">[{$menu_info.name}]</div>
<div class="box-data">
{include file=tiki-user_menu.tpl channels=$allchannels}
</div>
</div>
</td>
<td valign="top" align="right">
<table class="normal"><tr><td valign="top" class="odd">
<h2>{tr}Edit menu options{/tr}</h2>
<form action="tiki-admin_menu_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId|escape}" />
<input type="hidden" name="menuId" value="{$menuId|escape}" />
<table>
<tr><td class="form">{tr}Name{/tr}:</td><td><input id="menu_name" type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="form">{tr}URL{/tr}:</td><td><input id="menu_url" type="text" name="url" value="{$url|escape}" size="34" /></td></tr>
<tr><td class="form">{tr}Sections{/tr}:</td><td><input id="menu_section" type="text" name="section" value="{$section|escape}" /></td></tr>
<tr><td class="form">{tr}Permissions{/tr}:</td><td><input id="menu_perm" type="text" name="perm" value="{$perm|escape}" /></td></tr>
<tr><td class="form">{tr}Groups{/tr}:</td><td><input id="menu_groupname" type="text" name="groupname" value="{$groupname|escape}" /></td></tr>
<tr><td class="form">{tr}Type{/tr}:</td><td>
<select name="type">
<option value="s" {if $type eq 's'}selected="selected"{/if}>{tr}section{/tr}</option>
<option value="o" {if $type eq 'o'}selected="selected"{/if}>{tr}option{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}Position{/tr}:</td><td><input type="text" name="position" value="{$position|escape}" /></td></tr>
<tr><td  class="form">&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
</td><td valign="top" class="even">
<h2>{tr}Some useful URLs{/tr}</h2>
<table>
<tr><td>{tr}Home{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="{$tikiIndex},{tr}Home Page{/tr}">{tr}Home Page{/tr}</option>
<option value="tiki-view_blog.php?blogId={$home_blog},{tr}Home Blog{/tr},feature_blogs,tiki_p_view_blogs">{tr}Home Blog{/tr}</option>
<option value="tiki-browse_gallery.php?galleryId={$home_gallery},{tr}Home Image Gal{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Home Image Gallery{/tr}</option>
<option value="tiki-list_file_gallery?galleryId={$home_file_gallery},{tr}Home File Gal{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}Home File Gallery{/tr}</option>]
</select></td></tr>
<tr><td>{tr}General{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-chat.php,{tr}Chat{/tr},feature_chat,tiki_p_chat">{tr}Chat{/tr}</option>
<option value="tiki-stats.php,{tr}Statistics{/tr},feature_stats,tiki_p_view_stats">{tr}Statistics{/tr}</option>
<option value="tiki-games.php,{tr}Games{/tr},feature_games,tiki_p_play_games">{tr}Games{/tr}</option>
<option value="tiki-browse_categories.php,{tr}Categories{/tr},feature_categories,tiki_p_view_categories">{tr}Categories{/tr}</option>
<option value="tiki-user_preferences.php,{tr}User preferences{/tr}">{tr}User Preferences{/tr}</option>
</select></td></tr>
<tr><td>{tr}Wiki{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-index.php,{tr}Wiki Home{/tr},feature_wiki,tiki_p_view">{tr}Wiki Home{/tr}</option>
<option value="tiki-lastchanges.php,{tr}Last changes{/tr},feature_lastChanges,tiki_p_view">{tr}Last cCanges{/tr}</option>
<option value="tiki-wiki_rankings.php,{tr}Rankings{/tr},feature_wiki_rankings,tiki_p_view">{tr}Rankings{/tr}</option>
<option value="tiki-listpages.php,{tr}List pages{/tr},feature_listPages,tiki_p_view">{tr}List Pages{/tr}</option>
<option value="tiki-index.php?page=SandBox,{tr}Sandbox{/tr},feature_sandbox,tiki_p_view">{tr}Sandbox{/tr}</option>
</select></td></tr>
<tr><td>{tr}Images{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-galleries.php,{tr}List galleries{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}List Image Galleries{/tr}</option>
<option value="tiki-upload_image.php,{tr}Upload image{/tr},feature_galleries,tiki_p_upload_images">{tr}Upload{/tr}</option>
<option value="tiki-galleries_rankings.php,{tr}Gallery Rankings{/tr},feature_gal_rankings,tiki_p_view_image_gallery">{tr}Rankings{/tr}</option>
<option value="tiki-browse_gallery.php?galleryId=,{tr}Browse a gallery{/tr},feature_galleries,tiki_p_view_image_gallery">{tr}Browse a Gallery{/tr}</option>
</select></td></tr>
<tr><td>{tr}Articles{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-view_articles.php,{tr}Articles{/tr},feature_articles,tiki_p_read_article">{tr}Articles Home{/tr}</option>
<option value="tiki-list_articles.php,{tr}All articles{/tr},feature_articles,tiki_p_read_article">{tr}List Articles{/tr}</option>
<option value="tiki-cms_rankings.php,{tr}Rankings{/tr},feature_cms_rankings,tiki_p_read_article">{tr}Rankings{/tr}</option>
<option value="tiki-edit_submission.php,{tr}Submit{/tr},feature_submissions,tiki_p_submit_article">{tr}Submit{/tr}</option>
<option value="tiki-list_submissions.php,{tr}Submissions{/tr},feature_submissions,tiki_p_approve_submission">{tr}Submissions{/tr}</option>
</select></td></tr>
<tr><td>{tr}Blogs{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-list_blogs.php,{tr}List Blogs{/tr},feature_blogs,tiki_p_read_blog">{tr}List Blogs{/tr}</option>
<option value="tiki-blog_rankings.php,{tr}Rankings{/tr},feature_blog_rankings,tiki_p_read_blog">{tr}Rankings{/tr}</option>
<option value="tiki-edit_blog.php,{tr}Create blog{/tr},feature_blogs,tiki_p_create_blogs">{tr}Create blog{/tr}</option>
<option value="tiki-blog_post.php,{tr}Post{/tr},feature_blogs,tiki_p_blog_post">{tr}Post{/tr}</option>
</select></td></tr>
<tr><td>{tr}Files{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-file_galleries.php,{tr}File galleries{/tr},feature_file_galleries,tiki_p_view_file_gallery">{tr}File Galleries{/tr}</option>
<option value="tiki-upload_file.php,{tr}Upload file{/tr},feature_file_galleries,tiki_p_upload_files">{tr}Upload File{/tr}</option>
<option value="tiki-file_galleries_rankings.php,{tr}Rankings{/tr},feature_file_galleries_rankings,tiki_p_view_file_gallery">{tr}Rankings{/tr}</option>
</select></td></tr>
<tr><td>{tr}Forums{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-forums.php,{tr}Forums{/tr},feature_forums,tiki_p_forum_read">{tr}Forums{/tr}</option>
<option value="tiki-view_forum.php?forumId=,{tr}View a forum{/tr},feature_forums,tiki_p_forum_read">{tr}View a Forum{/tr}</option>
<option value="tiki-view_forum_thread.php?forumId=&amp;comments_parentId=,{tr}View a thread{/tr},feature_forums,tiki_p_forum_read">{tr}View a Thread{/tr}</option>8
</select></td></tr>
<tr><td>{tr}FAQs{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-list_faqs.php,{tr}FAQs{/tr},feature_faqs,tiki_p_view_faqs">{tr}FAQs{/tr}</option>
<option value="tiki-view_faq.php?faqId=,{tr}View a FAQ{/tr},feature_faqs,tiki_p_view_faqs">{tr}View a FAQ{/tr}</option>
</select></td></tr>
<tr><td>{tr}Quizzes{/tr}: </td><td><select name="wikilinks" onchange="setMenuCon(options[selectedIndex].value);return true;">
<option value=",,,">{tr}Choose{/tr} ...</option>
<option value="tiki-list_quizzes.php,{tr}Quizzes{/tr},feature_quizzes">{tr}Quizzes{/tr}</option>
<option value="tiki-take_quiz.php?quizId=,{tr}Take a quiz{/tr},feature_quizzes">{tr}Take a Quiz{/tr}</option>
<option value="tiki-quiz_stats.php,{tr}Quiz stats{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Quiz Statistics{/tr}</option>
<option value="tiki-quiz_stats_quiz.php?quizId=,{tr}Stats for a Quiz{/tr},feature_quizzes,tiki_p_view_quiz_stats">{tr}Statistics for a Quiz{/tr}</option>
</select>
</td></tr></table>
</td></tr></table>
</td></tr></table>
<h2>{tr}Menu options{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_menu_options.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     <input type="hidden" name="menuId" value="{$menuId}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionId_desc'}optionId_asc{else}optionId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'url_desc'}url_asc{else}url_desc{/if}">{tr}URL{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'section_desc'}section_asc{else}section_desc{/if}">{tr}Sections{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'perm_desc'}perm_asc{else}perm_desc{/if}">{tr}Permissions{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupnam_desc'}groupname_asc{else}groupname_desc{/if}">{tr}Group{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $channels[user].type eq 's'}
<tr>
<td class="odd">{$channels[user].menuId}</td>
<td class="odd">{$channels[user].position}</td>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].url}</td>
<td class="odd">{$channels[user].type}</td>
<td class="odd">{$channels[user].section}</td>
<td class="odd">{$channels[user].perm}</td>
<td class="odd">{$channels[user].groupname}</td>
<td class="odd" >
&nbsp;<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this menu?{/tr}')"
title="{tr}delete{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
&nbsp;<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}" title="{tr}edit{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].menuId}</td>
<td class="even">{$channels[user].position}</td>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].url}</td>
<td class="even">{$channels[user].type}</td>
<td class="even">{$channels[user].section}</td>
<td class="even">{$channels[user].perm}</td>
<td class="even">{$channels[user].groupname}</td>
<td class="even">
&nbsp;<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}"
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this menu?{/tr}')" 
title="{tr}delete{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
&nbsp;<a class="link" href="tiki-admin_menu_options.php?menuId={$menuId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}" title="{tr}edit{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/if}
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_menu_options.php?find={$find}&amp;menuId={$menuId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_menu_options.php?find={$find}&amp;menuId={$menuId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_menu_options.php?find={$find}&amp;menuId={$menuId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
