{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-simple_plugin.tpl,v 1.4 2003-08-02 22:17:07 zaufi Exp $ *}

<div class="catlists">
  <div class="cbox-title">{$title}</div>

  <div class="cbox-data">
  {foreach key=t item=i from=$listcat}
    <b>{tr}{$t}{/tr}:</b>
    {section name=o loop=$i}
        <a href="{$i[o].href}" class="link" title="{tr}Created{/tr} {$i[o].created|tiki_long_date}">
        {$i[o].name}
      </a> .
    {/section}
    <br />
  {/foreach}
  </div>
</div>
{dbg}
  {dbg2}
    {$title|dbg}
  {/dbg2}
{/dbg}