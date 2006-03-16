<h1><a class="pagetitle" href="tiki-admin_security.php">{tr}Security Admin{/tr}</a>
{if $feature_help eq 'y'}
<a href="{$helpurl}SecurityAdmin" target="tikihelp" class="tikihelp" title="{tr}security admin{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_security.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}security admin tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}edit tpl{/tr}' /></a>{/if}
</h1>

<table class="normal">
<tr><td colspan="4" class="heading">{tr}PHP settings{/tr}</td></tr>
<tr><td class="heading">{tr}PHP variable{/tr}</td>
<td class="heading">{tr}Setting{/tr}</td>
<td class="heading">{tr}Risk Factor{/tr}</td>
<td class="heading">{tr}Explanation{/tr}</td></tr>
{foreach from=$phpsettings key=key item=item}
<tr><td>{$key}</td>
<td>{$item.setting}</td>
<td>{$item.risk}</td>
<td>{$item.message}</td></tr>
{/foreach}
</table>
<br />
<table class="normal">
<tr><td colspan="4" class="heading">{tr}TikiWiki settings{/tr}</td></tr>
<tr><td class="heading">{tr}Tiki variable{/tr}</td>
<td class="heading">{tr}Setting{/tr}</td>
<td class="heading">{tr}Risk Factor{/tr}</td>
<td class="heading">{tr}Explanation{/tr}</td></tr>
{foreach from=$tikisettings key=key item=item}
<tr><td>{$key}</td>
<td>{$item.setting}</td>
<td>{$item.risk}</td>
<td>{$item.message}</td></tr>
{/foreach}
</table>
<br />
<a href="tiki-admin_security.php?check_files">{tr}Check all tiki files{/tr}</a><br />
{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}<br />
{tr}Note: You have to import security data via installation process (<a href="tiki-install.php">tiki-install.php</a>). Import the *secdb* update files in your database.{/tr}
<br />
{if $filecheck}
<table>
<tr><td colspan="4" class="heading">{tr}File checks{/tr}</td></tr>
<tr><td class="heading">{tr}Filename{/tr}</td>
<td class="heading">{tr}State{/tr}</td>
</tr>
{foreach from=$tikifiles key=key item=item}
<tr><td>{$key}</td>
<td>{$item}</td></tr>
{/foreach}
</table>
{/if}
