{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/debug/tiki-debug_dmsg_tab.tpl,v 1.1 2004-01-07 04:21:56 musus Exp $ *}

<table  id="log" cellspacing="0" cellpadding="0">
  <caption> {tr}Page generation debugging log{/tr} </caption>
  {section name=i loop=$messages}
    <tr>
      <td > {$messages[i].timestamp|date_format:"%H:%M:%S"} </td>
      <td> <pre>{$messages[i].msg|escape:"html"|wordwrap:90:"\n":true|replace:"\n":"<br />"}</pre> </td>
    </tr>
  {/section}
</table>
