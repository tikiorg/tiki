<a name="features"></a>
<div class="cbox">
<div class="cbox-title">{tr}Features{/tr}</div>
<div class="cbox-data">
<table width="100%">
<tr><td valign="top">
<div class="simplebox">
{tr}Tiki sections and features{/tr}
<form action="tiki-admin.php#features" method="post">
<table>
<tr><td class="form">{tr}Wiki{/tr}:</td><td><input type="checkbox" name="feature_wiki" {if $feature_wiki eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Search{/tr}:</td><td><input type="checkbox" name="feature_search" {if $feature_search eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Full Text Search{/tr}:</td><td><input type="checkbox" name="feature_search_fulltext" {if $feature_search_fulltext eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Search stats{/tr}:</td><td><input type="checkbox" name="feature_search_stats" {if $feature_search_stats eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Image Galleries{/tr}:</td><td><input type="checkbox" name="feature_galleries" {if $feature_galleries eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Featured links{/tr}:</td><td><input type="checkbox" name="feature_featuredLinks" {if $feature_featuredLinks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Hotwords{/tr}:</td><td><input type="checkbox" name="feature_hotwords" {if $feature_hotwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Hotwords in new window{/tr}:</td><td><input type="checkbox" name="feature_hotwords_nw" {if $feature_hotwords_nw eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User preferences screen{/tr}:</td><td><input type="checkbox" name="feature_userPreferences" {if $feature_userPreferences eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Articles{/tr}:</td><td><input type="checkbox" name="feature_articles" {if $feature_articles eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Submissions{/tr}:</td><td><input type="checkbox" name="feature_submissions" {if $feature_submissions eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Blogs{/tr}:</td><td><input type="checkbox" name="feature_blogs" {if $feature_blogs eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}XMLRPC API{/tr}:</td><td><input type="checkbox" name="feature_xmlrpc" {if $feature_xmlrpc eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Banners{/tr}:</td><td><input type="checkbox" name="feature_banners" {if $feature_banners eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Edit templates{/tr}:</td><td><input type="checkbox" name="feature_edit_templates" {if $feature_edit_templates eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Dynamic content system{/tr}:</td><td><input type="checkbox" name="feature_dynamic_content" {if $feature_dynamic_content eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}File galleries{/tr}:</td><td><input type="checkbox" name="feature_file_galleries" {if $feature_file_galleries eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Chat{/tr}:</td><td><input type="checkbox" name="feature_chat" {if $feature_chat eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Polls{/tr}:</td><td><input type="checkbox" name="feature_polls" {if $feature_polls eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Custom home{/tr}:</td><td><input type="checkbox" name="feature_custom_home" {if $feature_custom_home eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Forums{/tr}:</td><td><input type="checkbox" name="feature_forums" {if $feature_forums eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Communications (send/receive objects){/tr}:</td><td><input type="checkbox" name="feature_comm" {if $feature_comm eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Categories{/tr}:</td><td><input type="checkbox" name="feature_categories" {if $feature_categories eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}FAQs{/tr}:</td><td><input type="checkbox" name="feature_faqs" {if $feature_faqs eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Users can configure modules{/tr}:</td><td><input type="checkbox" name="user_assigned_modules" {if $user_assigned_modules eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User bookmarks{/tr}:</td><td><input type="checkbox" name="feature_user_bookmarks" {if $feature_user_bookmarks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Stats{/tr}:</td><td><input type="checkbox" name="feature_stats" {if $feature_stats eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Games{/tr}:</td><td><input type="checkbox" name="feature_games" {if $feature_games eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Quizzes{/tr}:</td><td><input type="checkbox" name="feature_quizzes" {if $feature_quizzes eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Allow smileys{/tr}:</td><td><input type="checkbox" name="feature_smileys" {if $feature_smileys eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}ShoutBox{/tr}:</td><td><input type="checkbox" name="feature_shoutbox" {if $feature_shoutbox eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTML pages{/tr}:</td><td><input type="checkbox" name="feature_html_pages" {if $feature_html_pages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Drawings{/tr}:</td><td><input type="checkbox" name="feature_drawings" {if $feature_drawings eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Referer stats{/tr}:</td><td><input type="checkbox" name="feature_referer_stats" {if $feature_referer_stats eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Trackers{/tr}:</td><td><input type="checkbox" name="feature_trackers" {if $feature_trackers eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Surveys{/tr}:</td><td><input type="checkbox" name="feature_surveys" {if $feature_surveys eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Webmail{/tr}:</td><td><input type="checkbox" name="feature_webmail" {if $feature_webmail eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Newsletters{/tr}:</td><td><input type="checkbox" name="feature_newsletters" {if $feature_newsletters eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Directory{/tr}:</td><td><input type="checkbox" name="feature_directory" {if $feature_directory eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User Messages{/tr}:</td><td><input type="checkbox" name="feature_messages" {if $feature_messages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User Tasks{/tr}:</td><td><input type="checkbox" name="feature_tasks" {if $feature_tasks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Newsreader{/tr}:</td><td><input type="checkbox" name="feature_newsreader" {if $feature_newsreader eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Contact{/tr}:</td><td><input type="checkbox" name="feature_contact" {if $feature_contact eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User Notepad{/tr}:</td><td><input type="checkbox" name="feature_notepad" {if $feature_notepad eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User files{/tr}:</td><td><input type="checkbox" name="feature_userfiles" {if $feature_userfiles eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User menu{/tr}:</td><td><input type="checkbox" name="feature_usermenu" {if $feature_usermenu eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Mini calendar{/tr}:</td><td><input type="checkbox" name="feature_minical" {if $feature_minical eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Ephemerides{/tr}:</td><td><input type="checkbox" name="feature_eph" {if $feature_eph eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Theme control{/tr}:</td><td><input type="checkbox" name="feature_theme_control" {if $feature_theme_control eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Workflow engine{/tr}:</td><td><input type="checkbox" name="feature_workflow" {if $feature_workflow eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use PHPOpenTracker{/tr}:</td><td><input type="checkbox" name="feature_phpopentracker" {if $feature_phpopentracker eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Charts{/tr}:</td><td><input type="checkbox" name="feature_charts" {if $feature_charts eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}User watches{/tr}:</td><td><input type="checkbox" name="feature_user_watches" {if $feature_user_watches eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Live support system{/tr}:</td><td><input type="checkbox" name="feature_live_support" {if $feature_live_support eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td align="center" class="form" colspan="2"><input type="submit" name="features" value="{tr}Set features{/tr}" /></td></tr>
</table>
</form>
</div>
</td><td valign="top">
<div class="simplebox">
{tr}General Layout options{/tr}
<form action="tiki-admin.php#features" method="post">
<table>
<tr><td class="form">{tr}Left column{/tr}:</td><td><input type="checkbox" name="feature_left_column" {if $feature_left_column eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Right column{/tr}:</td><td><input type="checkbox" name="feature_right_column" {if $feature_right_column eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Top bar{/tr}:</td><td><input type="checkbox" name="feature_top_bar" {if $feature_top_bar eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Bottom bar{/tr}:</td><td><input type="checkbox" name="feature_bot_bar" {if $feature_bot_bar eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td align="center" class="form" colspan="2"><input type="submit" name="layout" value="{tr}Set features{/tr}" /></td></tr>
</table>
</form>
<form action="tiki-admin.php#features" method="post">
<table>
<tr><td class="form">{tr}Layout per section{/tr}:</td><td><input type="checkbox" name="layout_section" {if $layout_section eq 'y'}checked="checked"{/if}/></td>
<td align="center" class="form" colspan="2"><input type="submit" name="layout_ss" value="{tr}Set{/tr}" /></td></tr>
</table>
</form>
<a href="tiki-admin_layout.php" class="link">{tr}Admin layout per section{/tr}</a>
</div>
</td></tr></table>
</div>
</div>
