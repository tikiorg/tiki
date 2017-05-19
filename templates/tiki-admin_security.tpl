{* $Id$ *}
{title help="Security Admin" admpage="security"}{tr}Security Admin{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}To <a class="rbox-link" target="tikihelp" href="http://security.tiki.org/tiki-contact.php">report any security issues</a>.{/tr}
	{tr}For additional security checks, please visit <a href="tiki-check.php">Tiki Server Compatibility Check</a>.{/tr}
{/remarksbox}

<h2>{tr}Tiki settings{/tr}</h2>
<div class="table-responsive secsetting-table">
	<table class="table table-striped table-hover">
		<tr>
			<th>{tr}Tiki variable{/tr}</th>
			<th>{tr}Setting{/tr}</th>
			<th>{tr}Risk Factor{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>

		{foreach from=$tikisettings key=key item=item}
			<tr>
				<td class="text">{$key}</td>
				<td class="text">{$item.setting}</td>
				<td class="text">
					<span class="text-{$fmap[$item.risk]['class']}">
						{icon name="{$fmap[$item.risk]['icon']}"} {$item.risk}
					</span>
				<td class="text">{$item.message}</td>
			</tr>
		{/foreach}
		{if !$tikisettings}
			{norecords _colspan=4}
		{/if}
	</table>
</div>
{tr}About WikiPlugins and security: Make sure to only grant the "tiki_p_plugin_approve" permission to trusted editors.{/tr} {tr}You can deactivate risky plugins at (<a href="tiki-admin.php?page=textarea">tiki-admin.php?page=textarea</a>).{/tr} {tr}You can approve plugin use at <a href="tiki-plugins.php">tiki-plugins.php</a>.{/tr}

<br>
<h2>{tr}Security checks{/tr}</h2>
<div>
	<a href="tiki-admin_security.php?check_files" class="btn btn-default">{tr}Check all tiki files{/tr}</a>
	<br>
	{remarksbox type="tip" title="{tr}Info{/tr}"}
		{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}
	{/remarksbox}
	<br>
	<br>
</div>

{if $filecheck}
	<div class="table-responsive secfile-table">
		<table class="table table-striped table-hover">
			<tr>
				<th colspan="2">{tr}File checks{/tr}</th>
			</tr>
			<tr>
				<th>{tr}Filename{/tr}</th>
				<th>{tr}State{/tr}</th>
			</tr>
			{foreach from=$tikifiles key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item}</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/if}

<a href="tiki-admin_security.php?check_file_permissions" class="btn btn-default">{tr}Check file permissions{/tr}</a>

{remarksbox type="tip" title="{tr}Info{/tr}"}
	{tr}Note, that this can take a very long time. You should check your max_execution_time setting in php.ini.{/tr}
	<br>
	{tr}This check tries to find files with problematic file permissions. Some file permissions that are shown here as problematic may be unproblematic or unavoidable in some environments.{/tr}
	<br>
	{tr}See end of table for detailed explanations.{/tr}
{/remarksbox}


{if $permcheck}
	<div class="table-responsive secperm-table">
		<table class="table table-striped table-hover">
			<tr>
				<th>{tr}Filename{/tr}</th>
				<th>{tr}type{/tr}</th>
				<th colspan="2">{tr}owner{/tr}</th>
				<th colspan="3">{tr}special{/tr}</th>
				<th>{tr}user{/tr}</th>
				<th>{tr}group{/tr}</th>
				<th>{tr}other{/tr}</th>
			</tr>
			<tr>
				<th colspan="2">&#160;</th>
				<th>{tr}uid{/tr}</th>
				<th>{tr}gid{/tr}</th>
				<th>{tr}suid{/tr}</th>
				<th>{tr}sgid{/tr}</th>
				<th>{tr}sticky{/tr}</th>
				<th>{tr}r{/tr}{tr}w{/tr}{tr}x{/tr}</th>
				<th>{tr}r{/tr}{tr}w{/tr}{tr}x{/tr}</th>
				<th>{tr}r{/tr}{tr}w{/tr}{tr}x{/tr}</th>
			</tr>
			<tr>
				<th colspan="16">{tr}Set User ID (suid) files{/tr}</th>
			</tr>

			{foreach from=$suid key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item.t}</td>
					<td class="text">{$item.u}</td>
					<td class="text">{$item.g}</td>
					<td class="text">{$item.suid|truex}</td>
					<td class="text">{$item.sgid|truex}</td>
					<td class="text">{$item.sticky|truex}</td>
					<td class="text">{$item.ur|truex}{$item.uw|truex}{$item.ux|truex}</td>
					<td class="text">{$item.gr|truex}{$item.gw|truex}{$item.gx|truex}</td>
					<td class="text">{$item.or|truex}{$item.ow|truex}{$item.ox|truex}</td>
				</tr>
			{/foreach}

			<tr>
				<th colspan="16">{tr}World writable files or directories{/tr}</th>
			</tr>
			{foreach from=$worldwritable key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item.t}</td>
					<td class="text">{$item.u}</td>
					<td class="text">{$item.g}</td>
					<td class="text">{$item.suid|truex}</td>
					<td class="text">{$item.sgid|truex}</td>
					<td class="text">{$item.sticky|truex}</td>
					<td class="text">{$item.ur|truex}{$item.uw|truex}{$item.ux|truex}</td>
					<td class="text">{$item.gr|truex}{$item.gw|truex}{$item.gx|truex}</td>
					<td class="text">{$item.or|truex}{$item.ow|truex}{$item.ox|truex}</td>
				</tr>
			{/foreach}

			<tr>
				<th colspan="16">{tr}Files or directories the Webserver can write to{/tr}</th>
			</tr>
			{foreach from=$apachewritable key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item.t}</td>
					<td class="text">{$item.u}</td>
					<td class="text">{$item.g}</td>
					<td class="text">{$item.suid|truex}</td>
					<td class="text">{$item.sgid|truex}</td>
					<td class="text">{$item.sticky|truex}</td>
					<td class="text">{$item.ur|truex}{$item.uw|truex}{$item.ux|truex}</td>
					<td class="text">{$item.gr|truex}{$item.gw|truex}{$item.gx|truex}</td>
					<td class="text">{$item.or|truex}{$item.ow|truex}{$item.ox|truex}</td>
				</tr>
			{/foreach}

			<tr>
				<th colspan="16">{tr}Strange Inodes (not file, not link, not directory){/tr}</th>
			</tr>
			{foreach from=$strangeinode key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item.t}</td>
					<td class="text">{$item.u}</td>
					<td class="text">{$item.g}</td>
					<td class="text">{$item.suid|truex}</td>
					<td class="text">{$item.sgid|truex}</td>
					<td class="text">{$item.sticky|truex}</td>
					<td class="text">{$item.ur|truex}{$item.uw|truex}{$item.ux|truex}</td>
					<td class="text">{$item.gr|truex}{$item.gw|truex}{$item.gx|truex}</td>
					<td class="text">{$item.or|truex}{$item.ow|truex}{$item.ox|truex}</td>
				</tr>
			{/foreach}

			<tr>
				<th colspan="16">{tr}Executable files{/tr}</th>
			</tr>
			{foreach from=$executable key=key item=item}
				<tr>
					<td class="url">{$key}</td>
					<td class="text">{$item.t}</td>
					<td class="text">{$item.u}</td>
					<td class="text">{$item.g}</td>
					<td class="text">{$item.suid|truex}</td>
					<td class="text">{$item.sgid|truex}</td>
					<td class="text">{$item.sticky|truex}</td>
					<td class="text">{$item.ur|truex}{$item.uw|truex}{$item.ux|truex}</td>
					<td class="text">{$item.gr|truex}{$item.gw|truex}{$item.gx|truex}</td>
					<td class="text">{$item.or|truex}{$item.ow|truex}{$item.ox|truex}</td>
				</tr>
			{/foreach}
		</table>
	</div>

	{remarksbox type="tip" title="{tr}Info{/tr}"}
		{tr}What to do with these check results?{/tr}
		<br>
		{tr}Set User ID (suid) files{/tr}
		<br>
		{tr}Suid files are not part of tiki and there is no need for suid files in a webspace. Sometimes intruders that gain elevated privileges leave suid files to "keep the door open".{/tr}
		<br>
		{tr}World writable files or directories{/tr}
		<br>
		{tr}In some environments where you cannot get root or have no other possibilities, it is unavoidable to let your webserver write to some tiki directories like or "temp". In any other case this is not needed. A bug in a script or other users could easily put malicious scripts on your webspace or upload illegal content.{/tr}
		<br>
		{tr}Files or directories the Webserver can write to{/tr}
		<br>
		{tr}The risk is almost the same in shared hosting environments without proper privilege separation (suexec wrappers). The webserver has to be able to write to some directories like "temp". Review the tiki install guide for further information.{/tr}
		<br>
		{tr}Strange Inodes (not file, not link, not directory){/tr}
		<br>
		{tr}Inodes that are not files or directories are not part of tiki. Review these Inodes!{/tr}
		<br>
		{tr}Executable files{/tr}
		<br>
		{tr}Setting the executable bit can be dangerous if the webserver is configured to execute cgi scripts from that directories. If you use the usual php module (for apache) then php scripts and other files in tiki do not need to have the executable bit. You can safely remove the executable bit with chmod.{/tr}
		<br>
	{/remarksbox}
{/if}
