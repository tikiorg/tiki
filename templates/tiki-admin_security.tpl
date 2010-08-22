{title help="Security+Admin"}{tr}Security Admin{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To <a class="rbox-link" target="tikihelp" href="http://security.tiki.org/tiki-contact.php">report any security issues</a>.{/tr}{/remarksbox}

<h2>{tr}PHP settings{/tr}</h2>
<table class="normal">
<tr><th>{tr}PHP variable{/tr}</th>
<th>{tr}Setting{/tr}</th>
<th>{tr}Risk Factor{/tr}</th>
<th>{tr}Explanation{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach from=$phpsettings key=key item=item}
<tr><td class="{cycle advance=false}">{$key}</td>
<td class="{cycle advance=false}">{$item.setting}</td>
<td class="{cycle advance=false}">
{if $item.risk eq 'safe'}{icon _id=accept alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'risky'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unsafe'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unknown'}{icon _id=error alt="$item.risk" style="vertical-align:middle"}
{/if}
{$item.risk}</td>
<td class="{cycle advance=true}">{$item.message}</td></tr>
{/foreach}
{if !$phpsettings}<tr><td colspan="4" class="odd">{tr}No records found.{/tr}</td></tr>
{/if}
</table>
<h2>{tr}PHP functions{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Function{/tr}</th>
		<th>{tr}Setting{/tr}</th>
		<th>{tr}Risk Factor{/tr}</th>
	</tr>
	{foreach from=$phpfunctions key=key item=item}
		<tr class="{cycle}">
			<td>{$key}</td>
			<td>{$item.setting}</td>
			<td>{if $item.risk eq 'safe'}{icon _id=accept alt="$item.risk" style="vertical-align:middle"}
				{elseif $item.risk eq 'risky'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
				{elseif $item.risk eq 'unsafe'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
				{elseif $item.risk eq 'unknown'}{icon _id=error alt="$item.risk" style="vertical-align:middle"}
				{/if}
				{$item.risk}
			</td>
		</tr>
	{/foreach}
</table>
{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To disallow a function, add a disable_functions=exec,passthru for instance in your php.ini{/tr}{/remarksbox}
<br />
<h2>{tr}Tiki settings{/tr}</h2>
<table class="normal">
<tr><th>{tr}Tiki variable{/tr}</th>
<th>{tr}Setting{/tr}</th>
<th>{tr}Risk Factor{/tr}</th>
<th>{tr}Explanation{/tr}</th></tr>
{cycle values="even,odd" print=false}
{foreach from=$tikisettings key=key item=item}
<tr><td class="{cycle advance=false}">{$key}</td>
<td class="{cycle advance=false}">{$item.setting}</td>
<td class="{cycle advance=false}">
{if $item.risk eq 'safe'}{icon _id=accept alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'risky'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unsafe'}{icon _id=exclamation alt="$item.risk" style="vertical-align:middle"}
{elseif $item.risk eq 'unknown'}{icon _id=error alt="$item.risk" style="vertical-align:middle"}
{/if}
{$item.risk}</td>
<td class="{cycle}">{$item.message}</td></tr>
{/foreach}
{if !$tikisettings}<tr><td colspan="4" class="odd">{tr}No records found.{/tr}</td></tr>
{/if}
</table>
{tr}About WikiPlugins and security: Make sure to only grant the "tiki_p_plugin_approve" permission to trusted editors.{/tr} {tr}You can deactivate risky plugins at (<a href="tiki-admin.php?page=textarea">tiki-admin.php?page=textarea</a>).{/tr} {tr}You can approve plugin use at <a href="tiki-plugins.php">tiki-plugins.php</a>.{/tr}

<br />
<h2>{tr}Security checks{/tr}</h2>
<div class="form">
<a href="tiki-admin_security.php?check_files">{tr}Check all tiki files{/tr}</a><br />
{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}<br />
<br />
</div>
{if $filecheck}
<table>
<tr><th colspan="4">{tr}File checks{/tr}</th></tr>
<tr><th>{tr}Filename{/tr}</th>
<th>{tr}State{/tr}</th>
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
<tr><th rowspan="2">{tr}Filename{/tr}</th>
<th rowspan="2">{tr}type{/tr}</th>
<th colspan="2">{tr}owner{/tr}</th>
<th colspan="3">{tr}special{/tr}</th>
<th colspan="3">{tr}user{/tr}</th>
<th colspan="3">{tr}group{/tr}</th>
<th colspan="3">{tr}other{/tr}</th>
</tr>
<tr>
<th>{tr}uid{/tr}</th>
<th>{tr}gid{/tr}</th>
<th>{tr}suid{/tr}</th>
<th>{tr}sgid{/tr}</th>
<th>{tr}sticky{/tr}</th>
<th>{tr}r{/tr}</th>
<th>{tr}w{/tr}</th>
<th>{tr}x{/tr}</th>
<th>{tr}r{/tr}</th>
<th>{tr}w{/tr}</th>
<th>{tr}x{/tr}</th>
<th>{tr}r{/tr}</th>
<th>{tr}w{/tr}</th>
<th>{tr}x{/tr}</th>
</tr>
<tr><th colspan="16">{tr}Set User ID (suid) files{/tr}</th></tr>
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

<tr><th colspan="16">{tr}World writable files or directories{/tr}</th></tr>
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

<tr><th colspan="16">{tr}Files or directories the Webserver can write to{/tr}</th></tr>
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

<tr><th colspan="16">{tr}Strange Inodes (not file, not link, not directory){/tr}</th></tr>
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

<tr><th colspan="16">{tr}Executable files{/tr}</th></tr>
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

