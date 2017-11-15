<ul>
  {foreach from=$included_by item=include}
  <li>
    {$include.type|capitalize}:
    <a href="{$include.href}" target="_blank">{$include.title}</a>
    {if $include.start || $include.end} - {/if}
    {if $include.start}
    {tr}from{/tr} "{$include.start}"
    {/if}
    {if $include.end}
    {tr}to{/tr} "{$include.end}"
    {/if}

  </li>
  {/foreach}
</ul>
