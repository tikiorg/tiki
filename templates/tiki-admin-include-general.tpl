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
<a href="#webmail" class="link">{tr}webmail{/tr}</a> |
<a href="#directory" class="link">{tr}directory{/tr}</a> |
<a href="#userfiles" class="link">{tr}userfiles{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}General preferences and settings{/tr}</div>
<div class="cbox-data">
<form action="tiki-admin.php#general" method="post">
<div class="simplebox">
<table>
<tr><td class="form">{tr}Home page{/tr}:</td><td>
<select name="tikiIndex">
<option value="tiki-index.php" {if $tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>{tr}Wiki{/tr}</option>
<option value="tiki-view_articles.php" {if $tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>{tr}Articles{/tr}</option>
<option value="{$home_blog_url}" {if $tikiIndex eq $home_blog_url}selected="selected"{/if}>{tr}Blog{/tr}: {$home_blog_name}</option>
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
<tr><td class="form">{tr}Server time zone{/tr}:</td><td class="form">{$timezone_server}
&nbsp;<a class="link" target="http://www.worldtimezone.com/" href="http://www.worldtimezone.com/">{tr}Map{/tr}</a>
</td></tr>

<tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>
<select name='display_timezone'>
	{html_options options=$timezone_options selected=$display_timezone}
</select>
</td></tr>

<tr><td class="form">{tr}Long date format{/tr}:</td><td><input type="text" name="long_date_format" value="{$long_date_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Help{/tr}</a>
</td>
</tr>

<tr><td class="form">{tr}Short date format{/tr}:</td><td><input type="text" name="short_date_format" value="{$short_date_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Help{/tr}</a>
</td>
</tr>

<tr><td class="form">{tr}Long time format{/tr}:</td><td><input type="text" name="long_time_format" value="{$long_time_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Help{/tr}</a>
</td>
</tr>

<tr><td class="form">{tr}Short time format{/tr}:</td><td><input type="text" name="short_time_format" value="{$short_time_format}" size="50"/>
&nbsp;<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Help{/tr}</a>
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
  <form method="post" action="tiki-admin.php#general">
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
