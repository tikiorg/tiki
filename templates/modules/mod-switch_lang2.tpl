<div class="box">
<div class="box-title">{tr}Language{/tr}: {$language}</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
{section name=ix loop=$languages}
  <td align="center">
    <a alt="{$languages[ix].name|escape}" class="linkmodule" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
      <img border="0" alt="{$languages[ix].name|escape}" src="{$languages[ix].flag}" />
    </a></td>
  {if not ($smarty.section.ix.rownum mod 6)}
    {if not $smarty.section.ix.last}
      </tr><tr>
    {/if}
  {/if}
{/section}
</tr></table>
</div>
</div>
