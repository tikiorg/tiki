{title help="Social+networks"}{tr}Social networks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

{if $tikifeedback}
  <div class="simplebox highlight">
    {section name=n loop=$tikifeedback}<div>{$tikifeedback[n].mes}</div>{/section}
  </div>
{/if}
{tabset name="mytiki_user_preference"}
{tab name="{tr}Accounts{/tr}"}
{cycle values="odd,even" print=false}
<form action="tiki-socialnetworks.php" method="post">
<table class="formcolor" style="width:100%;">
<tr>
 <th colspan="2"><img src="img/icons/twitter_t_logo_32.png" alt="Twitter" width="16" height="16" /> Twitter</th>
</tr>
<tr class="{cycle}">
 <td colspan="2">
 {if $twitterRegistered==0}{remarksbox type="note" title="{tr}Note{/tr}"}
  {tr}To use Twitter integration, the site admin must register this site as an application at <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}
 {/remarksbox}{else}
{if $twitter}
{button href="tiki-socialnetworks.php?remove_twitter=true" _text="{tr}Remove{/tr}"}
{tr}twitter authorisation.{/tr}
{else}
{if $show_removal}<a href="https://twitter.com/settings/connections" target="_blank">{tr}Click here{/tr}</a>{tr}to manage your authorisations at twitter{/tr}<br />{else}
{* Can't use button here, we need the reload/redirect to work *}
<span class="button"><a onclick=""  href="tiki-socialnetworks.php?request_twitter=true">Authorize</a></span>
{tr}this site with twitter.com to use twitter integration of this site.{/tr}
{/if}
{/if}
{/if}
 </td>
</tr>
<tr>
 <th colspan="2"><img src="img/icons/facebook-logo_32.png" alt="Facebook" width="16" height="16" /> Facebook</th>
</tr>
<tr class="{cycle}">
 <td colspan="2">
 {if $facebookRegistered==0}{remarksbox type="note" title="{tr}Note{/tr}"}
  {tr}To use Facebook integration, the site admin must register this site as an application at <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a> first.{/tr}
 {/remarksbox}{else}
{if $facebook}
{button href="tiki-socialnetworks.php?remove_facebook=true" _text="{tr}Remove{/tr}"}
{tr}facebook authorisation.{/tr}
{else}
{if $show_removal}<a href="http://facebook.com/editapps.php" target="_blank">{tr}Click here{/tr}</a>{tr}to manage your authorisations at facebook{/tr}<br />{else}
{* Can't use button here, we need the reload/redirect to work *}
<span class="button"><a onclick=""  href="tiki-socialnetworks.php?request_facebook=true">Authorize</a></span>
{tr}this site with facebook.com to use twitter integration of this site.{/tr}
{/if}
{/if}
{/if}
</td>
</tr>
<tr><th colspan="2">bit.ly</th>
</tr>
<tr class="{cycle}">
{if $prefs.socialnetworks_bitly_sitewide=='y'}
<td colspan="2">
{remarksbox type="note" title="{tr}Note{/tr}"}
  <p>{tr}The site admin has set up a global account which will be used for this site{/tr}.</p>
 {/remarksbox}
 </td>
{else}
<td>{tr}bit.ly Login{/tr}</td>
<td><input type="text" name="bitly_login" value="{$bitly_login}" style="width:95%;" /></td>
</tr>
<tr class="{cycle}">
<td>{tr}bit.ly Key{/tr}</td>
<td><input type="text" name="bitly_key" value="{$bitly_key}" style="width:95%;" /></td>
{/if}
</tr>
<tr>
<td colspan="2" class="input_submit_container"><input type="submit" name="accounts" value="{tr}Save changes{/tr}" /></td>
</tr>
</table>
</form>
{/tab}
{/tabset}
