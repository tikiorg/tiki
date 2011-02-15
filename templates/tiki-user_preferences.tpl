{* $Id$ *}

{if $userwatch ne $user}
  {title help="User+Preferences"}{tr}User Preferences:{/tr} {$userwatch}{/title}
{else}
  {title help="User+Preferences"}{tr}User Preferences{/tr}{/title}
{/if}

{if $userwatch eq $user or $userwatch eq ""}
    {include file='tiki-mytiki_bar.tpl'}
{/if}

{if $tiki_p_admin_users eq 'y'}
	<div class="navbar">
		{assign var=thisuser value=$userinfo.login}
		{button href="tiki-assignuser.php?assign_user=$thisuser" _text="{tr}Assign Group{/tr}"}
	</div>
{/if}


{if $tikifeedback}
  <div class="simplebox highlight">
    {section name=n loop=$tikifeedback}<div>{$tikifeedback[n].mes}</div>{/section}
  </div>
{/if}
{cycle values="odd,even" print=false}
{tabset name="mytiki_user_preference"}

{if $prefs.feature_userPreferences eq 'y'}
{tab name="{tr}Personal Information{/tr}"}
<form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />


  <table class="normal">
    <tr class="{cycle}">
      <td>{tr}User:{/tr}</td>
      <td>
        <strong>{$userinfo.login|escape}</strong>
        {if $prefs.login_is_email eq 'y' and $userinfo.login neq 'admin'} 
          <em>({tr}Use the email as username{/tr})</em>
        {/if}
      </td>
    </tr>
  
    <tr class="{cycle}">
      <td>
        {tr}Real Name:{/tr}
      </td>
      <td>
        {if $prefs.auth_ldap_nameattr eq '' || $prefs.auth_method ne 'ldap'}
          <input type="text" name="realName" value="{$user_prefs.realName|escape}" style="width:20em;font-size:1.1em;" />{else}{$user_prefs.realName|escape}
        {/if}
      </td>
    </tr>

    <tr class="{cycle}">
      <td>{tr}Avatar:{/tr}</td>
      <td>
        {$avatar} 
        <a href="tiki-pick_avatar.php{if $userwatch ne $user}?view_user={$userwatch}{/if}" class="link">{tr}Pick user Avatar{/tr}</a>
      </td>
    </tr>
  
	{if $prefs.feature_community_gender eq 'y'}
      <tr class="{cycle}"><td>{tr}Gender:{/tr}</td>
        <td>
          <input type="radio" name="gender" value="Male" {if $user_prefs.gender eq 'Male'}checked="checked"{/if}/> {tr}Male{/tr}
          <input type="radio" name="gender" value="Female" {if $user_prefs.gender eq 'Female'}checked="checked"{/if}/> {tr}Female{/tr}
          <input type="radio" name="gender" value="Hidden" {if $user_prefs.gender ne 'Male' and $user_prefs.gender ne 'Female'}checked="checked"{/if}/> {tr}Hidden{/tr}
        </td>
      </tr>
	{/if}

    <tr class="{cycle}">
      <td>{tr}Country:{/tr}</td>
      <td>
        {if isset($user_prefs.country) && $user_prefs.country != "None" && $user_prefs.country != "Other"}
          {$userinfo.login|countryflag}
        {/if}
        <select name="country">
          <option value="Other" {if $user_prefs.country eq "Other"}selected="selected"{/if}>{tr}Other{/tr}</option>
          {sortlinks}
            {section name=ix loop=$flags}
              {if $flags[ix] ne "Other"}
                <option value="{$flags[ix]|escape}" {if $user_prefs.country eq $flags[ix]}selected="selected"{/if}>{tr}{$flags[ix]|stringfix}{/tr}</option>
              {/if}
            {/section}
          {/sortlinks}
        </select>
      </td>
    </tr>
  
    {if $prefs.feature_gmap eq 'y'}
    <tr class="{cycle}">
      <td>{tr}Location:{/tr}</td>
      <td>
        {if $prefs.ajax_xajax eq 'y' and !empty($user_prefs.lat)}
          {wikiplugin _name="googlemap" type="user" setdefaultxyz="" locateitemtype="user" locateitemid="$userwatch" width="200" height="100" controls="n"}{/wikiplugin}
        {/if}
        <p>
          {button href="tiki-gmap_locator.php" for="user" view_user=$userinfo.login|escape for="user" _text="{tr}Use Google Map locator{/tr}" _auto_args='view_user,for'}
        </p>
      </td>
    </tr>
    {/if}

    <tr class="{cycle}">
      <td>{tr}URL:{/tr}</td>
      <td>
        <input type="text" size="40" name="homePage" value="{$user_prefs.homePage|escape}" />
      </td>
    </tr>
  
    {if $prefs.feature_wiki eq 'y' and $prefs.feature_wiki_userpage eq 'y'}
      <tr class="{cycle}">
        <td>{tr}Your personal Wiki Page:{/tr}</td>
        <td>
          {if $userPageExists eq 'y'}
            <a class="link" href="tiki-index.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}" title="{tr}View{/tr}">{$prefs.feature_wiki_userpage_prefix}{$userinfo.login|escape}</a> 
	    (<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">{tr}Edit{/tr}</a>)
          {else}
            {$prefs.feature_wiki_userpage_prefix}{$userinfo.login|escape} (<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix}{$userinfo.login}">{tr}Create{/tr}</a>)
          {/if}
        </td>
      </tr>
    {/if}
  
	{if $prefs.userTracker eq 'y' && $usertrackerId}
		{if $tiki_p_admin eq 'y' and !empty($userwatch) and $userwatch neq $user}
			<tr class="{cycle}">
				<td>{tr}User's personal tracker information:{/tr}</td>
				<td>
					<a class="link" href="tiki-view_tracker_item.php?trackerId={$usertrackerId}&user={$userwatch|escape:url}&view=+user">{tr}View extra information{/tr}</a>
				</td>
			</tr>
		{else}
			<tr class="{cycle}">
				<td>{tr}Your personal tracker information:{/tr}</td>
				<td>
					<a class="link" href="tiki-view_tracker_item.php?view=+user">{tr}View extra information{/tr}</a>
				</td>
			</tr>
		{/if}
	{/if}

    {* Custom fields *}
    {section name=ir loop=$customfields}
      {if $customfields[ir].show}
        <tr class="{cycle}">
          <td>{$customfields[ir].label}:</td>
          <td>
            <input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" />
          </td>
        </tr>
      {/if}
    {/section}
    <tr class="{cycle}">
      <td>{tr}User information:{/tr}</td>
      <td>
        <select name="user_information">
          <option value='private' {if $user_prefs.user_information eq 'private'}selected="selected"{/if}>{tr}Private{/tr}</option>
          <option value='public' {if $user_prefs.user_information eq 'public'}selected="selected"{/if}>{tr}Public{/tr}</option>
        </select>
      </td>
    </tr>
    <tr class="{cycle}">
      <td>{tr}Last login:{/tr}</td>
      <td><span class="description">{$userinfo.lastLogin|tiki_long_datetime}</span></td>
    </tr>
  
    <td colspan="2" class="input_submit_container"><input type="submit" name="new_prefs" value="{tr}Save changes{/tr}" /></td>
  
  </table>
</form>
{/tab}
{tab name="{tr}Preferences{/tr}"}
<form action="tiki-user_preferences.php" method="post">
  <table class="normal">
    <tr>
      <th colspan="2">{tr}General settings{/tr}</th>
    </tr>
    <tr class="{cycle}">
      <td>{tr}Is email public? (uses scrambling to prevent spam){/tr}</td>
      <td>
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
    
    <tr class="{cycle}">
      <td>{tr}Does your mail reader need a special charset{/tr}</td>
      <td>
        <select name="mailCharset">
          {section name=ix loop=$mailCharsets}
            <option value="{$mailCharsets[ix]|escape}" {if $user_prefs.mailCharset eq $mailCharsets[ix]}selected="selected"{/if}>{$mailCharsets[ix]}</option>
          {/section}
        </select>
      </td>
    </tr>
    {if $prefs.change_theme eq 'y' && empty($group_style)}
      <tr class="{cycle}">
        <td>{tr}Theme:{/tr}</td>
        <td>
          <select name="mystyle">
            <option value="" style="font-style:italic;border-bottom:1px dashed #666;">{tr}Site default{/tr}</option>
              {section name=ix loop=$styles}
                {if count($prefs.available_styles) == 0 || empty($prefs.available_styles[0]) || in_array($styles[ix], $prefs.available_styles)}
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
      <tr class="{cycle}">
        <td>{tr}Preferred language:{/tr}</td>
        <td>
          <select name="language">
            {section name=ix loop=$languages}
              {if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
                <option value="{$languages[ix].value|escape}"
                  {if $user_prefs.language eq $languages[ix].value}selected="selected"{/if}>
                  {$languages[ix].name}
                </option>
              {/if}
            {/section}
            <option value='' {if !$user_prefs.language}selected="selected"{/if}>{tr}Site default{/tr}</option>
          </select>
		  {if $prefs.feature_multilingual eq 'y'}
		  {if $user_prefs.read_language}
		  <div id="read-lang-div">
		  {else}
		  <a href="javascript:void(0)" onclick="document.getElementById('read-lang-div').style.display='block';this.style.display='none';">
		  <br/>
		  {tr}Can you read more languages?{/tr}</a>
		  <br/>&nbsp;
		  <div id="read-lang-div" style="display: none">
		  {/if}
			{tr}Other languages you can read (select on the left to add to the list on the right):{/tr}
			<br/>
			<select name="_blank" onchange="document.getElementById('read-language-input').value+=' '+this.options[this.selectedIndex].value+' '">
			  <option value="">{tr}Select language...{/tr}</option>
              {section name=ix loop=$languages}
                  <option value="{$languages[ix].value|escape}">
                    {$languages[ix].name}
                  </option>
              {/section}
            </select>
			&nbsp;=&gt;&nbsp;
		  	<input id="read-language-input" type="text" name="read_language" value="{$user_prefs.read_language}"/>
            <br/>&nbsp;
		  </div>
		  {/if}
        </td>
      </tr>
    {/if}
  
    <tr class="{cycle}">
      <td>{tr}Number of visited pages to remember:{/tr}</td>
      <td>
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
    <tr class="{cycle}">
      <td>{tr}Displayed time zone:{/tr}</td>
      <td>
        <select name="display_timezone" id="display_timezone">
	  <option value="" style="font-style:italic;">{tr}Detect user timezone if browser allows, otherwise site default{/tr}</option>
	  <option value="Site" style="font-style:italic;border-bottom:1px dashed #666;"{if isset($user_prefs.display_timezone) and $user_prefs.display_timezone eq 'Site'} selected="selected"{/if}>{tr}Site default{/tr}</option>
          {foreach key=tz item=tzinfo from=$timezones}
            {math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}
            {math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
            <option value="{$tz|escape}"{if isset($user_prefs.display_timezone) and $user_prefs.display_timezone eq $tz} selected="selected"{/if}>{$tz|escape} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
          {/foreach}
        </select>
      </td>
    </tr>

    {if $prefs.feature_community_mouseover eq 'y'}
      <tr class="{cycle}">
        <td>{tr}Display info tooltip on mouseover for every user who allows his/her information to be public{/tr}</td>
        <td>
          <input type="checkbox" name="show_mouseover_user_info" {if $show_mouseover_user_info eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_wiki eq 'y'}
      <tr class="{cycle}">
        <td>{tr}Use double-click to edit pages:{/tr}</td>
        <td>
          <input type="checkbox" name="user_dbl" {if $user_prefs.user_dbl eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}
  
    {if $prefs.feature_maps eq 'y' or $prefs.feature_gmap eq 'y'}
      <tr class="{cycle}">
        <td>{tr}Longitude:{/tr}</td>
        <td>
          <input type="text" name="lon" value="{$user_prefs.lon|escape}" /> <em>Use WGS84/decimal degrees</em>
        </td>
      </tr>
      <tr class="{cycle}">
        <td>{tr}Latitude:{/tr}</td>
        <td>
          <input type="text" name="lat" value="{$user_prefs.lat|escape}" /> <em>for longitude and latitude</em>
        </td>
      </tr>
      <tr class="{cycle}">
        <td>{tr}Map zoom:{/tr}</td>
        <td>
          <input type="text" name="zoom" value="{$user_prefs.zoom|escape}" />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
      <tr>
        <th colspan="2">{tr}User Messages{/tr}</th>
      </tr>
    
      <tr class="{cycle}">
        <td>{tr}Messages per page{/tr}</td>
        <td>
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
        <tr class="{cycle}">
          <td>{tr}Allow messages from other users{/tr}</td>
          <td><input type="checkbox" name="allowMsgs" {if $user_prefs.allowMsgs eq 'y'}checked="checked"{/if}/></td>
        </tr>
      {/if}

      <tr class="{cycle}">
        <td>{tr}Notify sender when reading his mail{/tr}</td>
        <td>
          <input type="checkbox" name="mess_sendReadStatus" {if $user_prefs.mess_sendReadStatus eq 'y'}checked="checked"{/if}/>
        </td>
      </tr>

      <tr class="{cycle}">
        <td>{tr}Send me an email for messages with priority equal or greater than:{/tr}</td>
        <td>
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

      <tr class="{cycle}">
        <td>{tr}Auto-archive read messages after x days{/tr}</td>
        <td>
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
        <th colspan="2">{tr}User Tasks{/tr}</th>
      </tr>
    
      <tr class="{cycle}">
        <td>{tr}Tasks per page{/tr}</td>
        <td>
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
      <th colspan="2">{tr}My Tiki{/tr}</th>
    </tr>

    {if $prefs.feature_wiki eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My pages{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_pages" {if $user_prefs.mytiki_pages eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_blogs eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My blogs{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_blogs" {if $user_prefs.mytiki_blogs eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_galleries eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My galleries{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_gals" {if $user_prefs.mytiki_gals eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_messages eq 'y'and $tiki_p_messages eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My messages{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_msgs" {if $user_prefs.mytiki_msgs eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My tasks{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_tasks" {if $user_prefs.mytiki_tasks eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_forums eq 'y' and $tiki_p_forum_read eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My forum topics{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_forum_topics" {if $user_prefs.mytiki_forum_topics eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
      <tr class="{cycle}">
        <td>{tr}My forum replies{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_forum_replies" {if $user_prefs.mytiki_forum_replies eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}
    
    {if $prefs.feature_trackers eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My user items{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_items" {if $user_prefs.mytiki_items eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_articles eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My Articles{/tr}</td>
        <td>
          <input type="checkbox" name="mytiki_articles" {if $user_prefs.mytiki_articles eq 'y'}checked="checked"{/if} />
        </td>
      </tr>
    {/if}

    {if $prefs.feature_userlevels eq 'y'}
      <tr class="{cycle}">
        <td>{tr}My level{/tr}</td>
        <td>
          <select name="mylevel">
            {foreach key=levn item=lev from=$prefs.userlevels}
	      <option value="{$levn}"{if $user_prefs.mylevel eq $levn} selected="selected"{/if}>{$lev}</option>
	    {/foreach}
          </select>
        </td>
      </tr>
    {/if}

    <tr>
      <td colspan="2" class="input_submit_container"><input type="submit" name="new_prefs" value="{tr}Save changes{/tr}" /></td>
    </tr>
  </table>
</form>
{/tab}
{/if}

{if $prefs.change_password neq 'n' or ! ($prefs.login_is_email eq 'y' and $userinfo.login neq 'admin')}
	{tab name="{tr}Account Information{/tr}"}
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="normal">
    {if $prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')}
      {if $prefs.change_password neq 'n' and ($prefs.login_is_email ne 'y' or $userinfo.login eq 'admin') }
        <tr class="{cycle}">
          <td colspan="2">{tr}Leave "New password" and "Confirm new password" fields blank to keep current password{/tr}</td>
        </tr>
      {/if}
    {/if}
  
      {if $prefs.login_is_email eq 'y' and $userinfo.login neq 'admin'}
        <input type="hidden" name="email" value="{$userinfo.email|escape}" />
      {else}
        <tr class="{cycle}">
          <td>{tr}Email address:{/tr}</td>
          <td><input type="text" name="email" value="{$userinfo.email|escape}" /></td>
        </tr>
      {/if}

      {if $prefs.auth_method neq 'cas' || ($prefs.cas_skip_admin eq 'y' && $user eq 'admin')}
        {if $prefs.change_password neq 'n'}
          <tr class="{cycle}">
            <td>{tr}New password:{/tr}</td>
            <td><input type="password" name="pass1" /></td>
          </tr>
  
          <tr class="{cycle}">
            <td>{tr}Confirm new password:{/tr}</td>
            <td><input type="password" name="pass2" /></td>
          </tr>
        {/if}
      
        {if $tiki_p_admin ne 'y' or $userwatch eq $user}
          <tr class="{cycle}">
            <td>{tr}Current password (required):{/tr}</td>
            <td><input type="password" name="pass" /></td>
          </tr>
        {/if}
      {/if}
    
      <tr>
        <td colspan="2" class="input_submit_container"><input type="submit" name="chgadmin" value="{tr}Save changes{/tr}" /></td>
      </tr>
    </table>
  </form>
	{/tab}
{/if}

{if $tiki_p_delete_account eq 'y' and $userinfo.login neq 'admin'}
{tab name="{tr}Account Deletion{/tr}"}
<form action="tiki-user_preferences.php" method="post" onsubmit='return confirm("{tr 0=$userwatch|escape}Are you really sure you want to delete the account %0?{/tr}");'>
{if !empty($userwatch)}<input type="hidden" name="view_user" value="{$userwatch|escape}" />{/if}
 <table class="normal">
  <tr class="{cycle}">
   <td></td>
   <td><input type='checkbox' name='deleteaccountconfirm' value='1' /> {tr}Check this box if you really want to delete the account{/tr}</td>
  </tr>
    <tr>
      <td colspan="2"  class="input_submit_container"><input type="submit" name="deleteaccount" value="{if !empty($userwatch)}{tr}Delete the account:{/tr} {$userwatch|escape}{else}{tr}Delete my account{/tr}{/if}" /></td>
    </tr>
 </table>
</form>
{/tab}
{/if}

{/tabset}
