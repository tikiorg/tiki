<h1><a class="pagetitle" href="tiki-edit_languages.php">{tr}Edit or ex/import Languages{/tr}</a></h1>
[<a href="tiki-edit_languages.php" class="link">{tr}Edit and create Languages{/tr}</a>
|<a href="tiki-imexport_languages.php" class="link">{tr}Im- Export Languages{/tr}</a>]

<table >
<tr>
  <td valign="top" >
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
        <option value="{$languages_files[ix].value|escape}"
          {if $imp_files eq $languages_files[ix].value}selected="selected"{/if}>
          {$languages_files[ix].name}
        </option>
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
        <option value="{$languages[ix].value|escape}"
          {if $exp_language eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
        {/section}
        </select></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="export" value="{tr}Export{/tr}" /></td></tr>
  {if isset($expmsg)}
  <tr><td align="center" colspan="2">{$expmsg}</td></tr>
  {/if}
  </table>
  </div>
  <div class="simplebox">
  <table>
  <tr><td align="center" colspan="2">{tr}Modify String in Source Language{/tr}</td></tr>
  <tr><td  class="form">{tr}Original String{/tr}:</td><td>
	<input type="text" size="40" name="orig_source" />
 </td></tr>
   <tr><td  class="form">{tr}New String{/tr}:</td><td>
	<input type="text" size="40" name="new_source" />
 </td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="edit_source" value="{tr}Edit{/tr}" /></td></tr>
  {if isset($editsourcemsg)}
  <tr><td align="center" colspan="2">{$editsourcemsg}</td></tr>
  {/if}
  </table>
  </div>  
  </form>
  </div>
  </td>
</tr>
</table>

