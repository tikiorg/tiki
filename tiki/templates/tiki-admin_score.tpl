<span class="pagetitle">{tr}Score{/tr}</span>

{if $feature_help eq 'y'}
<!-- the help link info --->
<a href="http://tikiwiki.org/tiki-index.php?page=ScoreSystem" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Banners{/tr}">
<img border='0' src='img/icons/help.gif' alt="{tr}help{/tr}" />
</a>{/if}

<!-- begin -->

<br /><br />
<div align="center">
<table class="normal">
<form method="post">
<tr>
  <td class="heading">{tr}Action{/tr}</td>
  <td class="heading">{tr}Points{/tr}</td>
  <td class="heading">{tr}Expiration{/tr}</td>
</tr>

{section name=e loop=$events}
{if $events[e].category != $categ}
  {assign var=categ value=$events[e].category}
<tr>
  <td class="even" colspan="3"><b>{tr}{$events[e].category}{/tr}</b></td>
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
    <input type="text" size="3" name="events[{$events[e].event}][score]" value="{$events[e].score}">
  </td>
  <td class="{$class}">
    <input type="text" size="4" name="events[{$events[e].event}][expiration]" value="{$events[e].expiration}">
  </td>
</tr>
{/section}
{if $class == 'odd'}
  {assign var=class value='even'}
{else}
  {assign var=class value='even'}
{/if}
<tr>
  <td class="{$class}"></td>
  <td class="{$class}" colspan="2">
    <input type="submit" value="{tr}Save{/tr}">
  </td>
</tr>
</form>
</table>
</div>

