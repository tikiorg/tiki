<h1><a class="pagetitle" href="tiki-admin_security.php">{tr}Security Admin{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Security+Admin" target="tikihelp" class="tikihelp" title="{tr}security admin{/tr}">
{icon _id='help'}</a>{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_security.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}security admin tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>{/if}
</h1>

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}To <a class="rbox-link" target="tikihelp" href="http://security.tikiwiki.org/tiki-contact.php">report any security issues</a>.{/tr}
</div>
</div>
<br />
<h2>{tr}PHP settings{/tr}</h2>
<table class="normal">
<tr><th class="heading">{tr}PHP variable{/tr}</th>
<th class="heading">{tr}Setting{/tr}</th>
<th class="heading">{tr}Risk Factor{/tr}</th>
<th class="heading">{tr}Explanation{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach from=$phpsettings key=key item=item}
<tr><td class="{cycle advance=false}">{$key}</td>
<td class="{cycle advance=false}">{$item.setting}</td>
<td class="{cycle advance=false}">
{if $item.risk eq 'safe'}{icon _id=accept.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'risky'}{icon _id=exclamation.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unsafe'}{icon _id=exclamation.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unknown'}{icon _id=error.png alt="$item.risk" style="vertical-align:middle"}
{/if}
{$item.risk}</td>
<td class="{cycle advance=true}">{$item.message}</td></tr>
{/foreach}
{if !$phpsettings}<tr><td colspan="4" class="odd">{tr}No records found.{/tr}</td></tr>
{/if}
</table>
<br />
<h2>{tr}Tikiwiki settings{/tr}</h2>
<table class="normal">
<tr><th class="heading">{tr}Tiki variable{/tr}</th>
<th class="heading">{tr}Setting{/tr}</th>
<th class="heading">{tr}Risk Factor{/tr}</th>
<th class="heading">{tr}Explanation{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach from=$tikisettings key=key item=item}
<tr><td class="{cycle advance=false}">{$key}</td>
<td class="{cycle advance=false}">{$item.setting}</td>
<td class="{cycle advance=false}">
{if $item.risk eq 'safe'}{icon _id=accept.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'risky'}{icon _id=exclamation.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unsafe'}{icon _id=exclamation.png alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unknown'}{icon _id=error.png alt="$item.risk" style="vertical-align:middle"}
{/if}
{$item.risk}</td>
<td class="{cycle}">{$item.message}</td></tr>
{/foreach}
{if !$tikisettings}<tr><td colspan="4" class="odd">{tr}No records found.{/tr}</td></tr>
{/if}
</table>
<br />
<h2>{tr}Security checks{/tr}</h2>
<div class="form">
<a href="tiki-admin_security.php?check_files">{tr}Check all tiki files{/tr}</a><br />
{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}<br />
{tr}Note: You have to import security data via installation process (<a href="tiki-install.php">tiki-install.php</a>). Import the *secdb* update files in your database.{/tr}
<br />
</div>
{if $filecheck}
<table>
<tr><td colspan="4" class="heading">{tr}File checks{/tr}</td></tr>
<tr><td class="heading">{tr}Filename{/tr}</td>
<td class="heading">{tr}State{/tr}</td>
</tr>
{foreach from=$tikifiles key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item}</td></tr>
{/foreach}
</table>
{/if}

<br />
<div class="form">
<a href="tiki-admin_security.php?check_file_permissions">{tr}Check file permissions{/tr}</a><br />
{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}<br />
{tr}This check tries to find files with problematic file permissions. Some file permissions that are shown here as problematic may be unproblematic or unavoidable in some environments.{/tr}<br />
{tr}See end of table for detailed explanations.{/tr}
<br />
</div>
{if $permcheck}
<table>
<tr><td class="heading" rowspan="2">{tr}Filename{/tr}</td>
<td class="heading" rowspan="2">{tr}type{/tr}</td>
<td class="heading" colspan="2">{tr}owner{/tr}</td>
<td class="heading" colspan="3">{tr}special{/tr}</td>
<td class="heading" colspan="3">{tr}user{/tr}</td>
<td class="heading" colspan="3">{tr}group{/tr}</td>
<td class="heading" colspan="3">{tr}other{/tr}</td>
</tr>
<tr>
<td class="heading">{tr}uid{/tr}</td>
<td class="heading">{tr}gid{/tr}</td>
<td class="heading">{tr}suid{/tr}</td>
<td class="heading">{tr}sgid{/tr}</td>
<td class="heading">{tr}sticky{/tr}</td>
<td class="heading">{tr}r{/tr}</td>
<td class="heading">{tr}w{/tr}</td>
<td class="heading">{tr}x{/tr}</td>
<td class="heading">{tr}r{/tr}</td>
<td class="heading">{tr}w{/tr}</td>
<td class="heading">{tr}x{/tr}</td>
<td class="heading">{tr}r{/tr}</td>
<td class="heading">{tr}w{/tr}</td>
<td class="heading">{tr}x{/tr}</td>
</tr>
<tr><td colspan="16" class="heading">{tr}Set User ID (suid) files{/tr}</td></tr>
{foreach from=$suid key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item.t}</td>
<td class="form">{$item.u}</td>
<td class="form">{$item.g}</td>
<td class="form">{$item.suid|truex}</td>
<td class="form">{$item.sgid|truex}</td>
<td class="form">{$item.sticky|truex}</td>
<td class="form">{$item.ur|truex}</td>
<td class="form">{$item.uw|truex}</td>
<td class="form">{$item.ux|truex}</td>
<td class="form">{$item.gr|truex}</td>
<td class="form">{$item.gw|truex}</td>
<td class="form">{$item.gx|truex}</td>
<td class="form">{$item.or|truex}</td>
<td class="form">{$item.ow|truex}</td>
<td class="form">{$item.ox|truex}</td>
</tr>
{/foreach}

<tr><td colspan="16" class="heading">{tr}World writable files or directories{/tr}</td></tr>
{foreach from=$worldwritable key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item.t}</td>
<td class="form">{$item.u}</td>
<td class="form">{$item.g}</td>
<td class="form">{$item.suid|truex}</td>
<td class="form">{$item.sgid|truex}</td>
<td class="form">{$item.sticky|truex}</td>
<td class="form">{$item.ur|truex}</td>
<td class="form">{$item.uw|truex}</td>
<td class="form">{$item.ux|truex}</td>
<td class="form">{$item.gr|truex}</td>
<td class="form">{$item.gw|truex}</td>
<td class="form">{$item.gx|truex}</td>
<td class="form">{$item.or|truex}</td>
<td class="form">{$item.ow|truex}</td>
<td class="form">{$item.ox|truex}</td>
</tr>
{/foreach}

<tr><td colspan="16" class="heading">{tr}Files or directories the Webserver can write to{/tr}</td></tr>
{foreach from=$apachewritable key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item.t}</td>
<td class="form">{$item.u}</td>
<td class="form">{$item.g}</td>
<td class="form">{$item.suid|truex}</td>
<td class="form">{$item.sgid|truex}</td>
<td class="form">{$item.sticky|truex}</td>
<td class="form">{$item.ur|truex}</td>
<td class="form">{$item.uw|truex}</td>
<td class="form">{$item.ux|truex}</td>
<td class="form">{$item.gr|truex}</td>
<td class="form">{$item.gw|truex}</td>
<td class="form">{$item.gx|truex}</td>
<td class="form">{$item.or|truex}</td>
<td class="form">{$item.ow|truex}</td>
<td class="form">{$item.ox|truex}</td>
</tr>
{/foreach}

<tr><td colspan="16" class="heading">{tr}Strange Inodes (not file, not link, not directory){/tr}</td></tr>
{foreach from=$strangeinode key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item.t}</td>
<td class="form">{$item.u}</td>
<td class="form">{$item.g}</td>
<td class="form">{$item.suid|truex}</td>
<td class="form">{$item.sgid|truex}</td>
<td class="form">{$item.sticky|truex}</td>
<td class="form">{$item.ur|truex}</td>
<td class="form">{$item.uw|truex}</td>
<td class="form">{$item.ux|truex}</td>
<td class="form">{$item.gr|truex}</td>
<td class="form">{$item.gw|truex}</td>
<td class="form">{$item.gx|truex}</td>
<td class="form">{$item.or|truex}</td>
<td class="form">{$item.ow|truex}</td>
<td class="form">{$item.ox|truex}</td>
</tr>
{/foreach}

<tr><td colspan="16" class="heading">{tr}Executable files{/tr}</td></tr>
{foreach from=$executable key=key item=item}
<tr><td class="form">{$key}</td>
<td class="form">{$item.t}</td>
<td class="form">{$item.u}</td>
<td class="form">{$item.g}</td>
<td class="form">{$item.suid|truex}</td>
<td class="form">{$item.sgid|truex}</td>
<td class="form">{$item.sticky|truex}</td>
<td class="form">{$item.ur|truex}</td>
<td class="form">{$item.uw|truex}</td>
<td class="form">{$item.ux|truex}</td>
<td class="form">{$item.gr|truex}</td>
<td class="form">{$item.gw|truex}</td>
<td class="form">{$item.gx|truex}</td>
<td class="form">{$item.or|truex}</td>
<td class="form">{$item.ow|truex}</td>
<td class="form">{$item.ox|truex}</td>
</tr>
{/foreach}

</table>

<div class="form">
<br />
<br />
<br />

{tr}What to do with these check results?{/tr}<br /><br />
{tr}Set User ID (suid) files{/tr}<br />
{tr}Suid files are not part of tiki and there is no need for suid files in a webspace. Sometimes intruders that gain elevated privileges leave suid files to "keep the door open".{/tr}<br />
<br />
{tr}World writable files or directories{/tr}<br />
{tr}In some environments where you cannot get root or have no other possibilities, it is unavoidable to let your webserver write to some tiki directories like "templates_c" or "temp". In any other case this is not needed. A bug in a script or other users could easily put malicious scripts on your webspace or upload illegal content.{/tr}<br />
<br />
{tr}Files or directories the Webserver can write to{/tr}<br />
{tr}The risk is almost the same in shared hosting environments without proper privilege separation (suexec wrappers). The webserver has to be able to write to some directories like "templates_c" or "temp". Review the tiki install guide for further information.{/tr}<br />
<br />
{tr}Strange Inodes (not file, not link, not directory){/tr}<br />
{tr}Inodes that are not files or directories are not part of tiki. Review these Inodes!{/tr}<br />
<br />
{tr}Executable files{/tr}<br />
{tr}Setting the executable bit can be dangerous if the webserver is configured to execute cgi scripts from that directories. If you use the usual php module (for apache) then php scripts and other files in tiki do not need to have the executable bit. You can safely remove the executable bit with chmod.{/tr}<br />
</div>

{/if}

