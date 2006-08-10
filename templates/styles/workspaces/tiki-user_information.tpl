<h1><a class="pagetitle" href="tiki-user_information.php?view_user={$userwatch}">{tr}User Information{/tr}</a></h1>
<table >
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <table>
  <tr><td class="form">{tr}User{/tr}:</td><td>{$userinfo.login}{if $tiki_p_admin eq 'y'} <a class="link" href="tiki-user_preferences.php?view_user={$userinfo.login}"><IMG SRC="img/icons/config.gif" title="{tr}Change user preferences{/tr}" border="0" /> </a>  {/if}</td></tr>
{if $feature_score eq 'y'}
  <tr><td class="form">{tr}Score{/tr}:</td><td>{$userinfo.score|star}{$userinfo.score}</td></tr>
{/if}
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
{if $email_isPublic neq 'n'}  
  <tr><td class="form">{tr}Email{/tr}:</td><td>{$userinfo.email}</td></tr>
{/if}  
  <tr><td class="form">{tr}Country{/tr}:</td><td><img alt="flag" src="img/flags/{$country}.gif" /> {tr}{$country}{/tr}</td></tr>
  <tr><td class="form">{tr}Theme{/tr}:</td><td>{$user_style}</td></tr>
  <tr><td  class="form">{tr}Language{/tr}:</td><td>{$user_language}</td></tr>
  <tr><td class="form">{tr}Real Name{/tr}:</td><td>{$realName}</td></tr>

  {* Custom fields *}
  {section name=ir loop=$customfields}
    <tr><td class="form">{tr}{$customfields[ir].prefName}{/tr}:</td><td>{$customfields[ir].value}</td></tr>
  {/section}

  <tr><td class="form">{tr}Avatar{/tr}:</td><td>{$avatar}</td></tr>
  <tr><td class="form">{tr}Homepage{/tr}:</td><td>{if $homePage ne ""}<a href="{$homePage}" class="link" title="{tr}Users HomePage{/tr}">{$homePage}</a>{/if}</td></tr>
{if $feature_wiki eq 'y' && $feature_wiki_userpage eq 'y'}
  <tr><td class="form">{tr}Personal Wiki Page{/tr}:</td><td>
{if $userPage_exists}
<a class="link" href="tiki-index.php?page={$feature_wiki_userpage_prefix|escape:'url'}{$userinfo.login|escape:'url'}">{$feature_wiki_userpage_prefix}{$userinfo.login}</a>
{elseif $user == $userinfo.login}
{$feature_wiki_userpage_prefix}{$userinfo.login}<a class="link" href="tiki-editpage.php?page={$feature_wiki_userpage_prefix|escape:'url'}{$userinfo.login|escape:'url'}" title="{tr}Create page{/tr}">?</a>
{else}&nbsp;{/if}
</td></tr>
{/if}
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>{$display_timezone}</td></tr>
{if $feature_friends eq 'y' && $user ne $userwatch && $user}
  {if $friend}
  <tr><td class="form">&nbsp;</td><td class="form">
    <img src="img/icons/ico_friend.gif" width="7" height="10"> {tr}This user is your friend{/tr}
  </td></tr>  
  {else}
  <tr><td class="form">&nbsp;</td><td class="form">
<img src="img/icons/ico_not_friend.png"> <a class="link" href="tiki-friends.php?request_friendship={$userinfo.login}">{tr}Request friendship from this user{/tr}</a>  
  </td></tr>  
  {/if}
{/if}
  <tr>
  <td class="form">&nbsp;</td>
  <td class="form">
    <img src="images/workspaces/edu_workspace.png"> <a class="link" href="tiki-workspaces_desktop.php?workspaceCode=PWS{$userinfo.login}">{tr}Personal portfolio{/tr}</a>
  </td>
  </tr>
  </table>
  </form>
  </div>
  </div>
  </div>
</td></tr>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y' and $allowMsgs eq 'y'}
{if $sent}
{$message}
{/if}
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}Send me a message{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <form method="post" action="tiki-user_information.php" name="f">
  <input type="hidden" name="to" value="{$userwatch|escape}" />
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="normalnoborder">
  <tr>
    <td class="form">{tr}Priority{/tr}:</td><td class="form">
    <select name="priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
    </select>
    <input type="submit" name="send" value="{tr}send{/tr}" />
    </td>
  </tr>
  <tr>
    <td class="form">{tr}Subject{/tr}:</td><td class="form"><input type="text" name="subject" value="" maxlength="255" style="width:100%;"/></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;" class="form"><textarea rows="20" cols="80" name="body"></textarea></td>
  </tr>
</table>

  
  </form>
  </div>
  </div>
  </div>

  </td>
</tr>  
{/if}
</table>
<br /><br />  
