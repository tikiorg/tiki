{* $Id $ *}

{title help="Social+networks"}{tr}Social networks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

{if $tikifeedback}
  <div class="simplebox highlight">
    {section name=n loop=$tikifeedback}<div>{$tikifeedback[n].mes}</div>{/section}
  </div>
{/if}
<fieldset>
 <legend>Twitter</legend>
 {if $twitterRegistered==0}{remarksbox type="note" title="{tr}Note{/tr}"}
  <p>{tr}To use Twitter integration, the site admin must register this site as an application at <a href="http://twitter.com/oauth_clients/" target="_blank">http://twitter.com/oauth_clients/</a> and allow write access for the application.{/tr}</p>
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
</fieldset>
<fieldset>
 <legend>Facebook</legend>
 {if $facebookRegistered==0}{remarksbox type="note" title="{tr}Note{/tr}"}
  <p>{tr}To use Facebook integration, the site admin must register this site as an application at <a href="http://developers.facebook.com/setup/" target="_blank">http://developers.facebook.com/setup/</a> first.{/tr}</p>
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
</fieldset>
