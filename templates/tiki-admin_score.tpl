<h1><span class="pagetitle">{tr}Score{/tr}</span>

{if $prefs.feature_help eq 'y'}
<!-- the help link info --->
<a href="{$prefs.helpurl}Score" target="tikihelp" class="tikihelp" title="{tr}Score System{/tr}: {tr}Score System{/tr}">
{icon _id='help'}</a>{/if}</h1>

<div align="center">
<form method="post">
<table class="normal">
<tr>
  <td class="heading">{tr}Action{/tr}</td>
  <td class="heading">{tr}Points{/tr}</td>
  <td class="heading">{tr}Expiration{/tr}</td>
</tr>

{section name=e loop=$events}
{if $events[e].category != $categ}
  {assign var=categ value=$events[e].category}
<tr>
  <td colspan="3"><b>{tr}{$events[e].category}{/tr}</b></td>
</tr>
{/if}

{if $smarty.section.e.index % 2}
  {assign var=class value='odd'}
{else}
  {assign var=class value='even'}
{/if}
<tr>
  <td class="{$class}">{tr}{$events[e].description}{/tr}</td>
  <td class="{$class}">
    <input type="text" size="3" name="events[{$events[e].event}][score]" value="{$events[e].score}" />
  </td>
  <td class="{$class}">
    <input type="text" size="4" name="events[{$events[e].event}][expiration]" value="{$events[e].expiration}" />
  </td>
</tr>
{/section}
<tr>
  <td class="button" colspan="3">
    <input type="submit" value="{tr}Save{/tr}" />
  </td>
</tr>
</table>

</form>
</div>

