{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-user_preferences.tpl,v 1.113.2.9 2007-12-09 08:58:18 pkdille Exp $ *}

<h1>
  {if $userwatch ne $user}
    <a class="pagetitle" href="tiki-user_preferences.php?view_user={$userwatch}">{tr}User Preferences{/tr}: {$userwatch}</a>
  {else}
    <a class="pagetitle" href="tiki-user_preferences.php">{tr}User Preferences{/tr}</a>
  {/if}

  {if $prefs.feature_help eq 'y'}
    <a href="{$prefs.helpurl}User+Preferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences{/tr}">
      <img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}Help{/tr}' />
    </a>
  {/if}

  {if $prefs.feature_view_tpl eq 'y'}
    <a href="tiki-edit_templates.php?template=tiki-user_preferences.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}UserPreferences tpl{/tr}">
      <img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' />
    </a>
  {/if}

  {if $tiki_p_admin_users eq 'y'}
    <a class="link" href="tiki-assignuser.php?assign_user={$userinfo.login}" title="{tr}Assign Group{/tr}">
      <img border="0" alt="{tr}Assign Group{/tr}" src="pics/icons/key.png" width='16' height='16' />
    </a>
  {/if}
</h1>

{if $userwatch eq $user or $userwatch eq ""}
  {if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
    {include file=tiki-mytiki_bar.tpl}
  {/if}
  <br />
{/if}

{if $tikifeedback}
  <div class="simplebox highlight">
    {section name=n loop=$tikifeedback}{$tikifeedback[n].mes}{/section}
  </div>
{/if}

<form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <input type="hidden" name="user" value="{$userwatch|escape}" />

  {cycle values="odd,even" print=false}
  <table class="normal">

    <tr>
      <td class="heading" colspan="2">{tr}Personal Information{/tr}</td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">{tr}User{/tr}:</td>
      <td class="{cycle}">
        {$userinfo.login}
        {if $prefs.login_is_email eq 'y' and $userinfo.login neq 'admin'} 
          <i>({tr}Use the email as username{/tr})</i>
        {/if}
      </td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">
        {tr}Real Name{/tr}:
      </td>
      <td class="{cycle}">
        {if $prefs.auth_ldap_nameattr eq '' || $prefs.auth_method ne 'auth'}
          <input type="text" name="realName" value="{$user_prefs.realName|escape}" />{else}{$user_prefs.realName}
        {/if}
      </td>
    </tr>

    {* this should be optional
      <tr><td class="{cycle advance=false}">{tr}Gender{/tr}:</td>
        <td class="{cycle}">
          <input type="radio" name="gender" value="Male" {if $gender eq 'Male'}checked="checked"{/if}/> {tr}Male{/tr}
          <input type="radio" name="gender" value="Female" {if $gender eq 'Female'}checked="checked"{/if}/> {tr}Female{/tr}
          <input type="radio" name="gender" value="Hidden" {if $gender ne 'Male' and $gender ne 'Female'}checked="checked"{/if}/> {tr}Hidden{/tr}
        </td>
      </tr>
    *}

    <tr>
      <td class="{cycle advance=false}">{tr}Country{/tr}:</td>
      <td class="{cycle}">
        {if $user_prefs.country != "None" && $user_prefs.country != "Other"}
          <img alt="{tr}Flag{/tr}" title="{tr}Flag{/tr}" src="img/flags/{$user_prefs.country}.gif" />
        {/if}
        <select name="country">
          <option value="Other" {if $user_prefs.country eq "Other"}selected="selected"{/if}>{tr}Other{/tr}</option>
          {sortlinks}
            {section name=ix loop=$flags}
              {if $flags[ix] ne "Other"}
                <option value="{$flags[ix]|escape}" {if $user_prefs.country eq $flags[ix]}selected="selected"{/if}>{tr}{$flags[ix]}{/tr}</option>
              {/if}
            {/section}
          {/sortlinks}
        </select>
      </td>
    </tr>
  
    {if $prefs.feature_maps eq 'y' or $prefs.feature_gmap eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}Longitude (WGS84/decimal degrees){/tr}:</td>
        <td class="{cycle}">
          <input type="text" name="lon" value="{$user_prefs.lon|escape}" />
	  {if $prefs.feature_gmap eq 'y'}
            <a href="tiki-gmap_locator.php?for=user{if $userinfo.login ne $user}&amp;view_user={$userinfo.login}{/if}">{tr}Use Google Map locator{/tr}</a>
          {/if}
        </td>
      </tr>
      <tr>
        <td class="{cycle advance=false}">{tr}Latitude (WGS84/decimal degrees){/tr}:</td>
        <td class="{cycle}">
          <input type="text" name="lat" value="{$user_prefs.lat|escape}" />
        </td>
      </tr>
    {/if}

    <tr>
      <td class="{cycle advance=false}">{tr}Avatar{/tr}:</td>
      <td class="{cycle}">
        {$avatar} 
        <a href="tiki-pick_avatar.php{if $userwatch ne $user}?view_user={$userwatch}{/if}" class="link">{tr}Pick user Avatar{/tr}</a>
      </td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">{tr}URL:{/tr}</td>
      <td class="{cycle}">
        <input type="text" size="40" name="homePage" value="{$user_prefs.homePage|escape}" />
      </td>
    </tr>
  
    {if $prefs.feature_wiki eq 'y' and $prefs.feature_wiki_userpage eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}Your personal Wiki Page{/tr}:</td>
        <td class="{cycle}">
          {if $userPageExists eq 'y'}
            <a class="link" href="tiki-index.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}" title="{tr}View{/tr}">{$prefs.feature_wiki_userpage_prefix}{$userinfo.login}</a> 
	    (<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">{tr}Edit{/tr}</a>)
          {else}
            {$prefs.feature_wiki_userpage_prefix}{$userinfo.login} (<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">{tr}Create{/tr}</a>)
          {/if}
        </td>
      </tr>
    {/if}
  
    {if $prefs.userTracker eq 'y' && $usertrackerId}
      <tr>
        <td class="{cycle advance=false}">{tr}Your personal tracker information{/tr}:</td>
        <td class="{cycle}">
	  <a class="link" href="tiki-view_tracker_item.php?view=+user">{tr}View extra information{/tr}</a>
    {/if}

    {* Custom fields *}
    {section name=ir loop=$customfields}
      {if $customfields[ir].show}
        <tr>
          <td class="{cycle advance=false}">{$customfields[ir].label}:</td>
          <td class="{cycle}">
            <input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" />
          </td>
        </tr>
      {/if}
    {/section}

    <tr>
      <td class="heading" colspan="2">{tr}Preferences{/tr}</td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">{tr}Last login{/tr}:</td>
      <td class="{cycle}">{$userinfo.lastLogin|tiki_short_datetime}</td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">{tr}Is email public? (uses scrambling to prevent spam){/tr}</td>
      <td class="{cycle}">
        {if $userinfo.email}
          <select name="email_isPublic">
            {section name=ix loop=$scramblingMethods}
              <option value="{$scramblingMethods[ix]|escape}" {if $user_prefs.email_isPublic eq $scramblingMethods[ix]}selected="selected"{/if}>{$scramblingEmails[ix]}</option>
            {/section}
          </select>
        {else}
          {tr}Unavailable - please set your e-mail below{/tr}
        {/if}
      </td>
    </tr>
    
    <tr>
      <td class="{cycle advance=false}">{tr}Does your mail reader need a special charset{/tr}</td>
      <td class="{cycle}">
        <select name="mailCharset">
          {section name=ix loop=$mailCharsets}
            <option value="{$mailCharsets[ix]|escape}" {if $user_prefs.mailCharset eq $mailCharsets[ix]}selected="selected"{/if}>{$mailCharsets[ix]}</option>
          {/section}
        </select>
      </td>
    </tr>
    
    {if $prefs.change_theme eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}Theme{/tr}:</td>
        <td class="{cycle}">
          <select name="mystyle">
            <option value="{$prefs.site_style}" style="font-style:italic;border-bottom:1px dashed #666;">{tr}Site default{/tr}</option>
              {section name=ix loop=$styles}
                {if count($prefs.available_styles) == 0 || in_array($styles[ix], $prefs.available_styles)}
                  <option value="{$styles[ix]|escape}" {if $user_prefs.theme eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
                {/if}
              {/section}
          </select>

          {if $prefs.feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}
            <a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>
          {/if}
        </td>
      </tr>
    {/if}
  
    {if $prefs.change_language eq 'y'}
      <tr>
        <td  class="{cycle advance=false}">{tr}Language{/tr}:</td>
        <td class="{cycle}">
          <select name="language">
            {section name=ix loop=$languages}
              {if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
                <option value="{$languages[ix].value|escape}"
                  {if $user_prefs.language eq $languages[ix].value}selected="selected"{/if}>
                  {$languages[ix].name}
                </option>
              {/if}
            {/section}
          </select>
        </td>
      </tr>
    {/if}
  
    <tr>
      <td class="{cycle advance=false}">{tr}Number of visited pages to remember{/tr}:</td>
      <td class="{cycle}">
        <select name="userbreadCrumb">
          <option value="1" {if $user_prefs.userbreadCrumb eq 1}selected="selected"{/if}>1</option>
          <option value="2" {if $user_prefs.userbreadCrumb eq 2}selected="selected"{/if}>2</option>
          <option value="3" {if $user_prefs.userbreadCrumb eq 3}selected="selected"{/if}>3</option>
          <option value="4" {if $user_prefs.userbreadCrumb eq 4}selected="selected"{/if}>4</option>
          <option value="5" {if $user_prefs.userbreadCrumb eq 5}selected="selected"{/if}>5</option>
          <option value="10" {if $user_prefs.userbreadCrumb eq 10}selected="selected"{/if}>10</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="{cycle advance=false}">{tr}Displayed time zone{/tr}:</td>
      <td class="{cycle}">
        <select name="display_timezone" id="display_timezone">
	  <option value="" style="font-style:italic;">{tr}Detect user timezone if browser allows, otherwise site default{/tr}</option>
	  <option value="Site" style="font-style:italic;border-bottom:1px dashed #666;"{if $user_prefs.display_timezone eq 'Site'} selected="selected"{/if}>{tr}Site default{/tr}</option>
          {foreach key=tz item=tzinfo from=$timezones}
            {math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}
            {math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
            <option value="{$tz|escape}"{if $user_prefs.display_timezone eq $tz} selected="selected"{/if}>{$tz|escape} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
          {/foreach}
        </select>
      </td>
    </tr>
  
    <tr>
      <td class="{cycle advance=false}">{tr}User information{/tr}:</td>
      <td class="{cycle}">
        <select name="user_information">
          <option value='private' {if $user_prefs.user_information eq 'private'}selected="selected"{/if}>{tr}private{/tr}</option>
          <option value='public' {if $user_prefs.user_information eq 'public'}selected="selected"{/if}>{tr}public{/tr}</option>
        </select>
      </td>
    </tr>
  
    {if $prefs.feature_wiki eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}Use double-click to edit pages{/tr}:</td>
        <td class="{cycle}">
          <input type="checkbox" name="user_dbl" {if $user_prefs.user_dbl eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
      {* not used 
        {if $prefs.feature_history eq 'y'}
          <tr>
            <td class="{cycle advance=false}">Use new diff any version interface:</td>
            <td class="{cycle}">
              <input type="checkbox" name="diff_versions" {if $user_prefs.diff_versions eq 'y'}checked="checked"{/if} />
            </td>
          </tr>
        {/if} 
      *}
    {/if}
  
    {if $prefs.feature_community_mouseover eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}Show user's info on mouseover{/tr}:</td>
        <td class="{cycle}">
          <input type="checkbox" name="show_mouseover_user_info" {if $show_mouseover_user_info eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
      <tr>
        <td class="heading" colspan="2">{tr}User Messages{/tr}</td>
      </tr>
    
      <tr>
        <td class="{cycle advance=false}">{tr}Messages per page{/tr}</td>
        <td class="{cycle}">
          <select name="mess_maxRecords">
            <option value="2" {if $user_prefs.mess_maxRecords eq 2}selected="selected"{/if}>2</option>
            <option value="5" {if $user_prefs.mess_maxRecords eq 5}selected="selected"{/if}>5</option>
            <option value="10" {if $user_prefs.mess_maxRecords eq 10}selected="selected"{/if}>10</option>
            <option value="20" {if $user_prefs.mess_maxRecords eq 20}selected="selected"{/if}>20</option>
            <option value="30" {if $user_prefs.mess_maxRecords eq 30}selected="selected"{/if}>30</option>
            <option value="40" {if $user_prefs.mess_maxRecords eq 40}selected="selected"{/if}>40</option>
            <option value="50" {if $user_prefs.mess_maxRecords eq 50}selected="selected"{/if}>50</option>
          </select>
        </td>
      </tr>

      {if $prefs.allowmsg_is_optional eq 'y'}
        <tr>
          <td class="{cycle advance=false}">{tr}Allow messages from other users{/tr}</td>
          <td class="{cycle}"><input type="checkbox" name="allowMsgs" {if $user_prefs.allowMsgs eq 'y'}checked="checked"{/if}/></td>
        </tr>
      {/if}

      <tr>
        <td class="{cycle advance=false}">{tr}Note author when reading his mail{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mess_sendReadStatus" {if $user_prefs.mess_sendReadStatus eq 'y'}checked="checked"{/if}/>
        </td>
      </tr>

      <tr>
        <td class="{cycle advance=false}">{tr}Send me an email for messages with priority equal or greater than{/tr}:</td>
        <td class="{cycle}">
          <select name="minPrio">
            <option value="1" {if $user_prefs.minPrio eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
            <option value="2" {if $user_prefs.minPrio eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
            <option value="3" {if $user_prefs.minPrio eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
            <option value="4" {if $user_prefs.minPrio eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
            <option value="5" {if $user_prefs.minPrio eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
            <option value="6" {if $user_prefs.minPrio eq 6}selected="selected"{/if}>{tr}none{/tr}</option>
          </select>
        </td>
      </tr>

      <tr>
        <td class="{cycle advance=false}">{tr}Auto-archive read messages after x days{/tr}</td>
        <td class="{cycle}">
          <select name="mess_archiveAfter">
            <option value="0" {if $user_prefs.mess_archiveAfter eq 0}selected="selected"{/if}>{tr}never{/tr}</option>
            <option value="1" {if $user_prefs.mess_archiveAfter eq 1}selected="selected"{/if}>1</option>
            <option value="2" {if $user_prefs.mess_archiveAfter eq 2}selected="selected"{/if}>2</option>
            <option value="5" {if $user_prefs.mess_archiveAfter eq 5}selected="selected"{/if}>5</option>
            <option value="10" {if $user_prefs.mess_archiveAfter eq 10}selected="selected"{/if}>10</option>
            <option value="20" {if $user_prefs.mess_archiveAfter eq 20}selected="selected"{/if}>20</option>
            <option value="30" {if $user_prefs.mess_archiveAfter eq 30}selected="selected"{/if}>30</option>
            <option value="40" {if $user_prefs.mess_archiveAfter eq 40}selected="selected"{/if}>40</option>
            <option value="50" {if $user_prefs.mess_archiveAfter eq 50}selected="selected"{/if}>50</option>
            <option value="60" {if $user_prefs.mess_archiveAfter eq 60}selected="selected"{/if}>60</option>
          </select>
        </td>
      </tr>
    {/if}

    {if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      <tr>
        <td class="heading" colspan="2">{tr}User Tasks{/tr}</td>
      </tr>
    
      <tr>
        <td class="{cycle advance=false}">{tr}Tasks per page{/tr}</td>
        <td class="{cycle}">
          <select name="tasks_maxRecords">
            <option value="2" {if $user_prefs.tasks_maxRecords eq 2}selected="selected"{/if}>2</option>
            <option value="5" {if $user_prefs.tasks_maxRecords eq 5}selected="selected"{/if}>5</option>
            <option value="10" {if $user_prefs.tasks_maxRecords eq 10}selected="selected"{/if}>10</option>
            <option value="20" {if $user_prefs.tasks_maxRecords eq 20}selected="selected"{/if}>20</option>
            <option value="30" {if $user_prefs.tasks_maxRecords eq 30}selected="selected"{/if}>30</option>
            <option value="40" {if $user_prefs.tasks_maxRecords eq 40}selected="selected"{/if}>40</option>
            <option value="50" {if $user_prefs.tasks_maxRecords eq 50}selected="selected"{/if}>50</option>
          </select>
        </td>
      </tr>
    {/if}

    <tr>
      <td class="heading" colspan="2">{tr}My Tiki{/tr}</td>
    </tr>

    {if $prefs.feature_wiki eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My pages{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_pages" {if $user_prefs.mytiki_pages eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_blogs eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My blogs{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_blogs" {if $user_prefs.mytiki_blogs eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_galleries eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My galleries{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_gals" {if $user_prefs.mytiki_gals eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_messages eq 'y'and $tiki_p_messages eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My messages{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_msgs" {if $user_prefs.mytiki_msgs eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My tasks{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_tasks" {if $user_prefs.mytiki_tasks eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_trackers eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My items{/tr}</td>
        <td class="{cycle}">
          <input type="checkbox" name="mytiki_items" {if $user_prefs.mytiki_items eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_workflow eq 'y'}
      {if $tiki_p_use_workflow eq 'y'}
        <tr>
          <td class="{cycle advance=false}">{tr}My workflow{/tr}</td>
          <td class="{cycle}">
            <input type="checkbox" name="mytiki_workflow" {if $user_prefs.mytiki_workflow eq 'y'}checked="checked"{/if} />
          </td>
        </tr>
      {/if}
    {/if}

    {if $prefs.feature_userlevels eq 'y'}
      <tr>
        <td class="{cycle advance=false}">{tr}My level{/tr}</td>
        <td class="{cycle}">
          <select name="mylevel">
            {foreach key=levn item=lev from=$prefs.userlevels}
	      <option value="{$levn}"{if $user_prefs.mylevel eq $levn} selected="selected"{/if}>{$lev}</option>
	    {/foreach}
          </select>
        </td>
      </tr>
    {/if}

    <tr>
      <td colspan="2" class="button"><input type="submit" name="new_prefs" value="{tr}Change preferences{/tr}" /></td>
    </tr>
  </table>
</form>


{if $prefs.change_password neq 'n' or ! ($prefs.login_is_email eq 'y' and $userinfo.login neq 'admin')}
  <br />
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="normal">
    <tr>
      <td class="heading" colspan="2">{tr}Account Information{/tr}</td>
    </tr>
    
    {if $prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')}
      {if $prefs.change_password neq 'n' and ($prefs.login_is_email ne 'y' or $userinfo.login eq 'admin') }
        <tr>
          <td class="{cycle advance=false}" colspan="2">{tr}Leave "New password" and "Confirm new password" fields blank to keep current password{/tr}</td>
        </tr>
      {/if}
    {/if}
  
      {if $prefs.login_is_email eq 'y' and $userinfo.login neq 'admin'}
        <input type="hidden" name="email" value="{$userinfo.email|escape}" />
      {else}
        <tr>
          <td class="{cycle advance=false}">{tr}Email address{/tr}:</td>
          <td class="{cycle}"><input type="text" name="email" value="{$userinfo.email|escape}" /></td>
        </tr>
      {/if}

      {if $prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')}
        {if $prefs.change_password neq 'n'}
          <tr>
            <td class="{cycle advance=false}">{tr}New password{/tr}:</td>
            <td class="{cycle}"><input type="password" name="pass1" /></td>
          </tr>
  
          <tr>
            <td class="{cycle advance=false}">{tr}Confirm new password{/tr}:</td>
            <td class="{cycle}"><input type="password" name="pass2" /></td>
          </tr>
        {/if}
      
        {if $tiki_p_admin ne 'y' or $userwatch eq $user}
          <tr>
            <td class="{cycle advance=false}">{tr}Current password (required){/tr}:</td>
            <td class="{cycle}"><input type="password" name="pass" /></td>
          </tr>
        {/if}
      {/if}
    
      <tr>
        <td colspan="2" class="button"><input type="submit" name="chgadmin" value="{tr}Change administrative info{/tr}" /></td>
      </tr>
    </table>
  </form>
{/if}

