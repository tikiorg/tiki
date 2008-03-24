<h1>{if $template}<a class="pagetitle" href="tiki-edit_templates.php?mode=listing&template={$template}">{tr}Edit templates{/tr}: {$template}</a>{else}<a class="pagetitle" href="tiki-edit_templates.php">{tr}Edit templates{/tr}</a>{/if}

      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Edit+Templates" target="tikihelp" class="tikihelp" title="{tr}EditTemplates{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-edit_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}EditTemplates tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

<div class="navbar">
{if $prefs.feature_editcss eq 'y'}
<span class="button2"><a href="tiki-edit_css.php" class="linkbut">{tr}Edit CSS{/tr}</a></span>
{/if}
{if $mode eq 'editing'}
<span class="button2"><a class="linkbut" href="tiki-edit_templates.php">{tr}Template listing{/tr}</a></span>
{/if}
</div>

{if $mode eq 'listing'}
<h2>{tr}Available templates{/tr}:</h2>
<table border="1" cellpadding="0" cellspacing="0" >
<tr>
<td class="heading">{tr}Template{/tr}</td>
</tr>
{section name=user loop=$files}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd"><a class="link" href="tiki-edit_templates.php?template={$files[user]}">{$files[user]}</a></td>
</tr>
{else}
<tr>
<td class="even"><a class="link" href="tiki-edit_templates.php?template={$files[user]}">{$files[user]}</a></td>
</tr>
{/if}
{sectionelse}
<tr><td colspan="2" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
{/if}
{if $mode eq 'editing'}
<form action="tiki-edit_templates.php" method="post">
<textarea name="data" rows="20" cols="80">{$data|escape}</textarea>
<div align="center">
<input type="hidden" name="template" value="{$template|escape}" />
{if $prefs.feature_edit_templates eq 'y' and $tiki_p_edit_templates eq 'y'}
{if $prefs.style_local eq 'n'}
<input type="submit" name="save" value="{tr}Save{/tr}" />
{/if}
<input type="submit" name="saveTheme" value="{tr}Save Only in the Theme:{/tr} {$prefs.style|replace:'.css':''}" />
{if $prefs.style_local eq 'y'}
<a class="blogt" href="tiki-edit_templates.php?template={$template}&amp;delete=y}"><img src="pics/icons/cross.png" alt="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" border="0" title="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" /></a>
{/if}
{/if}
</div>
</form>
{/if}
