<a class="pagetitle" href="tiki-import_phpwiki.php">{tr}Import pages from a PHPWiki Dump{/tr}</a>


  
      {if $feature_help eq 'y'}
<a href="{$helpurl}ImportingPagesAdmin" target="tikihelp" class="tikihelp" title="{tr}ImportingPagesPhpWikiPageAdmin{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}



      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-import_phpwiki.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}tiki-import_phpwiki tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt="{tr}edit tpl{/tr}" /></a>{/if}






<br /><br />

<form method="post" action="tiki-import_phpwiki.php">
<table class="normal">
<tr>
  <td class="formcolor">{tr}Path to where the dumped files are (relative to tiki basedir with trailing slash ex: dump/){/tr}:</td>
  <td class="formcolor"><input type="text" name="path" /></td>
</tr>
<tr>
  <td class="formcolor">{tr}Overwrite existing pages if the name is the same{/tr}:</td>
  <td class="formcolor">{tr}yes{/tr}<input type="radio" name="crunch" value='y' /><input checked="checked" type="radio" name="crunch" value='n' />{tr}no{/tr}</td>
</tr>
<tr>
  <td class="formcolor">{tr}Previously remove existing page versions{/tr}:</td>
  <td class="formcolor">{tr}yes{/tr}<input type="radio" name="remo" value='y' /><input checked="checked" type="radio" name="remo" value='n' />{tr}no{/tr}</td>
</tr>
<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor"><input type="submit" name="import" value="{tr}import{/tr}" /></td>
</tr>
</table>
</form>
<br /><br />
{if $result eq 'y'}
<table class="normal">
<tr>
  <td class="heading">{tr}Page{/tr}</td>
  <td class="heading">{tr}ver{/tr}</td>
  <td class="heading">{tr}excerpt{/tr}</td>
  <td class="heading">{tr}result{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$lines}
<tr>
  <td class="{cycle advance=false}">{$lines[ix].page}</td>
  <td class="{cycle advance=false}">{$lines[ix].version}</td>
  <td class="{cycle advance=false}">{$lines[ix].part}</td>
  <td class="{cycle}">{$lines[ix].msg}</td>
</tr>
{/section}
</table>
<br /><br />
{/if}
