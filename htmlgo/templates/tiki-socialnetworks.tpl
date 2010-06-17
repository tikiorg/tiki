{* $Id $ *}

{title help="Social+networks"}{tr}Social networks{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

{if $tikifeedback}
  <div class="simplebox highlight">
    {section name=n loop=$tikifeedback}<div>{$tikifeedback[n].mes}</div>{/section}
  </div>
{/if}
{cycle values="odd,even" print=false}
{tabset name="mytiki_socialnetworks"}
{tab name="{tr}Twitter{/tr}"}
{if $twitter}
{button href="tiki-socialnetworks.php?remove_twitter=true" _text="{tr}Remove{/tr}"}
{tr}twitter authorisation.{/tr}
{else}
{if $show_removal}<a href="https://twitter.com/settings/connections" target="_blank">{tr}Click here{/tr}</a>{tr}to manage your authorisations at twitter{/tr}<br />{else}
{* Can't use button here, we need the reload/redirect to work *}
<span class="button"><a onclick=""  href="/tikiwiki-4.2/tiki-socialnetworks.php?request_twitter=true">Authorize</a></span>
{tr}this site with twitter.com to use twitter integration of this site.{/tr}
{/if}
{/if}

{/tab}
{/tabset}