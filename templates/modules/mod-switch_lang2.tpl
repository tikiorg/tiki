<div class="box">
<div class="box-title">{tr}Language{/tr}: {$language}</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
{section name=ix loop=$languages}
  <td align="center">
    <a title="{$languages[ix].name|escape}" class="linkmodule" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
      {$languages[ix].display|escape}
    </a>
  </td>
  {if not ($smarty.section.ix.rownum mod 3)}
    {if not $smarty.section.ix.last}
      </tr><tr>
    {/if}
  {/if}
{/section}
</tr></table>
</div>
</div>
