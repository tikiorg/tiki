{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-admin-include-general.tpl,v 1.3 2004-01-26 03:58:03 musus Exp $ *}
<div class="tiki">
  <div class="tiki-title">{tr}General preferences and settings{/tr}</div>
  <div class="tiki-content">
    <form action="tiki-admin.php?page=general" method="post">
      <table class="admin"><tr>
        <th colspan="2">{tr}General Preferences{/tr}</th>
      </tr><tr>
        <td><label>{tr}Theme{/tr}:</label></td>
        <td width="67%"><select name="site_style">
            {section name=ix loop=$styles}
              <option value="{$styles[ix]|escape}"
                {if $style_site eq $styles[ix]}selected="selected"{/if}>
                {$styles[ix]}</option>
            {/section}
            </select>
        </td>
      </tr><tr>
        <td><label>{tr}Slideshows theme{/tr}:</label></td>
        <td><select name="slide_style">
            {section name=ix loop=$slide_styles}
              <option value="{$slide_styles[ix]|escape}"
                {if $slide_style eq $slide_styles[ix]}selected="selected"{/if}>
                {$slide_styles[ix]}</option>
            {/section}
            </select>
        </td>
      </tr><tr><td colspan="2"><hr /></td></tr>
      <tr>
        <td><label>{tr}Use group homepages{/tr}:</label></td>
        <td><input type="checkbox" name="useGroupHome" {if $useGroupHome eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td><label>{tr}Use URI as Home Page{/tr}:</label></td>
        <td><input type="checkbox" name="useUrlIndex" {if $useUrlIndex eq 'y'}checked="checked"{/if}/>
            <input type="text" name="urlIndex" value="{$urlIndex|escape}" size="50" />
        </td>
      </tr><tr>
        <td><label>{tr}Home page{/tr}:</label></td>
        <td><select name="tikiIndex">
            <option value="tiki-index.php"
              {if $tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>{tr}Wiki{/tr}</option>
            <option value="tiki-view_articles.php"
              {if $tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>{tr}Articles{/tr}</option>
            {if $home_blog_name}
              <option value="{$home_blog_url|escape}"
                {if $tikiIndex eq $home_blog_url}selected="selected"{/if}>{tr}Blog{/tr}: {$home_blog_name}</option>
            {/if}
            {if $home_gal_name}
              <option value="{$home_gallery_url|escape}"
                {if $tikiIndex eq $home_gallery_url}selected="selected"{/if}>{tr}Image Gallery{/tr}: {$home_gal_name}</option>
            {/if}
            {if $home_fil_name}
              <option value="{$home_file_gallery_url|escape}"
                {if $tikiIndex eq $home_file_gallery_url}selected="selected"{/if}>{tr}File Gallery{/tr}: {$home_fil_name}</option>
            {/if}
            {if $home_forum_name}
              <option value="{$home_forum_url|escape}"
                {if $tikiIndex eq $home_forum_url}selected="selected"{/if}>{tr}Forum{/tr}: {$home_forum_name}</option>
            {/if}
            {if $feature_custom_home eq 'y'}
              <option value="tiki-custom_home.php"
                {if $tikiIndex eq 'tiki-custom_home.php'}selected="selected"{/if}>{tr}Custom home{/tr}</option>
            {/if}
            </select>
        </td>
      </tr><tr><td colspan="2"><hr /></td></tr><tr>
        <td><label>{tr}Language{/tr}:</label></td>
        <td>
        <select name="language" id="">
        {section name=ix loop=$languages}
        <option value="{$languages[ix].value|escape}"
          {if $language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
        {/section}
        </select>
        </td>
      </tr><tr>
        <td><label>{tr}Use database for translation{/tr}:</label></td>
        <td><input type="checkbox" name="lang_use_db" {if $lang_use_db eq 'y'}checked="checked"{/if}/></td>
        {if $lang_use_db eq 'y'}
          </tr><tr>
            <td><label>{tr}Record untranslated{/tr}:</label></td>
            <td><input type="checkbox" name="record_untranslated" {if $record_untranslated eq 'y'}checked="checked"{/if}/></td>
        {/if}
      </tr><tr>
        <td><label>{tr}OS{/tr}:</label></td>
        <td><select name="system_os">
            <option value="unix"
              {if $system_os eq 'unix'}selected="selected"{/if}>{tr}Unix{/tr}</option>
            <option value="windows"
              {if $system_os eq 'windows'}selected="selected"{/if}>{tr}Windows{/tr}</option>
            <option value="unknown"
              {if $system_os eq 'unknown'}selected="selected"{/if}>{tr}Unknown/Other{/tr}</option>
            </select>
        </td>
      </tr></table>
      <table class="admin"><tr>
        <td class="heading" colspan="5" align="center">{tr}General Settings{/tr}</td>
      </tr><tr>
        <td><label>{tr}Disallow access to the site (except for those with permission){/tr}:</label></td>
        <td><input type="checkbox" name="site_closed" {if $site_closed eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td><label>{tr}Message to display when site is closed{/tr}:</label></td>
        <td><input type="text" name="site_closed_msg" value="{$site_closed_msg}" size="60" /></td>
      </tr>
      </table>
      <table class="admin"><tr>
        <td colspan="2"><hr /></td>
      </tr><tr>
        <td><label>{tr}Disallow access when load is above the threshold (except for those with permission){/tr}:</label></td>
        <td><input type="checkbox" name="use_load_threshold" {if $use_load_threshold eq 'y'}checked="checked"{/if}/>
      </td>
      </tr><tr>
        <td><label>{tr}Max average server load threshold in the last minute{/tr}:</label></td>
        <td><input type="text" name="load_threshold" value="{$load_threshold}" size="5" /></td>
      </tr><tr>
        <td><label>{tr}Message to display when server is too busy{/tr}:</label></td>
        <td><input type="text" name="site_busy_msg" value="{$site_busy_msg}" size="60" /></td>
      </tr>
      </table>
      <table class="admin"><tr>
        <td colspan="5"><hr /></td></tr>
        <tr>
        <td><label>{tr}Open external links in new window{/tr}:</label></td>
        <td><input type="checkbox" name="popupLinks" {if $popupLinks eq 'y'}checked="checked"{/if} /></td>
        <td>&nbsp;</td>
        <td><label>{tr}Display modules to all groups always{/tr}:</label></td>
        <td><input type="checkbox" name="modallgroups"
              {if $modallgroups eq 'y'}checked="checked"{/if} {popup text="Warning! New admins, there is currently a bug
that should remove your login module if you disable this, making you unable to get back in Tiki. Use tiki-login_scr.php to
get back in if this happens to you. The corresponding SF bug tracker is 835233." textcolor=red} />
        </td>
      </tr><tr>
        <td><label>{tr}Use cache for external pages{/tr}:</label></td>
        <td><input type="checkbox" name="cachepages" {if $cachepages eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td><label>{tr}Use cache for external images{/tr}:</label></td>
        <td><input type="checkbox" name="cacheimages" {if $cacheimages eq 'y'}checked="checked"{/if}/></td>
      </tr><tr>
        <td><label>{tr}Use direct pagination links{/tr}:</label></td>
        <td><input type="checkbox" name="direct_pagination" {if $direct_pagination eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td><label>{tr}Display menus as folders{/tr}:</label></td>
        <td><input type="checkbox" name="feature_menusfolderstyle"
              {if $feature_menusfolderstyle eq 'y'}checked="checked"{/if} /></td>
      </tr><tr>
        <td><label>{tr}Use gzipped output{/tr}:</label></td>
        <td><input type="checkbox" name="feature_obzip"
              {if $feature_obzip eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td><label>{tr}Count admin pageviews{/tr}:</label></td>
        <td><input type="checkbox" name="count_admin_pvs"
              {if $count_admin_pvs eq 'y'}checked="checked"{/if}/></td>
<!-- Still under discussion     
      </tr><tr>
        <td><label>{tr}Hide anonymous-only modules from registered users{/tr}:</label></td>
        <td><input type="checkbox" name="modseparateanon"
              {if $modseparateanon eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
-->     
      </tr></table>
      <table class="admin"><tr>
        <td colspan="2"><hr /></td>
      </tr><tr>
        <td><label>{tr}Server name (for absolute URIs){/tr}:</label></td>
        <td><input type="text" name="feature_server_name" value="{$feature_server_name|escape}" size="40" /></td>
      </tr><tr>
        <td><label>{tr}Browser title{/tr}:</label></td>
        <td><input type="text" name="siteTitle" value="{$siteTitle|escape}" size="40" /></td>
      </tr><tr>
<!--
        <td><label>{tr}Wiki_Tiki_Title{/tr}: </label></td>
        <td><input type="text" size="5" name="title" value="{$title|escape}" size="40" /></td>
      </tr><tr>
-->
        <td><label>{tr}Temporary directory{/tr}:</label></td>
        <td><input type="text" name="tmpDir" value="{$tmpDir|escape}" size="50" /></td>
      </tr><tr>
        <td><label>{tr}Sender Email{/tr}:</label></td>
        <td><input type="text" name="sender_email" value="{$sender_email|escape}" size="50" /></td>
      </tr><tr>
<!--  <td><label>{tr}Email Encoding{/tr}:</label></td>
        <td><select name="email_encoding">
                  <option value="utf-8" {if $email_encoding != "iso-8859-1"}selected="selected"{/if}>utf-8</option>
                  <option value="iso-8859-1"{if $email_encoding == "iso-8859-1"}selected="selected"{/if}>iso-8859-1</option>
        </select></td>
      </tr><tr>
-->
        <td><label>{tr}Contact user{/tr}:</label></td>
        <td>{if $feature_contact eq 'y'}
              <input type="text" name="contact_user" value="{$contact_user|escape}" size="40" />
            {else}
              {tr}contact feature disabled{/tr}
            {/if}
        </td>
      </tr><tr>
        <td><label>{tr}Store session data in database{/tr}:</label></td>
        <td><input type="checkbox" name="session_db" {if $session_db eq 'y'}checked="checked"{/if} /></td>
      </tr><tr>
        <td><label>{tr}Session lifetime in minutes{/tr}:</label></td>
        <td><input size="5" type="text" name="session_lifetime" value="{$session_lifetime|escape}" /></td>
      </tr><tr>
        <td><label>{tr}Use proxy{/tr}:</label></td>
        <td><input type="checkbox" name="use_proxy" {if $use_proxy eq 'y'}checked="checked"{/if} /></td>
      </tr><tr>
        <td><label>{tr}Proxy Host{/tr}:</label></td>
        <td><input type="text" name="proxy_host" value="{$proxy_host|escape}" size="40" /></td>
      </tr><tr>
        <td><label>{tr}Proxy port{/tr}:</label></td>
        <td><input size="5" type="text" name="proxy_port" value="{$proxy_port|escape}" /></td>
      </tr><tr>
        <td><label>{tr}Maximum number of records in listings{/tr}:</label></td>
        <td><input size="5" type="text" name="maxRecords" value="{$maxRecords|escape}" /></td>
      </tr></table>
      <table class="admin"><tr>
        <td class="heading" colspan="2" align="center">{tr}Date and Time Formats{/tr}</td>
      </tr><tr>
        <td><label>{tr}Long date format{/tr}:</label></td>
        <td><input type="text" name="long_date_format" value="{$long_date_format|escape}" size="40"/></td>
      </tr><tr>
        <td><label>{tr}Short date format{/tr}:</label></td>
        <td><input type="text" name="short_date_format" value="{$short_date_format|escape}" size="40"/></td>
      </tr><tr>
        <td><label>{tr}Long time format{/tr}:</label></td>
        <td><input type="text" name="long_time_format" value="{$long_time_format|escape}" size="40"/></td>
      </tr><tr>
        <td><label>{tr}Short time format{/tr}:</label></td>
        <td><input type="text" name="short_time_format" value="{$short_time_format|escape}" size="40"/></td>
      </tr><tr>
        {assign var="fcnlink" value="http://www.php.net/manual/en/function.strftime.php"}
        <td colspan="2" align="center"><a target="strftime" href="{$fcnlink}">{tr}Date and Time Format Help{/tr}</a></td>
      </tr><tr>
        <td colspan="2" class="button"><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td>
      </tr></table>
<!-- Obsolete from 1.7 timezone changes
      <table class="admin"><tr>
        <td class="heading" colspan="2" align="center">{tr}Time Zone{/tr}</td>
      </tr><tr>
        <td  style="vertical-align:middle;">{tr}Server time zone{/tr}:</td>
        <td><div class="simplebox">{$timezone_server}</div></td>
      </tr><tr>
        <td><label>{tr}Displayed time zone{/tr}:</label></td>
        <td><select name='display_timezone'>{html_options options=$timezone_options selected=$display_timezone}</select></td>
      </tr><tr>
        <td colspan="2" align="center"><a target="wtz" href="http://www.worldtimezone.com/">{tr}Time Zone Map{/tr}</a></td>
      </tr>
      </table>
-->
    </form>
  </div>
</div>
<br />
<div class="tiki">
  <div class="tiki-title">{tr}Register your site at tikiwiki.org{/tr}</div>
  <div class="tiki-content"><a href="tiki-register_site.php">{tr}Click here for more details.{/tr}</a></div>
</div>
<br />
<div class="tiki">
  <div class="tiki-title">{tr}Change admin password{/tr}</div>
  <div class="tiki-content">
    <form method="post" action="tiki-admin.php?page=general">
      <table class="admin"><tr>
        <td><label>{tr}New password{/tr}:</label></td>
        <td><input type="password" name="adminpass" /></td>
      </tr><tr>
        <td><label>{tr}Repeat password{/tr}:</label></td>
        <td><input type="password" name="again" /></td>
      </tr><tr>
        <td colspan="2" class="button">
          <input type="submit" name="newadminpass" value="{tr}Change password{/tr}" /></td>
      </tr></table>
    </form>
  </div>
</div>