<a class="pagetitle" href="tiki-user_preferences.php">{tr}User Preferences{/tr}</a>

{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=UserPreferences" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}User Preferences{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-user_preferences.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}UserPreferences tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}

{include file=tiki-mytiki_bar.tpl}
<br /><br />
<table class="admin">
<tr>
  <!--The line below was <td valign="top" > for no real reason-->
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <input type="hidden" name="user" value="{$userwatch|escape}" /> 
  <table>
  <tr><td class="form">{tr}Name{/tr}:</td><td>{$userinfo.login}</td></tr>
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
  <tr><td class="form">{tr}Is email public? (uses scrambling to prevent spam){/tr}</td><td>
  <select name="email_isPublic">
   {section name=ix loop=$scramblingMethods}
      <option value="{$scramblingMethods[ix]|escape}" {if $email_isPublic eq $scramblingMethods[ix]}selected="selected"{/if}>{$scramblingEmails[ix]}</option>
   {/section}
  </select>
  </td></tr>
  </td></tr>
  <tr><td class="form">{tr}Country{/tr}:</td><td>
  <img alt="{tr}flag{/tr}" title="{tr}flag{/tr}" src="img/flags/{$country}.gif" />
  <select name="country">
  {sortlinks}
  {section name=ix loop=$flags}  
  <option value="{$flags[ix]|escape}" {if $country eq $flags[ix]}selected="selected"{/if}>{tr}{$flags[ix]}{/tr}</option>  
  {/section}
  {/sortlinks}
  </select>
  </td></tr>
  {if $change_theme eq 'y'}
  <tr><td class="form">{tr}Theme{/tr}:</td><td><select name="style">
        {section name=ix loop=$styles}
        <option value="{$styles[ix]|escape}" {if $style eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
        {/section}
        </select>
		{if $feature_editcss and $tiki_p_create_css}
			<a href="tiki-edit_css.php" class="link" title="{tr}Edit CSS{/tr}">{tr}Edit CSS{/tr}</a>
		{/if}
				</td></tr>
  {/if}
  {if $change_language eq 'y'}      
  <tr><td  class="form">{tr}Language{/tr}:</td><td>
        <select name="language">
        {section name=ix loop=$languages}
        <option value="{$languages[ix].value|escape}"
          {if $language eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
        {/section}
        </select></td></tr>
  {/if}      
  <tr><td class="form">{tr}Real Name{/tr}:</td><td><input type="text" name="realName" value="{$realName|escape}" /></td></tr>
  <tr><td class="form">{tr}Avatar{/tr}:</td><td>{$avatar} <a href="tiki-pick_avatar.php" class="link">{tr}Pick user Avatar{/tr}</a></td></tr>
  <tr><td class="form">{tr}Number of visited pages to remember{/tr}:</td><td>
  <select name="userbreadCrumb">
  <option value="1" {if $userbreadCrumb eq 1}selected="selected"{/if}>1</option>
  <option value="2" {if $userbreadCrumb eq 2}selected="selected"{/if}>2</option>
  <option value="3" {if $userbreadCrumb eq 3}selected="selected"{/if}>3</option>
  <option value="4" {if $userbreadCrumb eq 4}selected="selected"{/if}>4</option>
  <option value="5" {if $userbreadCrumb eq 5}selected="selected"{/if}>5</option>
  <option value="10" {if $userbreadCrumb eq 10}selected="selected"{/if}>10</option>
  </select>
  </td></tr>
  <tr><td class="form">{tr}HomePage{/tr}:</td><td><input type="text" name="homePage" value="{$homePage|escape}" /></td></tr>
  {if $feature_wiki eq 'y'}
  <tr><td class="form">{tr}Your personal Wiki Page{/tr}:</td><td><a class="link" href="tiki-index.php?page=UserPage{$userinfo.login}">UserPage{$userinfo.login}</a> (<a class="link" href="tiki-editpage.php?page=UserPage{$userinfo.login}">{tr}edit{/tr}</a>)</td></tr>
  {/if}
  
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td>
  <td>
  <input type="radio" name="display_timezone" value="UTC" {if $display_timezone eq 'UTC'}checked="checked"{/if}/> {tr}UTC{/tr}
  <input type="radio" name="display_timezone" value="Local" {if $display_timezone ne 'UTC'}checked="checked"{/if}/> {tr}Local{/tr}
  </td>
  </tr>
  <tr><td class="form">{tr}User information{/tr}:</td><td>
  <select name="user_information">
    <option value='private' {if $user_information eq 'private'}selected="selected"{/if}>{tr}private{/tr}</option>
    <option value='public' {if $user_information eq 'public'}selected="selected"{/if}>{tr}public{/tr}</option>
  </select>
  </td></tr>
  {if $feature_wiki eq 'y'}
  <tr><td class="form">{tr}Use dbl click to edit pages{/tr}:</td>
  <td>
  <input type="checkbox" name="user_dbl" {if $user_dbl eq 'y'}checked="checked"{/if} />
  </td>
  </tr>
  {/if}

  {* Custom fields *}
  {section name=ir loop=$customfields}
    <tr><td class="form">{$customfields[ir].prefName}:</td>
        <td><input type="text" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" /></td>
    </tr>
  {/section}

  <tr><td colspan="2" class="button"><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  
  <div class="simplebox">
  {tr}Change your email{/tr}<br />
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="admin">
  <tr><td class="form">{tr}Email{/tr}:</td><td><input type="text" name="email" value="{$userinfo.email|escape}" /></td>
  <tr><td>{tr}Password{/tr}:</td><td><input type="password" name="pass" /></td></tr>
  <tr><td colspan="2" class="button"><input type="submit" name="chgemail" value="{tr}change email{/tr}"></td></tr>
  </table>
  </form>
  </div>

  <div class="simplebox">
  {tr}Change your password{/tr}<br />
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="admin">
  <tr><td>{tr}Old password{/tr}:</td><td><input type="password" name="old" /></td></tr>
  <tr><td>{tr}New password{/tr}:</td><td><input type="password" name="pass1" /></td></tr>
  <tr><td>{tr}Again please{/tr}:</td><td><input type="password" name="pass2" /></td></tr>
  <tr><td colspan="2" class="button"><input type="submit" name="chgpswd" value="{tr}change password{/tr}"></td></tr>
  </table>
  </form>
  </div>
  </div>
  </td>
  <!--The line below was <td valign="top" > for no real reason-->
  <td valign="top">
  <!--
  <div class="cbox">
  <div class="cbox-title">{tr}Configure this page{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
 
  </div>
  </div>
  -->
  </td>
</tr>
</table>

{if $feature_messages eq 'y'}
    <div class="cbox">
      <div class="cbox-title">{tr}User Messages{/tr}</div>
      <div class="cbox-data">
        <div class="simplebox">
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
  <td colspan="2" class="button"><input type="submit" name="messprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>

        </div>
      </div>
    </div>
  
  {/if}

  

<br />


{if $feature_tasks eq 'y'}
    <div class="cbox">
      <div class="cbox-title">{tr}User Tasks{/tr}</div>
      <div class="cbox-data">
        <div class="simplebox">
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
  <td class="form">{tr}Use dates{/tr}</td>
  <td class="form"><input type="checkbox" name="tasks_useDates" {if $tasks_useDates eq 'y'}checked="checked"{/if}/></td>
</tr>


<tr>
  <td colspan="2" class="button"><input type="submit" name="tasksprefs" value="{tr}Change preferences{/tr}" /></td>
</tr>
</table>
</form>

        </div>
      </div>
    </div>
  
  {/if}

<br />

    <div class="cbox">
      <div class="cbox-title">{tr}My Tiki{/tr}</div>
      <div class="cbox-data">
        <div class="simplebox">
        <form action="tiki-user_preferences.php" method="post">
<table class="admin">
<tr><td class="form">{tr}My pages{/tr}</td><td class="form"><input type="checkbox" name="mytiki_pages" {if $mytiki_pages eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My blogs{/tr}</td><td class="form"><input type="checkbox" name="mytiki_blogs" {if $mytiki_blogs eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My galleries{/tr}</td><td class="form"><input type="checkbox" name="mytiki_gals" {if $mytiki_gals eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My messages{/tr}</td><td class="form"><input type="checkbox" name="mytiki_msgs" {if $mytiki_msgs eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My tasks{/tr}</td><td class="form"><input type="checkbox" name="mytiki_tasks" {if $mytiki_tasks eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="form">{tr}My items{/tr}</td><td class="form"><input type="checkbox" name="mytiki_items" {if $mytiki_items eq 'y'}checked="checked"{/if} /></td></tr>
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
