{* $Header: /cvsroot/tikiwiki/tiki/templates/debug/tiki-debug_dmsg_tab.tpl,v 1.2 2003-08-01 10:31:14 redflo Exp $ *}

<table width="100%" id="log" cellspacing="0" cellpadding="0">
  <caption> {tr}Page generation debugging log{/tr} </caption>
  {section name=i loop=$messages}
    <tr>
      <td width="10%"> {$messages[i].timestamp|date_format:"%H:%M:%S"} </td>
      <td> <pre>{$messages[i].msg|escape:"html"|wordwrap:90:"\n":true|replace:"\n":"<br />"}</pre> </td>
    </tr>
  {/section}
</table>
