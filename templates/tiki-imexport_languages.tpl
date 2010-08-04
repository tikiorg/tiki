{title}{tr}Edit or export Languages{/tr}{/title}

<div class="navbar">
	{button href="tiki-edit_languages.php" _text="{tr}Edit and create Languages{/tr}"}
</div>

<table>
<tr>
  <td valign="top" >
  <div class="cbox">
  <div class="cbox-title">{tr}Export languages{/tr}</div>
  <div class="cbox-data">
  <form action="tiki-imexport_languages.php" method="post">
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
  </form>
  </div>
  </td>
</tr>
</table>

