<h1>{if $userwatch ne $user}<a class="pagetitle" href="tiki-user_preferences.php?view_user={$userwatch}">{tr}User Preferences{/tr}: {$userwatch}</a>{else}<a class="pagetitle" href="tiki-user_preferences.php">{tr}User Preferences{/tr}</a>{/if}

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Preferences" target="tikihelp" class="tikihelp" title="{tr}User Preferences{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}'></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_preferences.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}UserPreferences tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}'></a>
{/if}</h1>

{if $userwatch eq $user or $userwatch eq ""}{include file=tiki-mytiki_bar.tpl}{/if}
<br />
{if $tikifeedback}
<div class="simplebox highlight">{section name=n loop=$tikifeedback}{$tikifeedback[n].mes}<br />{/section}</div>
{else}
<br />
{/if}
<table class="admin">
<tr>
  <!--The line below was <td valign="top" > for no real reason-->
  <td valign="top">

{if $feature_tabs eq 'y'}

{cycle values="1,2,3" name=tabs print=false advance=false}
<div id="page-bar">
 <span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Personal Information{/tr}</a></span>
 <span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}General preferences{/tr}</a></span>
 <span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},4);">{tr}Other preferences{/tr}</a></span>
</div>


{/if}

{cycle name=content values="1,2,3" print=false advance=false}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

  <div class="cbox">
  <div class="cbox-title">{tr}Personal Information{/tr}</div>
  <div class="cbox-data">
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <input type="hidden" name="user" value="{$userwatch|escape}" /> 
  <table class="admin">
  <tr><td class="form">{tr}Name{/tr}:</td><td class="form">{$userinfo.login}</td></tr>
  <tr><td class="form">{tr}Real Name{/tr}:</td><td class="form"><input type="text" name="realName" value="{$realName|escape}" /></td></tr>
  <tr><td class="form">{tr}Country{/tr}:</td><td class="form">
  {if $country == "None"}
  <img src="img/flags/Other.gif" border="0" width="20" height="13" alt='{tr}flag{/tr}' title='{tr}flag{/tr}'>
  {else}
  <img alt="{tr}flag{/tr}" title="{tr}flag{/tr}" src="img/flags/{$country}.gif" />
  {/if}
<select name="country">
  <option value="Other" {if $country eq "Other"}selected="selected"{/if}>{tr}Other{/tr}</option>
  {sortlinks}
  {section name=ix loop=$flags}
  {if $flags[ix] ne "Other"}
  <option value="{$flags[ix]|escape}" {if $country eq $flags[ix]}selected="selected"{/if}>{tr}{$flags[ix]}{/tr}</option>
  {/if}
  {/section}
  {/sortlinks}
  </select>
  </td></tr>
  
  <tr><td class="form">{tr}Longitude (WGS84/decimal degrees){/tr}:</td><td class="form"><input type="text" name="lon" value="{$lon|escape}" /></td></tr>
  <tr><td class="form">{tr}Latitude (WGS84/decimal degrees){/tr}:</td><td class="form"><input type="text" name="lat" value="{$lat|escape}" /></td></tr>
  
  <tr><td class="form">{tr}Avatar{/tr}:</td><td class="form">{$avatar} <a href="tiki-pick_avatar.php" class="link">{tr}Pick user Avatar{/tr}</a></td></tr>
  <tr><td class="form">{tr}HomePage{/tr}:</td><td class="form"><input type="text" size="40" name="homePage" value="{$homePage|escape}" /></td></tr>
  {if $feature_wiki eq 'y' and $feature_wiki_userpage eq 'y'}
  <tr><td class="form">{tr}Your personal Wiki Page{/tr}:</td><td class="form"><a class="link" href="tiki-index.php?page={$feature_wiki_userpage_prefix}{$userinfo.login}">{$feature_wiki_userpage_prefix}{$userinfo.login}</a> 
	(<a class="link" href="tiki-editpage.php?page={$feature_wiki_userpage_prefix}{$userinfo.login}">{tr}edit{/tr}</a>)</td></tr>
  {/if}
	{if $userTracker eq 'y' && $usertrackerId}
  <tr><td class="form">{tr}Your personal tracker information{/tr}:</td><td class="form">
	{if $useritemId}
	<a class="link" href="tiki-view_tracker_item.php?trackerId={$usertrackerId}&amp;itemId={$useritemId}&amp;show=mod">{tr}Edit information{/tr}</a>
	{else}
	<a class="link" href="tiki-view_tracker_item.php?view=+user">{tr}View extra information{/tr}</a>
	{/if}
	{/if}
  <tr>
  <td class="form">{tr}Personal portfolio{/tr}:</td>
  <td class="form">
    <img src="images/workspaces/edu_workspace.png"> <a class="link" href="tiki-workspaces_desktop.php?workspaceCode=PWS{$userinfo.login}">{$userinfo.login} {tr}Personal portfolio{/tr}</a>
  </td>
  </tr>
  {* Custom fields *}
  {section name=ir loop=$customfields}
    <tr><td class="form">{$customfields[ir].prefName}:</td>
        <td class="form"><input type="text" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" /></td>
    </tr>
  {/section}

  <tr><td colspan="2" class="button"><input type="submit" name="info" value="{tr}Change information{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  </div>

</div>


<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

  <div class="cbox">
  <div class="cbox-title">{tr}Preferences{/tr}</div>
  <div class="cbox-data">
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <input type="hidden" name="user" value="{$userwatch|escape}" /> 
  <table class="admin">
  <tr><td class="form">{tr}Last login{/tr}:</td><td class="form">{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
  <tr><td class="form">{tr}Is email public? (uses scrambling to prevent spam){/tr}</td><td class="form">
{if $userinfo.email}
  <select name="email_isPublic">
   {section name=ix loop=$scramblingMethods}
      <option value="{$scramblingMethods[ix]|escape}" {if $email_isPublic eq $scramblingMethods[ix]}selected="selected"{/if}>{$scramblingEmails[ix]}</option>
   {/section}
  </select>
{else}
  {tr}Unavailable - please set your e-mail below{/tr}
{/if}
  </td></tr>
  <tr><td class="form">{tr}Does your mail reader need a special charset{/tr}</td>
  <td class="form">
  <select name="mailCharset">
   {section name=ix loop=$mailCharsets}
      <option value="{$mailCharsets[ix]|escape}" {if $mailCharset eq $mailCharsets[ix]}selected="selected"{/if}>{$mailCharsets[ix]}</option>
   {/section}
  </select>
  </td></tr>
  {if $change_theme eq 'y'}
  <tr><td class="form">{tr}Theme{/tr}:</td><td class="form"><select name="mystyle">
    {section name=ix loop=$styles}
      {if count($available_styles) == 0 || in_array($styles[ix], $available_styles)}
        <option value="{$styles[ix]|escape}" {if $style eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
      {/if}
    {/section}
        </select>
		{if $feature_editcss eq 'y' and $tiki_p_create_css eq 'y'}
			<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>
		{/if}
				</td></tr>
  {/if}
  {if $change_language eq 'y'}
  <tr><td  class="form">{tr}Language{/tr}:</td><td class="form">
        <select name="language">
    {section name=ix loop=$languages}
      {if count($available_languages) == 0 || in_array($languages[ix].value, $available_languages)}
        <option value="{$languages[ix].value|escape}"
          {if $langUser eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
      {/if}
    {/section}
        </select></td></tr>
  {/if}      
  <tr><td class="form">{tr}Number of visited pages to remember{/tr}:</td><td class="form">
  <select name="userbreadCrumb">
  <option value="1" {if $userbreadCrumb eq 1}selected="selected"{/if}>1</option>
  <option value="2" {if $userbreadCrumb eq 2}selected="selected"{/if}>2</option>
  <option value="3" {if $userbreadCrumb eq 3}selected="selected"{/if}>3</option>
  <option value="4" {if $userbreadCrumb eq 4}selected="selected"{/if}>4</option>
  <option value="5" {if $userbreadCrumb eq 5}selected="selected"{/if}>5</option>
  <option value="10" {if $userbreadCrumb eq 10}selected="selected"{/if}>10</option>
  </select>
  </td></tr>
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td>
  <td class="form">
  <input type="radio" name="display_timezone" value="UTC" {if $display_timezone eq 'UTC'}checked="checked"{/if}/> {tr}UTC{/tr}
  <input type="radio" name="display_timezone" value="Local" {if $display_timezone ne 'UTC'}checked="checked"{/if}/> {tr}Local{/tr}
  </td>
  </tr>
  <tr><td class="form">{tr}User information{/tr}:</td><td class="form">
  <select name="user_information">
    <option value='private' {if $user_information eq 'private'}selected="selected"{/if}>{tr}private{/tr}</option>
    <option value='public' {if $user_information eq 'public'}selected="selected"{/if}>{tr}public{/tr}</option>
  </select>
  </td></tr>
  {if $feature_wiki eq 'y'}
  <tr><td class="form">{tr}Use double-click to edit pages{/tr}:</td>
  <td class="form">
  <input type="checkbox" name="user_dbl" {if $user_dbl eq 'y'}checked="checked"{/if} />
  </td>
  </tr>
  {* not used {if $feature_history eq 'y'}
  <tr><td class="form">Use new diff any version interface:</td>
  <td class="form">
  <input type="checkbox" name="diff_versions" {if $diff_versions eq 'y'}checked="checked"{/if} />
  </td>
  </tr>
  {/if} *}
  {/if}
  {if $feature_community_mouseover eq 'y'}
  <tr><td class="form">{tr}Show user's info on mouseover{/tr}:</td>
  <td class="form">
  <input type="checkbox" name="show_mouseover_user_info" {if $show_mouseover_user_info eq 'y'}checked="checked"{/if} />
  </td>
  </tr>
  {/if}

  <tr><td colspan="2" class="button"><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  </div>

  <div class="cbox">
  <div class="cbox-title">{tr}Account Information{/tr}</div>
  <div class="cbox-data">
  {if $auth_method neq 'cas' || ($cas_skip_admin eq 'y' && $user eq 'admin')}
  {if $change_password neq 'n'}{tr}Leave "New password" and "Confirm new password" fields blank to keep current password{/tr}{/if}
  {/if}
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="admin">
  <tr><td class="form">{tr}Email address{/tr}:</td><td class="form"><input type="text" name="email" value="{$userinfo.email|escape}" /></td></tr>
{if $auth_method neq 'cas' || ($cas_skip_admin eq 'y' && $user eq 'admin')}
  {if $change_password neq 'n'}
  <tr><td class="form">{tr}New password{/tr}:</td><td class="form"><input type="password" name="pass1" /></td></tr>
  <tr><td class="form">{tr}Confirm new password{/tr}:</td><td class="form"><input type="password" name="pass2" /></td></tr>
  {/if}
  {if $tiki_p_admin ne 'y' or $userwatch eq $user}
    <tr><td class="form">{tr}Current password (required){/tr}:</td><td class="form"><input type="password" name="pass" /></td></tr>
  {/if}
{/if}
  <tr><td colspan="2" class="button"><input type="submit" name="chgadmin" value="{tr}Change administrative info{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  </div>

</div>


<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
  <div class="cbox">
  <div class="cbox-title">{tr}User Messages{/tr}</div>
  <div class="cbox-data">
        <form action="tiki-user_preferences.php" method="post">
<table class="admin">
<tr>
  <td class="form">{tr}Messages per page{/tr}</td>
  <td class="form">
    <select name="mess_maxRecords">
      <option value="2" {if $mess_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $mess_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $mess_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $mess_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $mess_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $mess_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $mess_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>
  </td>
</tr>
<tr>
  <td class="form">{tr}Allow messages from other users{/tr}</td>
  <td class="form"><input type="checkbox" name="allowMsgs" {if $allowMsgs eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
  <td class="form">{tr}Note author when reading his mail{/tr}</td>
  <td class="form"><input type="checkbox" name="mess_sendReadStatus" {if $mess_sendReadStatus eq 'y'}checked="checked"{/if}/></td>
</tr>
<tr>
  <td class="form">{tr}Send me an email for messages with priority equal or greater than{/tr}:</td>
  <td class="form">
    <select name="minPrio">
      <option value="1" {if $minPrio eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $minPrio eq 2}selected="selected"{/if}>2</option>
      <option value="3" {if $minPrio eq 3}selected="selected"{/if}>3</option>
      <option value="4" {if $minPrio eq 4}selected="selected"{/if}>4</option>
      <option value="5" {if $minPrio eq 5}selected="selected"{/if}>5</option>
      <option value="6" {if $minPrio eq 6}selected="selected"{/if}>{tr}none{/tr}</option>
    </select>
  </td>
</tr>
<tr>
  <td class="form">{tr}Auto-archive read messages after x days{/tr}</td>
  <td class="form">
    <select name="mess_archiveAfter">
      <option value="0" {if $mess_archiveAfter eq 0}selected="selected"{/if}>{tr}never{/tr}</option>
      <option value="1" {if $mess_archiveAfter eq 1}selected="selected"{/if}>1</option>
      <option value="2" {if $mess_archiveAfter eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $mess_archiveAfter eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $mess_archiveAfter eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $mess_archiveAfter eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $mess_archiveAfter eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $mess_archiveAfter eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $mess_archiveAfter eq 50}selected="selected"{/if}>50</option>
      <option value="60" {if $mess_archiveAfter eq 60}selected="selected"{/if}>60</option>
    </select>
  </td>
</tr>
<tr>
  <td colspan="2" class="button"><input type="submit" name="messprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>
</div>
</div>

  {/if}

{if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}

  <div class="cbox">
  <div class="cbox-title">{tr}User Tasks{/tr}</div>
  <div class="cbox-data">
        <form action="tiki-user_preferences.php" method="post">
<table class="admin">
<tr>
  <td class="form">{tr}Tasks per page{/tr}</td>
  <td class="form">
    <select name="tasks_maxRecords">
      <option value="2" {if $tasks_maxRecords eq 2}selected="selected"{/if}>2</option>
      <option value="5" {if $tasks_maxRecords eq 5}selected="selected"{/if}>5</option>
      <option value="10" {if $tasks_maxRecords eq 10}selected="selected"{/if}>10</option>
      <option value="20" {if $tasks_maxRecords eq 20}selected="selected"{/if}>20</option>
      <option value="30" {if $tasks_maxRecords eq 30}selected="selected"{/if}>30</option>
      <option value="40" {if $tasks_maxRecords eq 40}selected="selected"{/if}>40</option>
      <option value="50" {if $tasks_maxRecords eq 50}selected="selected"{/if}>50</option>
    </select>
  </td>
</tr>



<tr>
  <td colspan="2" class="button"><input type="submit" name="tasksprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>

</div>
</div>

  {/if}

  <div class="cbox">
  <div class="cbox-title"><a href="tiki-my_tiki.php">{tr}My Tiki{/tr}</a></div>
  <div class="cbox-data">

        <form action="tiki-user_preferences.php" method="post">
<table class="admin">

{if $feature_wiki eq 'y'}
<tr><td class="form">{tr}My pages{/tr}</td><td class="form"><input type="checkbox" name="mytiki_pages" {if $mytiki_pages eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_blogs eq 'y'}
<tr><td class="form">{tr}My blogs{/tr}</td><td class="form"><input type="checkbox" name="mytiki_blogs" {if $mytiki_blogs eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_galleries eq 'y'}
<tr><td class="form">{tr}My galleries{/tr}</td><td class="form"><input type="checkbox" name="mytiki_gals" {if $mytiki_gals eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_messages eq 'y'and $tiki_p_messages eq 'y'}
<tr><td class="form">{tr}My messages{/tr}</td><td class="form"><input type="checkbox" name="mytiki_msgs" {if $mytiki_msgs eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
<tr><td class="form">{tr}My tasks{/tr}</td><td class="form"><input type="checkbox" name="mytiki_tasks" {if $mytiki_tasks eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_trackers eq 'y'}
<tr><td class="form">{tr}My items{/tr}</td><td class="form"><input type="checkbox" name="mytiki_items" {if $mytiki_items eq 'y'}checked="checked"{/if} /></td></tr>
{/if}

{if $feature_workflow eq 'y'}
  {if $tiki_p_use_workflow eq 'y'}
    <tr><td class="form">{tr}My workflow{/tr}</td><td class="form"><input type="checkbox" name="mytiki_workflow" {if $mytiki_workflow eq 'y'}checked="checked"{/if} /></td></tr>
  {/if}
{/if}
<tr>
  <td colspan="2" class="button"><input type="submit" name="mytikiprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>

</div>
</div>

</div>

</td>
</tr>
</table>

