{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/debug/tiki-debug_watch_tab.tpl,v 1.3 2004-01-16 19:35:59 musus Exp $ *}

<table id="watchlist">
  <caption>{tr}Watchlist{/tr}</caption>
  <tr>
    <td class="heading">Variable</td>
    <td class="heading">Value</td>
  </tr>
  {cycle values="even,odd" print=false}
  {section name=i loop=$watchlist}
    <tr>
      <td class="{cycle advance=false}"{if $smarty.section.i.index == 0} id="firstrow"{/if}>
        <code>{$watchlist[i].var}</code>
      </td>
      <td class="{cycle}"{if $smarty.section.i.index == 0} id="firstrow"{/if}>
        <pre>{$watchlist[i].value|escape:"html"|wordwrap:60:"\n":true|replace:"\n":"<br />"}</pre>
      </td>
    </tr>
  {/section}
</table>
