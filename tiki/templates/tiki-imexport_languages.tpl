<a class="pagetitle" href="tiki-edit_languages.php">{tr}Edit or ex/import Languages{/tr}</a><br/><br/>
[<a href="tiki-edit_languages.php" class="link">{tr}Edit and create Languages{/tr}</a>
|<a href="tiki-inexport_languages.php" class="link">{tr}Im- Export Languages{/tr}</a>]

<table width="100%">
<tr>
  <td valign="top" width="50%">
  <div class="cbox">
  <div class="cbox-title">{tr}Im- Export languages{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <form action="tiki-imexport_languages.php" method="post">
  <table>
  <tr><td align="center" colspan="2">Import</td></tr>
  <tr><td  class="form">{tr}Select the language to Import{/tr}:</td><td>
        <select name="imp_language">
        {section name=ix loop=$languages_files}
        <option value="{$languages_files[ix]|escape}" {if $imp_language eq $languages_files[ix]}selected="selected"{/if}>{$languages_files[ix]}</option>
        {/section}
        </select></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="import" value="{tr}import{/tr}" /></td></tr>
  {if isset($impmsg)}
  <tr><td align="center" colspan="2">{$impmsg}</td></tr>
  {/if}
  </table>
  </div>
  <div class="simplebox">
  <table>
  <tr><td align="center" colspan="2">{tr}Export{/tr}</td></tr>
  <tr><td  class="form">{tr}Select the language to Export{/tr}:</td><td>
        <select name="exp_language">
        {section name=ix loop=$languages}
        <option value="{$languages[ix]|escape}" {if $exp_language eq $languages[ix]}selected="selected"{/if}>{$languages[ix]}</option>
        {/section}
        </select></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="export" value="{tr}export{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  </td>
</tr>
</table>

