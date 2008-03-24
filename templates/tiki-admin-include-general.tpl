{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-general.tpl,v 1.69.2.11 2008-03-08 21:12:22 sylvieg Exp $ *}

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
    <form action="tiki-admin.php?page=general" method="post">
      <table class="admin"><tr>
        <td class="heading" colspan="2"
            align="center">{tr}General Preferences{/tr}</td>
      </tr>
	<tr><td width="40%" class="form">{tr}Tikiwiki version{/tr} : </td><td class="form">{$tiki_version}</td></tr>
	<tr><td colspan="2"><hr /></td></tr>

      <tr>
        <td class="form"><label for="general-homepages">{tr}Use group homepages{/tr}:</label></td>
        <td><input type="checkbox" name="useGroupHome" id="general-homepages"
              {if $prefs.useGroupHome eq 'y'}checked="checked"{/if}/>
        </td>
      </tr>
	<tr>
		<td class="form"><label for="general-gogrouphome">{tr}Go to group homepage only if login from default homepage{/tr}:</label></td>
		<td><input type="checkbox" name="limitedGoGroupHome" id="general-gogrouphome"{if $prefs.limitedGoGroupHome eq 'y'}checked="checked"{/if}/>
        </td>
	</tr>
      <tr>
        <td class="form"><label for="general-uri">{tr}Use URI as Home Page{/tr}:</label></td>
        <td><input type="checkbox" name="useUrlIndex" id="general-uri"
              {if $prefs.useUrlIndex eq 'y'}checked="checked"{/if}/>
            <input type="text" name="urlIndex" value="{$prefs.urlIndex|escape}" size="50" />
        </td>
      </tr><tr>
        <td class="form"><label for="general-homepage">{tr}Home page{/tr}:</label></td>
        <td><select name="tikiIndex" id="general-homepage">
            <option value="tiki-index.php"
              {if $prefs.site_tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>
              {tr}Wiki{/tr}</option>
            <option value="tiki-view_articles.php"
              {if $prefs.site_tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>
              {tr}Articles{/tr}</option>
            {if $prefs.home_blog_name}
              <option value="{$prefs.home_blog_url|escape}"
                {if $prefs.site_tikiIndex eq $prefs.home_blog_url}selected="selected"{/if}>
                {tr}Blog{/tr}: {$prefs.home_blog_name}</option>
            {/if}
            {if $home_gal_name}
              <option value="{$prefs.home_gallery_url|escape}"
                {if $prefs.site_tikiIndex eq $prefs.home_gallery_url}selected="selected"{/if}>
                {tr}Image Gallery{/tr}: {$home_gal_name}</option>
            {/if}
            {if $home_fil_name}
              <option value="{$prefs.home_file_gallery_url|escape}"
                {if $prefs.site_tikiIndex eq $prefs.home_file_gallery_url}selected="selected"{/if}>
                {tr}File Gallery{/tr}: {$home_fil_name}</option>
            {/if}
            {if $prefs.home_forum_name}
              <option value="{$prefs.home_forum_url|escape}"
                {if $prefs.site_tikiIndex eq $prefs.home_forum_url}selected="selected"{/if}>
                {tr}Forum{/tr}: {$prefs.home_forum_name}</option>
            {/if}
            {if $prefs.feature_custom_home eq 'y'}
              <option value="tiki-custom_home.php"
                {if $prefs.site_tikiIndex eq 'tiki-custom_home.php'}selected="selected"{/if}>{tr}Custom home{/tr}</option>
            {/if}
            </select>
        </td>
      </tr><tr><td colspan="2"><hr/></td></tr><tr>
        <td class="form"><label for="general-os">{tr}OS{/tr}:</label></td>
        <td><select name="system_os" id="general-os">
            <option value="unix"
              {if $prefs.system_os eq 'unix'}selected="selected"{/if}>{tr}Unix{/tr}</option>
            <option value="windows"
              {if $prefs.system_os eq 'windows'}selected="selected"{/if}>{tr}Windows{/tr}</option>
            <option value="unknown"
              {if $prefs.system_os eq 'unknown'}selected="selected"{/if}>{tr}Unknown/Other{/tr}</option>
            </select>
        </td>
      </tr><tr>
      <td class="form"><label for="general-error">{tr}PHP error reporting level:{/tr}</label></td>
      <td><select name="error_reporting_level" id="general-error">
            <option value="0" {if $prefs.error_reporting_level eq 0}selected="selected"{/if}>{tr}No error reporting{/tr}</option>
            <option value="2047" {if $prefs.error_reporting_level eq 2047}selected="selected"{/if}>{tr}Report all PHP errors{/tr}</option>
            <option value="2039" {if $prefs.error_reporting_level eq 2039}selected="selected"{/if}>{tr}Report all errors except notices{/tr}</option>
            </select>
						<br />

						<input type="checkbox" name="error_reporting_adminonly"{if $prefs.error_reporting_adminonly eq 'y'} checked="checked"{/if} />
						{tr}visible to admin only{/tr}<br />
						<input type="checkbox" name="smarty_notice_reporting"{if $prefs.smarty_notice_reporting eq 'y'} checked="checked"{/if} />
						{tr}smarty notice reporting{/tr}
      </td>
	  </tr><tr>
	  <td class="form"><label for="log_mail">{tr}Log Mail in Tiki Logs:{/tr}</label></td>
	  <td><input type="checkbox" name="log_mail"{if $prefs.log_mail eq 'y'} checked="checked"{/if} /></td>
	  </TR><TR>
	  <td class="form"><label for="log_sql">{tr}Log SQL:{/tr}</label></td>
	  <td><input type="checkbox" name="log_sql"{if $prefs.log_sql eq 'y'} checked="checked"{/if} /></td>
      </tr><tr>
      <td class="form"><label for="general-charset">{tr}Default charset for sending mail:{/tr}</label></td>
      <td><select name="default_mail_charset" id="general-charset">
            <option value="utf-8" {if $prefs.default_mail_charset eq "utf-8"}selected="selected"{/if}>utf-8</option>
            <option value="iso-8859-1" {if $prefs.default_mail_charset eq "iso-8859-1"}selected="selected"{/if}>iso-8859-1</option>
            </select>
      </td>
	</tr><tr>
      <td class="form"><label for="mail_crlf">{tr}Mail end of line:{/tr}</label></td>
      <td><select name="mail_crlf" id="mail_crlf">
            <option value="CRLF" {if $prefs.mail_crlf eq "CRLF"}selected="selected"{/if}>CRLF {tr}(standard){/tr}</option>
            <option value="LF" {if $prefs.mail_crlf eq "LF"}selected="selected"{/if}>LF {tr}(some Unix MTA){/tr}</option>
            </select>
      </td>
	</tr>
		
			<tr>
        <td class="heading" colspan="2" align="center">{tr}General Settings{/tr}</td>
      </tr>
		
		<tr>
        <td class="form" >
          <label for="general-access">{tr}Disallow access to the site (except for those with permission){/tr}:</label></td>
        <td ><input type="checkbox" name="site_closed" id="general-access"
              {if $prefs.site_closed eq 'y'}checked="checked"{/if}/>
        </td>
      </tr><tr>
        <td class="form">
            <label for="general-site_closed">{tr}Message to display when site is closed{/tr}:</label></td>
        <td><input type="text" name="site_closed_msg" id="general-site_closed"
             value="{$prefs.site_closed_msg}" size="60"/></td>
      </tr>
     
			<tr>
        <td colspan="2"><hr/></td>
      </tr>

			<tr>
        <td class="form" >
          <label for="general-load">{tr}Disallow access when load is above the threshold (except for those with permission){/tr}:</label></td>
        <td ><input type="checkbox" name="use_load_threshold" id="general-load"
              {if $prefs.use_load_threshold eq 'y'}checked="checked"{/if}/>
      </td>
      </tr><tr>
        <td class="form"><label for="general-max_load">{tr}Max average server load threshold in the last minute{/tr}:</label></td>
        <td><input type="text" name="load_threshold" id="general-max_load" value="{$prefs.load_threshold}" size="5" /></td>
      </tr><tr>
        <td class="form"><label for="general-load_mess">{tr}Message to display when server is too busy{/tr}:</label></td>
        <td><input type="text" name="site_busy_msg" id="general-load_mess" value="{$prefs.site_busy_msg}" size="60" /></td>
      </tr>
			<tr><td colspan="2">
			
			<table width="100%">
        <td colspan="5"><hr/></td></tr>
        <tr>
        <td class="form"><label for="general-cache_ext_pages">{tr}Use cache for external pages{/tr}:</label></td>
        <td><input type="checkbox" name="cachepages" id="general-cache_ext_pages"
              {if $prefs.cachepages eq 'y'}checked="checked"{/if}/>
        </td>
        <td>&nbsp;</td>
        <td class="form"><label for="general-cache_ext_imgs">{tr}Use cache for external images{/tr}:</label></td>
        <td><input type="checkbox" name="cacheimages" id="general-cache_ext_imgs"
              {if $prefs.cacheimages eq 'y'}checked="checked"{/if}/>
        </td>
      </tr><tr>
        <td class="form"><label for="general-gzip">
        {if $prefs.feature_help eq 'y'}<a href="{$prefs.helpurl}Compression" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}:" >{/if}
        {tr}Use gzipped output{/tr}
        {if $prefs.feature_help eq 'y'}</a>{/if}
        :</label>
          {if $gzip_handler ne 'none'}
          <br /><div class="highlight">
          {tr}output compression is active.{/tr}<br />
          {tr}compression is handled by{/tr}: {$gzip_handler}
          </div>{/if}
        </td>
        <td><input type="checkbox" name="feature_obzip" id="general-gzip" {if $prefs.feature_obzip eq 'y'}checked="checked"{/if}/></td>
        <td>&nbsp;</td>
        <td class="form"><label for="general-pageviews">{tr}Count admin pageviews{/tr}:</label></td>
        <td><input type="checkbox" name="count_admin_pvs" id="general-pageviews" {if $prefs.count_admin_pvs eq 'y'}checked="checked"{/if}/></td>
      </tr>
			</table>
			</td></tr>

      <tr>
        <td colspan="2"><hr/></td>
      </tr>
			
			<tr>
        <td class="form"><label for="general-browser_title">{tr}Browser title{/tr}:</label></td>
        <td><input type="text" name="siteTitle" id="general-browser_title" value="{$prefs.siteTitle|escape}" size="40" /></td>
      </tr><tr>
        <td class="form"><label for="general-temp">{tr}Temporary directory{/tr}:</label></td>
        <td><input type="text" name="tmpDir" id="general-temp" value="{$prefs.tmpDir|escape}" size="50" /></td>
      </tr><tr>
        <td class="form"><label for="general-send_email">{tr}Sender Email{/tr}:</label></td>
        <td><input type="text" name="sender_email" id="general-send_email" value="{$prefs.sender_email|escape}" size="50" /></td>
      </tr>
			<tr>
        <td class="form"><label for="general-contact">{tr}Contact user{/tr}:</label></td>
        <td><input type="text" name="contact_user" id="general-contact" value="{$prefs.contact_user|escape}" size="40" /></td>
      </tr><tr>
        <td class="form"><label for="contact_anon">{tr}Allow anonymous users to "Contact Us"{/tr}:</label></td>
        <td><input type="checkbox" name="contact_anon" id="contact_anon"
              {if $prefs.contact_anon eq 'y'}checked="checked"{/if}/>
{if $prefs.feature_contact ne 'y'}
        {tr}contact feature disabled{/tr}
{/if}
        </td>
      </tr><tr>
        <td class="form"><label for="general-session_db">{tr}Store session data in database{/tr}:</label></td>
        <td><input type="checkbox" name="session_db" id="general-session_db"
              {if $prefs.session_db eq 'y'}checked="checked"{/if}/>
        </td>
      </tr><tr>
        <td class="form"><label for="general-session_life">{tr}Session lifetime in minutes{/tr}:</label></td>
        <td><input size="5" type="text" name="session_lifetime" id="general-session_life" value="{$prefs.session_lifetime|escape}" /></td>
      </tr><tr>
        <td class="form"><label for="general-proxy">{tr}Use proxy{/tr}:</label></td>
        <td><input type="checkbox" name="use_proxy" id="general-proxy"
              {if $prefs.use_proxy eq 'y'}checked="checked"{/if}/>
        </td>
      </tr><tr>
        <td class="form"><label for="general-proxy_host">{tr}Proxy Host{/tr}:</label></td>
        <td><input type="text" name="proxy_host" id="general-proxy_host" value="{$prefs.proxy_host|escape}" size="40" /></td>
      </tr><tr>
        <td class="form"><label for="general-proxy_port">{tr}Proxy port{/tr}:</label></td>
        <td><input size="5" type="text" name="proxy_port" id="general-proxy_port" value="{$prefs.proxy_port|escape}" /></td>
      </tr><tr>
        <td class="form"><label for="general-max_records">{tr}Maximum number of records in listings{/tr}:</label></td>
        <td><input size="5" type="text" name="maxRecords" id="general-max_records"
                   value="{$prefs.maxRecords|escape}" /></td>

      </tr><tr>
        <td class="form"><label for="feature_help">{tr}Help System{/tr}:</label></td>
        <td><input type="checkbox" name="feature_help" id="general-feature_help" {if $prefs.feature_help eq 'y'}checked="checked"{/if} /></td>

      </tr><tr>
        <td class="form"><label for="general-helpurl">{tr}Help URL{/tr}:</label></td>
        <td><input type="text" name="helpurl" id="general-helpurl" value="{$prefs.helpurl|escape}" size="40" /></td>

      </tr><tr>
        <td class="form"></td>
        <td>{tr}Please expect not found help-pages with the default-URL.{/tr}<br />
	    {tr}Any help with the documentation is welcome.{/tr}</td>
      </tr>

			<tr>
        <td class="heading" colspan="2" align="center"><a name="help">{tr}Release check{/tr}</a></td>
			</tr>
			<tr>
				<td class="form"><label for="general-versioncheck">{tr}Release check{/tr}</label></td>
				<td><input type="checkbox" id="general-versioncheck" name="feature_version_checks" {if $prefs.feature_version_checks eq 'y'}checked="checked"{/if}/></td>
      </tr>
			<tr>
        <td class="form">{tr}Check frequency{/tr}:</td>
        <td>
        <select name="tiki_version_check_frequency">
        <option value="86400"{if $prefs.tiki_version_check_frequency eq 86400} selected="selected"{/if}>{tr}Each day{/tr}</option>
        <option value="604800"{if $prefs.tiki_version_check_frequency eq 604800} selected="selected"{/if}>{tr}Each week{/tr}</option>
        <option value="2592000"{if $prefs.tiki_version_check_frequency eq 2592000} selected="selected"{/if}>{tr}Each month{/tr}</option>
        </select>
        </td>
      </tr>   
			<tr>
        <td class="form"></td>
        <td>{tr}Release checks are made when you browse admin main page only{/tr}
				<br /><a href="tiki-admin.php?page=general&amp;forcecheck=1">{tr}Force immediate check{/tr}</a></td>
      </tr>

			<tr>
        <td class="heading" colspan="2" align="center">{tr}Date and Time Formats{/tr}</td>
      </tr><tr>
        <td class="form" ><label for="general-timezone">{tr}Default timezone{/tr}:</label></td>
        <td ><select name="server_timezone" id="general-timezone">
				{foreach key=tz item=tzinfo from=$timezones}
				{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
				<option value="{$tz}"{if $prefs.server_timezone eq $tz} selected="selected"{/if}>{$tz} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
				{/foreach}
				</select></td>
      </tr><tr>
        <td class="form" ><label for="general-long_date">{tr}Long date format{/tr}:</label></td>
        <td ><input type="text" name="long_date_format" id="general-long_date" value="{$prefs.long_date_format|escape}" size="40"/></td>
      </tr><tr>
        <td class="form"><label for="general-short_date">{tr}Short date format{/tr}:</label></td>
        <td><input type="text" name="short_date_format" id="general-short_date" value="{$prefs.short_date_format|escape}" size="40"/></td>
      </tr><tr>
        <td class="form"><label for="general-long_time">{tr}Long time format{/tr}:</label></td>
        <td><input type="text" name="long_time_format" id="general-long_time" value="{$prefs.long_time_format|escape}" size="40"/></td>
      </tr><tr>
        <td class="form"><label for="general-short_time">{tr}Short time format{/tr}:</label></td>
        <td><input type="text" name="short_time_format" id="general-short_time" value="{$prefs.short_time_format|escape}" size="40"/></td>
      </tr><tr>
        <td class="form" ><label for="general-display_fieldorder">{tr}Fields display order{/tr}:</label></td>
        <td ><select name="display_field_order" id="general-display_fieldorder">
        	<option value="DMY" {if $prefs.display_field_order=="DMY"}selected="selected"{/if}>{tr}Day{/tr} {tr}Month{/tr} {tr}Year{/tr}</option>
            <option value="DYM" {if $prefs.display_field_order=="DYM"}selected="selected"{/if}>{tr}Day{/tr} {tr}Year{/tr} {tr}Month{/tr}</option>
            <option value="MDY" {if $prefs.display_field_order=="MDY"}selected="selected"{/if}>{tr}Month{/tr} {tr}Day{/tr} {tr}Year{/tr}</option>
            <option value="MYD" {if $prefs.display_field_order=="MYD"}selected="selected"{/if}>{tr}Month{/tr} {tr}Year{/tr} {tr}Day{/tr}</option>
            <option value="YDM" {if $prefs.display_field_order=="YDM"}selected="selected"{/if}>{tr}Year{/tr} {tr}Day{/tr} {tr}Month{/tr}</option>
            <option value="YMD" {if $prefs.display_field_order=="YMD"}selected="selected"{/if}>{tr}Year{/tr} {tr}Month{/tr} {tr}Day{/tr}</option>
        </select></td>
      </tr><tr>
        {assign var="fcnlink" value="http://www.php.net/manual/en/function.strftime.php"}
        <td colspan="2" align="center">
          <a class="link" target="strftime" href="{$fcnlink}">
            {tr}Date and Time Format Help{/tr}</a></td>
      </tr>

				<tr>
					<td class="heading" colspan="2" align="center">{tr}Other{/tr}</td>
				</tr>
				<tr>
				<td class="form"><label for="user_show_realnames">{tr}When possible, show the real user name instead of login:{/tr}</label></td>
				<td><input type="checkbox" name="user_show_realnames" id="user_show_realnames" {if $prefs.user_show_realnames eq 'y'}checked="checked"{/if}/></td>
				</tr>
				
				<tr>
					<td colspan="2"><hr/></td>
				</tr>

				<tr>
					<td class="form"><label for="site_crumb_seper">{tr}Locations separator{/tr}:</label></td>
					<td>
						<div style="float: left"><input type="text" name="site_crumb_seper" id="site_crumb_seper" value="{$prefs.site_crumb_seper}" size="5" maxlength="8" /></div>
						<div>
							&nbsp; <small><em>{tr}Examples{/tr}</em>: &nbsp; &raquo; &nbsp; / &nbsp; &gt; &nbsp; : &nbsp; -> &nbsp; &#8594;</small>
						</div>
					</td>
				</tr>
                                <tr>
                                        <td class="form"><label for="site_nav_seper">{tr}Choices separator{/tr}:</label></td>
                                        <td>
                                                <div style="float: left"><input type="text" name="site_nav_seper" id="site_nav_seper" value="{$prefs.site_nav_seper}" size="5" maxlength="8" /></div>
                                                <div>
                                                        &nbsp; <small><em>{tr}Examples{/tr}</em>: &nbsp; | &nbsp; / &nbsp; &brvbar; &nbsp; : </small>
                                                </div>
                                        </td>
                                </tr>
<tr>
<td class="form"><label for="site_nav_seper">{tr}Preference syntax{/tr}:</label></td>
<td><select name="pref_syntax">
<option value="1.9" {if $prefs.pref_syntax eq '1.9'}selected="selected"{/if}>1.9 - $feature_foobar</option>
<option value="1.10" {if $prefs.pref_syntax eq '1.10'}selected="selected"{/if}>1.10 - $prefs.feature_foobar, $prefs['feature_foobar']</option>
</select></td>
</tr>

      <tr>
        <td class="button" colspan='2'>
          <input type="submit" name="new_prefs" value="{tr}Change preferences{/tr}" />
        </td>
      </tr></table>
    </form>
  </div>
</div>

<div class="simplebox">
<div class="cbox">
  <div class="cbox-title">
    {tr}Change admin password{/tr}
  </div>
  <div class="cbox-data">
    <form method="post" action="tiki-admin.php?page=general">
      <table class="admin"><tr>
        <td class="form" ><label for="general-new_pass">{tr}New password{/tr}:</label></td>
        <td ><input type="password" name="adminpass" id="general-new_pass" /></td>
      </tr><tr>
        <td class="form"><label for="general-repeat_pass">{tr}Repeat password{/tr}:</label></td>
        <td><input type="password" name="again" id="general-repeat_pass" /></td>
      </tr><tr>
        <td colspan="2" class="button">
          <input type="submit" name="newadminpass" value="{tr}Change password{/tr}" />
        </td>
      </tr></table>
    </form>
  </div>
</div>
</div>
