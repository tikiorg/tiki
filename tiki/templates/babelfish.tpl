{* $Header: /cvsroot/tikiwiki/tiki/templates/babelfish.tpl,v 1.2 2003-08-14 13:13:46 zaufi Exp $ *}

{if $feature_babelfish eq 'y' and $feature_babelfish_logo eq 'y'}

<div align="center">
<table width=100%>
  {section loop=$babelfish_links name=i}
    <tr>
      {if $smarty.section.i.index == 0}
        <td>
          <a href="{$babelfish_links[i].href}" target="{$babelfish_links[i].target}"> {$babelfish_links[i].msg} </a>
        </td>
        <td rowspan="{$smarty.section.i.total}" align=right>
          {$babelfish_logo}
        </td>
      {else}
        <td>
          <a href="{$babelfish_links[i].href}" target="{$babelfish_links[i].target}"> {$babelfish_links[i].msg} </a>
        </td>
      {/if}
    </tr>
  {/section}
</table>
</div>

{elseif $feature_babelfish eq 'y' and $feature_babelfish_logo eq 'n'}

<div align="center">
<table width=100%>
  {section loop=$babelfish_links name=i}
  <tr> <td align="center">
    <a href="{$babelfish_links[i].href}" target="{$babelfish_links[i].target}"> {$babelfish_links[i].msg} </a>
  </td> </tr>
  {/section}
</table>
</div>

{elseif $feature_babelfish eq 'n' and $feature_babelfish_logo eq 'y'}

<div align="center">
  {$babelfish_logo}
</div>

{/if}
