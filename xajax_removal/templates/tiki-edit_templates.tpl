{if $template}
  {title help="Edit+Templates" url="tiki-edit_templates.php?mode=listing&template=$template"}{tr}Edit template:{/tr} {$template}{/title}
{else}
  {title help="Edit+Templates"}{tr}Edit templates{/tr}{/title}
{/if}

<div class="navbar">
  {if $prefs.feature_editcss eq 'y'}
		{button href="tiki-edit_css.php" _text="{tr}Edit CSS{/tr}"}
  {/if}
  {if $mode eq 'editing'}
		{button href="tiki-edit_templates.php" _text="{tr}Template listing{/tr}"}
  {/if}
</div>

{if $mode eq 'listing'}
<h2>{tr}Available templates{/tr}:</h2>
<table border="1" cellpadding="0" cellspacing="0" >
<tr>
<th>{tr}Template{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$files}
  <tr class="{cycle}">
    <td><a class="link" href="tiki-edit_templates.php?template={$files[user]}">{$files[user]}</a></td>
  </tr>
{sectionelse}
  <tr class="{cycle}"><td colspan="2">{tr}No records found{/tr}</td></tr>
{/section}
</table>
{/if}
{if $mode eq 'editing'}
    {remarksbox type="warning" title="{tr}Important!{/tr}" highlight="y"}
	<p>{tr}If you go to edit this (or any other TPL file) file via the Tiki built-in TPL editor below, all the javascript can be sanitized or completely stripped out by Tiki security filtering.{/tr}
	{tr}This would cause you problems (e.g. menus can stop collapsing/expanding).{/tr}</p>

	<p>{tr}You should only modify default header.tpl and other important files via text code editor, through console, or SSH, or FTP edit commands. And only if you know what you are doing ! ;-){/tr}</p>

	<p>{tr}Maybe You just want to modify the top of your Tiki site?{/tr}<br />
	{tr}Please consider using the Look & Feel preferences custom code areas or modify tiki-top_bar.tpl which you can do safely via the web-based interface.{/tr}</p>
    {/remarksbox}

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
<a class="blogt" href="tiki-edit_templates.php?template={$template}&amp;delete=y}"><img src="pics/icons/cross.png" alt="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" title="{tr}Delete the copy in the theme:{/tr} {$prefs.style|replace:'.css':''}" /></a>
{/if}
{/if}
</div>
</form>
{/if}
