{popup_init src="lib/overlib.js"}
<h2><a class="pagetitle" href="tiki-admin.php">{tr}Administration{/tr}</a></h2>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<a name="features"></a>
<div class="cbox">
<div class="cbox-title">{tr}Features{/tr}</div>
<div class="cbox-data">
<table width="100%">
<tr><td valign="top">
<div class="simplebox">
{tr}Tiki sections and features{/tr}
<form action="tiki-admin.php" method="post">
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
<tr><td class="form">{tr}Shoutbox{/tr}:</td><td><input type="checkbox" name="feature_shoutbox" {if $feature_shoutbox eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTML pages{/tr}:</td><td><input type="checkbox" name="feature_html_pages" {if $feature_html_pages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Drawings{/tr}:</td><td><input type="checkbox" name="feature_drawings" {if $feature_drawings eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Referer stats{/tr}:</td><td><input type="checkbox" name="feature_referer_stats" {if $feature_referer_stats eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Trackers{/tr}:</td><td><input type="checkbox" name="feature_trackers" {if $feature_trackers eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Surveys{/tr}:</td><td><input type="checkbox" name="feature_surveys" {if $feature_surveys eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Webmail{/tr}:</td><td><input type="checkbox" name="feature_webmail" {if $feature_webmail eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Newsletters{/tr}:</td><td><input type="checkbox" name="feature_newsletters" {if $feature_newsletters eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Directory{/tr}:</td><td><input type="checkbox" name="feature_directory" {if $feature_directory eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Messages{/tr}:</td><td><input type="checkbox" name="feature_messages" {if $feature_messages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Contact{/tr}:</td><td><input type="checkbox" name="feature_contact" {if $feature_contact eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td align="center" class="form" colspan="2"><input type="submit" name="features" value="{tr}Set features{/tr}" /></td></tr>
</table>
</form>
</div>
</td><td valign="top">
<div class="simplebox">
{tr}General Layout options{/tr}
<form action="tiki-admin.php" method="post">
<table>
<tr><td class="form">{tr}Left column{/tr}:</td><td><input type="checkbox" name="feature_left_column" {if $feature_left_column eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Right column{/tr}:</td><td><input type="checkbox" name="feature_right_column" {if $feature_right_column eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Top bar{/tr}:</td><td><input type="checkbox" name="feature_top_bar" {if $feature_top_bar eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Bottom bar{/tr}:</td><td><input type="checkbox" name="feature_bot_bar" {if $feature_bot_bar eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td align="center" class="form" colspan="2"><input type="submit" name="layout" value="{tr}Set features{/tr}" /></td></tr>
</table>
</form>
<form action="tiki-admin.php" method="post">
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

<a name="general"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}General preferences and settings{/tr}</div>
<div class="cbox-data">
<form action="tiki-admin.php" method="post">
<div class="simplebox">
<table>
<tr><td class="form">{tr}Home page{/tr}:</td><td>
<select name="tikiIndex">
<option value="tiki-index.php" {if $tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>Wiki</option>
<option value="tiki-view_articles.php" {if $tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>Articles</option>
<option value="{$home_blog_url}" {if $tikiIndex eq $home_blog_url}selected="selected"{/if}>Blog: {$home_blog_name}</option>
<option value="{$home_gallery_url}" {if $tikiIndex eq $home_gallery_url}selected="selected"{/if}>{tr}Image Gallery{/tr}: {$home_gal_name}</option>
<option value="{$home_file_gallery_url}" {if $tikiIndex eq $home_file_gallery_url}selected="selected"{/if}>{tr}File Gallery{/tr}: {$home_fil_name}</option>
<option value="{$home_forum_url}" {if $tikiIndex eq $home_forum_url}selected="selected"{/if}>{tr}Forum{/tr}: {$home_forum_name}</option>
{if $feature_custom_home eq 'y'}
<option value="tiki-custom_home.php" {if $tikiIndex eq 'tiki-custom_home.php'}selected="selected"{/if}>{tr}Custom home{/tr}</option>
{/if}
</select>
</td></tr>
<tr><td class="form">{tr}OS{/tr}</td><td>
<select name="system_os">
<option value="unix" {if $system_os eq 'unix'}selected="selected"{/if}>{tr}Unix{/tr}</option>
<option value="windows" {if $system_os eq 'windows'}selected="selected"{/if}>{tr}Windows{/tr}</option>
<option value="unknown" {if $system_os eq 'unknown'}selected="selected"{/if}>{tr}Unknown/Other{/tr}</option>
</select>
</td></tr>
<tr><td class="form">{tr}Use URI as Home Page{/tr}:</td><td><input type="checkbox" name="useUrlIndex" {if $useUrlIndex eq 'y'}checked="checked"{/if}/><input type="text" name="urlIndex" value="{$urlIndex}"/></td></tr>
<tr><td class="form">{tr}Open external links in new window{/tr}:</td><td><input type="checkbox" name="popupLinks" {if $popupLinks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use gzipped output{/tr}:</td><td><input type="checkbox" name="feature_obzip" {if $feature_obzip eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Display modules to all groups always{/tr}:</td><td><input type="checkbox" name="modallgroups" {if $modallgroups eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use cache for external pages{/tr}:</td><td><input type="checkbox" name="cachepages" {if $cachepages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use cache for external images{/tr}:</td><td><input type="checkbox" name="cacheimages" {if $cacheimages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Count admin pageviews{/tr}:</td><td><input type="checkbox" name="count_admin_pvs" {if $count_admin_pvs eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Maximum number of records in listings{/tr}: </td><td><input size="5" type="text" name="maxRecords" value="{$maxRecords}" /></td></tr>
<tr><td class="form">{tr}Use direct pagination links{/tr}:</td><td><input type="checkbox" name="direct_pagination" {if $direct_pagination eq 'y'}checked="checked"{/if}/></td></tr>
<!--<tr><td class="form">{tr}Wiki_Tiki_Title{/tr}: </td><td><input type="text" size="5" name="title" value="{$title}" /></td></tr>-->
<tr><td class="form">{tr}Theme{/tr}:</td><td>
        <select name="style_site">
        {section name=ix loop=$styles}
        <option value="{$styles[ix]}" {if $style_site eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
        {/section}
        </select></td></tr>
<tr><td class="form">{tr}Slideshows theme{/tr}:</td><td>
        <select name="slide_style">
        {section name=ix loop=$slide_styles}
        <option value="{$slide_styles[ix]}" {if $slide_style eq $slide_styles[ix]}selected="selected"{/if}>{$slide_styles[ix]}</option>
        {/section}
        </select></td></tr>        
<tr><td class="form">{tr}Language{/tr}:</td><td>
        <select name="language">
        {section name=ix loop=$languages}
        <option value="{$languages[ix]}" {if $site_language eq $languages[ix]}selected="selected"{/if}>{$languages[ix]}</option>
        {/section}
<tr><td class="form">{tr}Use database for translation{/tr}:</td><td><input type="checkbox" name="lang_use_db" {if $lang_use_db eq 'y'}checked="checked"{/if}/></td></tr>
{if $lang_use_db eq 'y'}
<tr><td class="form">{tr}Record untranslated{/tr}:</td><td><input type="checkbox" name="record_untranslated" {if $record_untranslated eq 'y'}checked="checked"{/if}/></td></tr>
{/if}
        </select></td></tr>
<tr><td class="form">{tr}Server name (for absolute URIs){/tr}:</td><td><input type="text" name="feature_server_name" value="{$feature_server_name}" /></td></tr>        
<tr><td class="form">{tr}Browser title{/tr}:</td><td><input type="text" name="siteTitle" value="{$siteTitle}" /></td></tr>
<tr><td class="form">{tr}Temporary directory{/tr}:</td><td><input type="text" name="tmpDir" value="{$tmpDir}" /></td></tr>
<tr><td class="form">{tr}Server time zone{/tr}:</td><td>{$timezone_server}
&nbsp;<a class="link" target="http://www.worldtimezone.com/" href="http://www.worldtimezone.com/">Map</a>
</td></tr>

<tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>
<select name='display_timezone'>
	{html_options options=$timezone_options selected=$display_timezone}
</select>
</td></tr>

<tr><td class="form">{tr}Long date format{/tr}:</td><td><input type="text" name="long_date_format" value="{$long_date_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">Help</a>
</td>
</tr>

<tr><td class="form">{tr}Short date format{/tr}:</td><td><input type="text" name="short_date_format" value="{$short_date_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">Help</a>
</td>
</tr>

<tr><td class="form">{tr}Long time format{/tr}:</td><td><input type="text" name="long_time_format" value="{$long_time_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">Help</a>
</td>
</tr>

<tr><td class="form">{tr}Short time format{/tr}:</td><td><input type="text" name="short_time_format" value="{$short_time_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">Help</a>
</td>
</tr>

<tr><td class="form">{tr}Contact user{/tr}:</td><td>{if $feature_contact eq 'y'}<input type="text" name="contact_user" value="{$contact_user}" />{else}{tr}contact feature disabled{/tr}{/if}</td></tr>

<tr><td>&nbsp;</td><td><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</div>
</form>
<div class="simplebox">
<table width="80%" cellpadding="0" cellspacing="0">
<tr>
  <td>
  <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Change admin password{/tr}:</td><td><input type="password" name="adminpass" /></td></tr>
    <tr><td class="form">{tr}Again{/tr}:</td><td><input type="password" name="again" /></td></tr>
    <tr><td>&nbsp;</td><td><input type="submit" name="newadminpass" value="{tr}change{/tr}" /></td></tr>
    </table>
  </form>
  </td>
</tr>
</table>
</div>
</div>
</div>


<a name="login"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}User registration and login{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $allowRegister eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Request passcode to register{/tr}:</td><td><input type="checkbox" name="useRegisterPasscode" {if $useRegisterPasscode eq 'y'}checked="checked"{/if}/><input type="text" name="registerPasscode" value="{$registerPasscode}"/></td></tr>
<tr><td class="form">{tr}Validate users by email{/tr}:</td><td><input type="checkbox" name="validateUsers" {if $validateUsers eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Remind passwords by email{/tr}:</td><td><input type="checkbox" name="forgotPass" {if $forgotPass eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change theme{/tr}:</td><td><input type="checkbox" name="change_theme" {if $change_theme eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Reg users can change language{/tr}:</td><td><input type="checkbox" name="change_language" {if $change_language eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Store plaintext passwords{/tr}:</td><td><input type="checkbox" name="feature_clear_passwords" {if $feature_clear_passwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use challenge/response authentication{/tr}:</td><td><input type="checkbox" name="feature_challenge" {if $feature_challenge eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Force to use chars and nums in passwords{/tr}:</td><td><input type="checkbox" name="pass_chr_num" {if $pass_chr_num eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Minimum password length{/tr}:</td><td><input type="text" name="min_pass_length" value="{$min_pass_length}" /></td></tr>
<tr><td class="form">{tr}Password invalid after days{/tr}:</td><td><input type="text" name="pass_due" value="{$pass_due}" /></td></tr>
<!-- # not implemented
<tr><td class="form">{tr}Require HTTP Basic authentication{/tr}:</td><td><input type="checkbox" name="http_basic_auth" {if $http_basic_auth eq 'y'}checked="checked"{/if}/></td></tr>
-->
<tr><td class="form">{tr}Allow secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login" {if $https_login eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Require secure (https) login{/tr}:</td><td><input type="checkbox" name="https_login_required" {if $https_login_required eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}HTTP server name{/tr}:</td><td><input type="text" name="http_domain" value="{$http_domain}" /></td></tr>
<tr><td class="form">{tr}HTTP port{/tr}:</td><td><input type="text" name="http_port" size="5" value="{$http_port}" /></td></tr>
<tr><td class="form">{tr}HTTP URL prefix{/tr}:</td><td><input type="text" name="http_prefix" value="{$http_prefix}" /></td></tr>
<tr><td class="form">{tr}HTTPS server name{/tr}:</td><td><input type="text" name="https_domain" value="{$https_domain}" /></td></tr>
<tr><td class="form">{tr}HTTPS port{/tr}:</td><td><input type="text" name="https_port" size="5" value="{$https_port}" /></td></tr>
<tr><td class="form">{tr}HTTPS URL prefix{/tr}:</td><td><input type="text" name="https_prefix" value="{$https_prefix}" /></td></tr>
<tr><td class="form">{tr}Remember me feature{/tr}</td><td>
<select name="rememberme">
<option value="disabled" {if $rememberme eq 'disabled'}selected="selected"{/if}>{tr}Disabled{/tr}</option>
<!--<option value="noadmin" {if $rememberme eq 'noadmin'}selected="selected"{/if}>{tr}Only for users{/tr}</option>-->
<option value="all" {if $rememberme eq 'all'} selected="selected"{/if}>{tr}Users and admins{/tr}</option>
</select>
{tr}Duration:{/tr}
<select name="remembertime">
<option value="300" {if $remembertime eq 300} selected="selected"{/if}>5 {tr}minutes{/tr}</option>
<option value="900" {if $remembertime eq 900} selected="selected"{/if}>15 {tr}minutes{/tr}</option>
<option value="1800" {if $remembertime eq 1800} selected="selected"{/if}>30 {tr}minutes{/tr}</option>
<option value="3600" {if $remembertime eq 3600} selected="selected"{/if}>1 {tr}hour{/tr}</option>
<option value="7200" {if $remembertime eq 7200} selected="selected"{/if}>2 {tr}hours{/tr}</option>
<option value="36000" {if $remembertime eq 36000} selected="selected"{/if}>10 {tr}hours{/tr}</option>
<option value="72000" {if $remembertime eq 72000} selected="selected"{/if}>1 {tr}day{/tr}</option>
<option value="720000" {if $remembertime eq 720000} selected="selected"{/if}>1 {tr}week{/tr}</option>
</select>
</tr></tr>

<tr><td>&nbsp;</td><td><input type="submit" name="loginprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>



<a name="wiki"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Wiki settings{/tr}</div>
<div class="cbox-data">
    <table>
    <tr><td width="60%" valign="top">
    <div class="simplebox">
    {tr}Dumps{/tr}:<br/>
    <a class="link" href="tiki-admin.php?dump=1">{tr}Generate dump{/tr}</a><br/>
    <a class="link" href="dump/new.tar">{tr}Download last dump{/tr}</a>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Create a tag for the current wiki{/tr}<br/>
    {tr}Tag Name{/tr}<input  maxlength="20" size="10" type="text" name="tagname"/>
    <input type="submit" name="createtag" value="{tr}create{/tr}"/>
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Restore the wiki{/tr}<br/>
    {tr}Tag Name{/tr}: <select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="restoretag" value="{tr}restore{/tr}"/>          
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Remove a tag{/tr}<br/>
    {tr}Tag Name{/tr}<select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
    <input type="submit" name="removetag" value="{tr}remove{/tr}"/>          
    </form>
    </div>    
    
    
    <div class="simplebox">
    {tr}Wiki comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="wiki_comments_per_page" value="{$wiki_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="wiki_comments_default_ordering">
    <option value="commentDate_desc" {if $wiki_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $wiki_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikiprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Wiki attachments{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Wiki attachments{/tr}:</td><td><input type="checkbox" name="feature_wiki_attachments" {if $feature_wiki_attachments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="y" {if $w_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="w_use_db" value="n" {if $w_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="w_use_dir" value="{$w_use_dir}" /> </tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikiattprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Export Wiki Pages{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td align="center" colspan="2"><a class="link" href="tiki-export_wiki_pages.php">{tr}Export{/tr}</a></tr>
    </table>
    </form>
    </div>
    
    
    <div class="simplebox">
    <a class="link" href="tiki-admin.php?rmvunusedpic=1">{tr}Remove unused pictures{/tr}</a>
    </div>
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table><tr>
    <td class="form">{tr}Wiki Home Page{/tr}</td><td class="form"><input type="text" name="wikiHomePage" value="{$wikiHomePage}" />
    <input type="submit" name="setwikihome" value="{tr}set{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table><tr>
    <td class="form">{tr}Wiki Page Names{/tr}</td><td class="form">
    <select name="wiki_page_regex">
    <option value='full' {if $wiki_page_regex eq 'full'}selected="selected"{/if}>{tr}full{/tr}</option>
    <option value='strict' {if $wiki_page_regex eq 'strict'}selected="selected"{/if}>{tr}strict{/tr}</option>
    </select>
    <input type="submit" name="setwikiregex" value="{tr}set{/tr}" />
    </td>
    </tr></table>
    </form>
    </div>
    
    </td>
    
    <td width="40%" valign="top">
    <div class="simplebox">
    {tr}Wiki Features{/tr}:<br/>
    <form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Sandbox{/tr}:</td><td><input type="checkbox" name="feature_sandbox" {if $feature_sandbox eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Last changes{/tr}:</td><td><input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Dump{/tr}:</td><td><input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/></td></tr>
    <!--<tr><td class="form">{tr}Ranking{/tr}:</td><td><input type="checkbox" name="feature_ranking" {if $feature_ranking eq 'y'}checked="checked"{/if}/></td></tr>-->
    <tr><td class="form">{tr}History{/tr}:</td><td><input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}List pages{/tr}:</td><td><input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Backlinks{/tr}:</td><td><input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Like pages{/tr}:</td><td><input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_wiki_rankings" {if $feature_wiki_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Undo{/tr}:</td><td><input type="checkbox" name="feature_wiki_undo" {if $feature_wiki_undo eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}MultiPrint{/tr}:</td><td><input type="checkbox" name="feature_wiki_multiprint" {if $feature_wiki_multiprint eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_wiki_comments" {if $feature_wiki_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td><input type="checkbox" name="wiki_spellcheck" {if $wiki_spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use templates{/tr}:</td><td><input type="checkbox" name="feature_wiki_templates" {if $feature_wiki_templates eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Warn on edit{/tr}:</td><td><input type="checkbox" name="feature_warn_on_edit" {if $feature_warn_on_edit eq 'y'}checked="checked"{/if}/>
    <select name="warn_on_edit_time">
    <option value="1" {if $warn_on_edit_time eq 1}selected="selected"{/if}>1</option>
    <option value="2" {if $warn_on_edit_time eq 2}selected="selected"{/if}>2</option>
    <option value="5" {if $warn_on_edit_time eq 5}selected="selected"{/if}>5</option>
    <option value="10" {if $warn_on_edit_time eq 10}selected="selected"{/if}>10</option>
    <option value="15" {if $warn_on_edit_time eq 15}selected="selected"{/if}>15</option>
    <option value="30" {if $warn_on_edit_time eq 30}selected="selected"{/if}>30</option>
    </select> {tr}mins{/tr}
    </td></tr>
    <tr><td class="form">{tr}Pictures{/tr}:</td><td><input type="checkbox" name="feature_wiki_pictures" {if $feature_wiki_pictures eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use page description{/tr}:</td><td><input type="checkbox" name="feature_wiki_description" {if $feature_wiki_description eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Show page title{/tr}:</td><td><input type="checkbox" name="feature_page_title" {if $feature_page_title eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Cache wiki pages{/tr}:</td><td>
    <select name="wiki_cache">
    <option value="0" {if $wiki_cache eq 0}selected="selected"{/if}>0 ({tr}no cache{/tr})</option>
    <option value="60" {if $wiki_cache eq 60}selected="selected"{/if}>1 minute</option>
    <option value="300" {if $wiki_cache eq 300}selected="selected"{/if}>5 minutes</option>
    <option value="600" {if $wiki_cache eq 600}selected="selected"{/if}>10 minute</option>
    <option value="900" {if $wiki_cache eq 900}selected="selected"{/if}>15 minutes</option>
    <option value="1800" {if $wiki_cache eq 1800}selected="selected"{/if}>30 minute</option>
    <option value="3600" {if $wiki_cache eq 3600}selected="selected"{/if}>1 hour</option>
    <option value="7200" {if $wiki_cache eq 7200}selected="selected"{/if}>2 hours</option>
    </select> 
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikifeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
    </form>
    </div>
    
    <div class="simplebox">
    {tr}Wiki History{/tr}
    <form action="tiki-admin.php" method="post">
    <table>
    <tr><td class="form">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions}" /></td></tr>
    <tr><td class="form">{tr}Never delete versions younger than days{/tr}: </td><td><input size="5" type="text" name="keep_versions" value="{$keep_versions}" /></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikisetprefs" value="{tr}Set{/tr}" /></td></tr>    
    </table>
    </form>
    </div>
    </td></tr>
    </table>
</div>
</div>

<a name="gal"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Image galleries{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Gallery (main gallery){/tr}</td><td>
<select name="homeGallery">
{section name=ix loop=$galleries}
<option value="{$galleries[ix].galleryId}" {if $galleries[ix].galleryId eq $home_gallery}selected="selected"{/if}>{$galleries[ix].name|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="galset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>

    

<div class="simplebox">
{tr}Galleries features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_gal_rankings" {if $feature_gal_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_image_galleries_comments" {if $feature_image_galleries_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store images{/tr}:</td><td><input type="radio" name="gal_use_db" value="y" {if $gal_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store images{/tr}:</td><td><input type="radio" name="gal_use_db" value="n" {if $gal_use_db eq 'n'}checked="checked"{/if}/> {tr}Directory path{/tr}:<input type="text" name="gal_use_dir" value="{$gal_use_dir}" /> </tr>
    <tr><td class="form">{tr}Uploaded image names must match regex{/tr}:</td><td><input type="text" name="gal_match_regex" value="{$gal_match_regex}"/></td></tr>
    <tr><td class="form">{tr}Uploaded image names cannot match regex{/tr}:</td><td><input type="text" name="gal_nmatch_regex" value="{$gal_nmatch_regex}"/></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="galfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
<div class="simplebox">
<a class="link" href="tiki-admin.php?rmvorphimg=1">{tr}Remove images in the system gallery not being used in Wiki pages, articles or blog posts{/tr}</a>
</div>

    <div class="simplebox">
    {tr}Image galleries comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="image_galleries_comments_per_page" value="{$image_galleries_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="image_galleries_comments_default_ordering">
    <option value="commentDate_desc" {if $image_galleries_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $image_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="imagegalcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>


</div>
</div>

<a name="fgal"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}File galleries{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Gallery (main gallery){/tr}</td><td>
<select name="homeFileGallery">
{section name=ix loop=$file_galleries}
<option value="{$file_galleries[ix].galleryId}" {if $file_galleries[ix].galleryId eq $home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="filegalset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>


<div class="simplebox">
{tr}Galleries features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_file_galleries_rankings" {if $feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_file_galleries_comments" {if $feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="fgal_use_db" value="y" {if $fgal_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="fgal_use_db" value="n" {if $fgal_use_db eq 'n'}checked="checked"{/if}/> {tr}Directory path{/tr}:<input type="text" name="fgal_use_dir" value="{$fgal_use_dir}" /> </tr>
    <tr><td class="form">{tr}Uploaded filenames must match regex{/tr}:</td><td><input type="text" name="fgal_match_regex" value="{$fgal_match_regex}"/></td></tr>
    <tr><td class="form">{tr}Uploaded filenames cannot match regex{/tr}:</td><td><input type="text" name="fgal_nmatch_regex" value="{$fgal_nmatch_regex}"/>
    <a class="link" {popup sticky="true" trigger="onClick" caption="Storing files in a directory" text="If you decide to store files in a directory you must ensure that the user cannot access directly to the directory. You have two options to accomplish this:<br/><ul><li>Use a directory ourside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use and .htaccess to prevent the user from listing the directory contents</li></ul>To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/"}>please read</a></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="filegalfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>

    <div class="simplebox">
    {tr}File galleries comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="file_galleries_comments_per_page" value="{$file_galleries_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="file_galleries_comments_default_ordering">
    <option value="commentDate_desc" {if $file_galleries_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="filegalcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>


</div>
</div>

<a name="cms"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}CMS settings{/tr}</div>
<div class="cbox-data">



<div class="simplebox">
{tr}CMS features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_cms_rankings" {if $feature_cms_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_article_comments" {if $feature_article_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td><input type="checkbox" name="cms_spellcheck" {if $cms_spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use templates{/tr}:</td><td><input type="checkbox" name="feature_cms_templates" {if $feature_cms_templates eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="cmsfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
<div class="simplebox">
<form method="post" action="tiki-admin.php">
<table>
  <tr><td class="form">{tr}Maximum number of articles in home{/tr}: </td><td><input size="5" type="text" name="maxArticles" value="{$maxArticles}" /></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="cmsprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>

    <div class="simplebox">
    {tr}Article comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="article_comments_per_page" value="{$article_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="article_comments_default_ordering">
    <option value="commentDate_desc" {if $article_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $article_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="articlecomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

</div>
</div>

<a name="polls"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Poll settings{/tr}</div>
<div class="cbox-data">
    <div class="simplebox">
    {tr}Poll comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_poll_comments" {if $feature_poll_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="poll_comments_per_page" value="{$poll_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="poll_comments_default_ordering">
    <option value="commentDate_desc" {if $poll_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $poll_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="pollprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
</div>
</div>


<a name="blogs"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Blog settings{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Blog (main blog){/tr}</td><td>
<select name="homeBlog">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="blogset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>




<div class="simplebox">
{tr}Blog features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_blog_rankings" {if $feature_blog_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Blog level comments{/tr}:</td><td><input type="checkbox" name="feature_blog_comments" {if $feature_blog_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Post level comments{/tr}:</td><td><input type="checkbox" name="feature_blogposts_comments" {if $feature_blogposts_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Spellchecking{/tr}:</td><td><input type="checkbox" name="blog_spellcheck" {if $blog_spellcheck eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default ordering for blog listing{/tr}:</td>
    <td>
    <select name="blog_list_order">
    <option value="created_desc" {if $blog_list_order eq 'created_desc'}selected="selected"{/if}>{tr}Creation date (desc){/tr}</option>
    <option value="lastModif_desc" {if $blog_list_order eq 'lastModif_desc'}selected="selected"{/if}>{tr}Last modification date (desc){/tr}</option>
    <option value="title_asc" {if $blog_list_order eq 'title_asc'}selected="selected"{/if}>{tr}Blog title (asc){/tr}</option>
    <option value="posts_desc" {if $blog_list_order eq 'posts_desc'}selected="selected"{/if}>{tr}Number of posts (desc){/tr}</option>
    <option value="hits_desc" {if $blog_list_order eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
    <option value="activity_desc" {if $blog_list_order eq 'activity_desc'}selected="selected"{/if}>{tr}Activity (desc){/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="blogfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>

    <div class="simplebox">
    {tr}Blog comments settings{/tr}
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="blog_comments_per_page" value="{$blog_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="blog_comments_default_ordering">
    <option value="commentDate_desc" {if $blog_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $blog_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="blogcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>

</div>
</div>

<a name="forums"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Forums{/tr}</div>
<div class="cbox-data">
    {tr}Forums settings{/tr}
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Home Forum (main forum){/tr}</td><td>
    <select name="homeForum">
    {section name=ix loop=$forums}
    <option value="{$forums[ix].forumId}" {if $forums[ix].forumId eq $home_forum}selected="selected"{/if}>{$forums[ix].name|truncate:20:"(...)":true}</option>
    {/section}
    </select>
    </td></tr>
        <tr><td align="center" colspan="2"><input type="submit" name="homeforumprefs" value="{tr}Set home forum{/tr}" /></td></tr>
    </table>
    </form>
    </div>
    
    
    
    <div class="simplebox">
    <form method="post" action="tiki-admin.php">
    <table>
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_forum_rankings" {if $feature_forum_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Ordering for forums in the forum listing{/tr}
    </td><td>
    <select name="forums_ordering">
    <option value="created_desc" {if $forums_ordering eq 'created_desc'}selected="selected"{/if}>{tr}Creation Date (desc){/tr}</option>
    <option value="threads_desc" {if $forums_ordering eq 'threads_desc'}selected="selected"{/if}>{tr}Topics (desc){/tr}</option>
    <option value="comments_desc" {if $forums_ordering eq 'comments_desc'}selected="selected"{/if}>{tr}Threads (desc){/tr}</option>
    <option value="lastPost_desc" {if $forums_ordering eq 'lastPost_desc'}selected="selected"{/if}>{tr}Last post (desc){/tr}</option>
    <option value="hits_desc" {if $forums_ordering eq 'hits_desc'}selected="selected"{/if}>{tr}Visits (desc){/tr}</option>
    <option value="name_desc" {if $forums_ordering eq 'name_desc'}selected="selected"{/if}>{tr}Name (desc){/tr}</option>
    <option value="name_asc" {if $forums_ordering eq 'name_asc'}selected="selected"{/if}>{tr}Name (asc){/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="forumprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
</div>
</div>    


<a name="faqs"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}FAQs settings{/tr}</div>
<div class="cbox-data">



<div class="simplebox">
{tr}FAQ comments{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_faq_comments" {if $feature_faq_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="faq_comments_per_page" value="{$faq_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="faq_comments_default_ordering">
    <option value="commentDate_desc" {if $faq_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $faq_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="faqcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
</form>
</div>
</div>
</div>


<a name="trackers"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Trackers{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="y" {if $t_use_db eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td><input type="radio" name="t_use_db" value="n" {if $t_use_db eq 'n'}checked="checked"{/if}/> {tr}Path{/tr}:<input type="text" name="t_use_dir" value="{$t_use_dir}" /> </tr>
<tr><td align="center" colspan="2"><input type="submit" name="trkset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>

[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<a name="webmail"></a>
<div class="cbox">
<div class="cbox-title">{tr}Webmail{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td class="form">{tr}Allow viewing HTML mails?{/tr}:</td><td><input type="checkbox" name="webmail_view_html" {if $webmail_view_html eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}Maximum size for each attachment{/tr}:</td><td><select name="webmail_max_attachment">
<option value="500000" {if $webmail_max_attachment eq 500000}selected="selected"{/if}>500Kb</option>
<option value="1000000" {if $webmail_max_attachment eq 1000000}selected="selected"{/if}>1Mb</option>
<option value="1500000" {if $webmail_max_attachment eq 1500000}selected="selected"{/if}>1.5Mb</option>
<option value="2000000" {if $webmail_max_attachment eq 2000000}selected="selected"{/if}>2Mb</option>
<option value="2500000" {if $webmail_max_attachment eq 2500000}selected="selected"{/if}>2.5Mb</option>
<option value="3000000" {if $webmail_max_attachment eq 3000000}selected="selected"{/if}>3Mb</option>
<option value="100000000" {if $webmail_max_attachment eq 100000000}selected="selected"{/if}>Unlimited</option>
</select></tr>
<tr><td align="center" colspan="2"><input type="submit" name="webmail" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>

[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<a name="directory"></a>
<div class="cbox">
<div class="cbox-title">{tr}Directory{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td>{tr}Number of columns per page when listing categories{/tr}</td><td>
    <select name="directory_columns">
    <option value="1" {if $directory_columns eq 1}selected="selected"{/if}>1</option>
    <option value="2" {if $directory_columns eq 2}selected="selected"{/if}>2</option>
    <option value="3" {if $directory_columns eq 3}selected="selected"{/if}>3</option>
    <option value="4" {if $directory_columns eq 4}selected="selected"{/if}>4</option>
    <option value="5" {if $directory_columns eq 5}selected="selected"{/if}>5</option>
    <option value="6" {if $directory_columns eq 6}selected="selected"{/if}>6</option>
    </select>
    </td></tr>
<tr><td>{tr}Links per page{/tr}</td><td><input type="text" name="directory_links_per_page" value="{$directory_links_per_page}" />
<tr><td>{tr}Validate URLs{/tr}</td><td><input type="checkbox" name="directory_validate_urls" {if $directory_validate_urls eq 'y'}checked="checked"{/if}></td></tr>
</td></tr>    
<tr><td>{tr}Method to open directory links{/tr}</td><td>
<select name="directory_open_links">
<option value="r" {if $directory_open_links eq 'r'}selected="selected"{/if}>{tr}replace current window{/tr}</option>
<option value="n" {if $directory_open_links eq 'n'}selected="selected"{/if}>{tr}new window{/tr}</option>
<option value="f" {if $directory_open_links eq 'f'}selected="selected"{/if}>{tr}inline frame{/tr}</option>
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="directory" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
</div>
</div>



<a name="rss"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a>|
<a href="#directory" class="link">{tr}directory{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}RSS feeds{/tr}</div>
<div class="cbox-data">
    <table>
    <tr>
      <td valign="top">
        <form action="tiki-admin.php" method="post">
        <table>
        <tr><td class="form">{tr}<b>Feed</b>{/tr}</td>
            <td class="form">{tr}<b>enable/disable</b>{/tr}</td>
            <td class="form">{tr}<b>Max number of items</b>{/tr}</td>
        </tr>
        <tr><td class="form">{tr}Feed for Articles{/tr}:</td><td><input type="checkbox" name="rss_articles" {if $rss_articles eq 'y'}checked="checked"{/if}/></td><td class="form"><input type="text" name="max_rss_articles" size="5" value="{$max_rss_articles}" /></td></tr>
        <tr><td class="form">{tr}Feed for Weblogs{/tr}:</td><td><input type="checkbox" name="rss_blogs" {if $rss_blogs eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blogs" size="5" value="{$max_rss_blogs}" /></td></tr>
        <tr><td class="form">{tr}Feed for Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_galleries" {if $rss_image_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_galleries" size="5" value="{$max_rss_image_galleries}" /></td></tr>
        <tr><td class="form">{tr}Feed for File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_galleries" {if $rss_file_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_galleries" size="5" value="{$max_rss_file_galleries}" /></td></tr>
        <tr><td class="form">{tr}Feed for the Wiki{/tr}:</td><td><input type="checkbox" name="rss_wiki" {if $rss_wiki eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_wiki" size="5" value="{$max_rss_wiki}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_gallery" {if $rss_image_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_gallery" size="5" value="{$max_rss_image_gallery}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_gallery" {if $rss_file_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_gallery" size="5" value="{$max_rss_file_gallery}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual weblogs{/tr}:</td><td><input type="checkbox" name="rss_blog" {if $rss_blog eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blog" size="5" value="{$max_rss_blog}" /></td></tr>
        <tr><td class="form">{tr}Feed for forums{/tr}:</td><td><input type="checkbox" name="rss_forums" {if $rss_forums eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forums" size="5" value="{$max_rss_forums}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual forums{/tr}:</td><td><input type="checkbox" name="rss_forum" {if $rss_forum eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forum" size="5" value="{$max_rss_forum}" /></td></tr>
        
        <tr><td align="center" colspan="3"><input type="submit" name="rss" value="{tr}Set feeds{/tr}" /></td></tr>    
        </table>
        </form>
      </td>
    </tr>
    </table>
</div>
</div>
<br/><br/>

