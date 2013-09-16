{* $Id$ *}
{title help="Server Check"}{tr}Server Check{/tr}{/title}

<h2>{tr}MySQL or MariaDB Database Properties{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$mysql_properties key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.setting}</td>
			<td class="text">
				{if $item.fitness eq 'good'}
					{icon _id=accept alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad'}
					{icon _id=exclamation alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'info'}
					{icon _id=information alt="" style="vertical-align:middle"}					
				{elseif $item.fitness eq 'unknown'}
					{icon _id=no_information alt="" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=4}
	{/foreach}
</table>
{if $haveMySQLSSL}
	{if $mysqlSSL === true}
		<p class="mysqlsslstatus"><img src="img/icons/lock.png" style="outline:lightgreen solid thin"/> {tr}MySQL SSL connection is active{/tr}</p>
	{else}
		<p class="mysqlsslstatus"><img src="img/icons/lock_open.png"  style="outline:pink solid thin"/> {tr}MySQL connection is not encrypted{/tr}<br>
		{tr}To activate SSL, copy the keyfiles (.pem) til db/cert folder. The filenames must end with "-key.pem", "-cert.pem", "-ca.pem"{/tr}
		</p>
	{/if}
{else}
	<p><img src="img/icons/lock_gray.png" style="outline:pink solid thin"/> {tr}MySQL Server does not have SSL activated{/tr}
	</p>
{/if}

<h2>{tr}MySQL crashed Tables{/tr}</h2>
{remarksbox type="note" title="{tr}Be careful{/tr}"}{tr}The following list is just a very quick look at SHOW TABLE STATUS that tells you, if tables have been marked as crashed. If you are experiencing database problems you should still run CHECK TABLE or myisamchk to make sure{/tr}.{/remarksbox}
<table class="normal">
	<tr>
		<th>{tr}Table{/tr}</th>
		<th>{tr}Comment{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$mysql_crashed_tables key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.Comment}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>

<h2>{tr}Test sending e-mails{/tr}</h2>
{tr}To test if your installation is capable of sending emails please visit the <a href="tiki-install.php">Tiki Installer</a>{/tr}.

<h2>{tr}Server Information{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$server_information key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.value}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>

<h2>{tr}Server Properties{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$server_properties key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.value}</td>
			<td class="text">
				{if $item.fitness eq 'good'}
					{icon _id=accept alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad'}
					{icon _id=exclamation alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'info'}
					{icon _id=information alt="" style="vertical-align:middle"}							
				{elseif $item.fitness eq 'unknown'}
					{icon _id=no_information alt="" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=4}
	{/foreach}
</table>

<h2>{tr}Special directories{/tr}</h2>
{tr}To backup these directories go to <a href="tiki-admin_system.php">Admin->Tiki Cache/SysAdmin</a>{/tr}.
{if count($dirs)}
	<table class="normal">
		<tr>
			<th>{tr}Directory{/tr}</th>
			<th>{tr}Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>
		{cycle values="even,odd" print=false}
		{foreach from=$dirs item=d key=k}
			<tr class="{cycle}">
				<td class="text">{$d|escape}</td>
				<td class="text">
					{if $dirsWritable[$k]}
						{icon _id=accept alt="" style="vertical-align:middle"}
					{else}
						{icon _id=exclamation alt="" style="vertical-align:middle"}
					{/if}
				</td>
				<td>
					{if $dirsWritable[$k]}
						{tr}Directory is writeable{/tr}.
					{else}
						{tr}Directory is not writeable!{/tr}
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
{/if}


<h2>{tr}Apache properties{/tr}</h2>
{if $apache_properties}
	<table class="normal">
		<tr>
			<th>{tr}Property{/tr}</th>
			<th>{tr}Value{/tr}</th>
			<th>{tr}Tiki Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>
		{cycle values="even,odd" print=false}
		{foreach from=$apache_properties key=key item=item}
			<tr class="{cycle}">
				<td class="text">{$key}</td>
				<td class="text">{$item.setting}</td>
				<td class="text">
					{if $item.fitness eq 'good'}
						{icon _id=accept alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'bad'}
						{icon _id=exclamation alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'ugly'}
						{icon _id=error alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'info'}
						{icon _id=information alt="" style="vertical-align:middle"}								
					{elseif $item.fitness eq 'unknown'}
						{icon _id=no_information alt="" style="vertical-align:middle"}
					{/if}
					{$item.fitness}
				</td>
				<td class="text">{$item.message}</td>
			</tr>
		{foreachelse}
			{norecords _colspan=4}
		{/foreach}
	</table>
{else}
	{$no_apache_properties}
{/if}

<h2>{tr}IIS properties{/tr}</h2>
{if $iis_properties}
	<table class="normal">
		<tr>
			<th>{tr}Property{/tr}</th>
			<th>{tr}Value{/tr}</th>
			<th>{tr}Tiki Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>
		{cycle values="even,odd" print=false}
		{foreach from=$iis_properties key=key item=item}
			<tr class="{cycle}">
				<td class="text">{$key}</td>
				<td class="text">{$item.setting}</td>
				<td class="text">
					{if $item.fitness eq 'good'}
						{icon _id=accept alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'bad'}
						{icon _id=exclamation alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'ugly'}
						{icon _id=error alt="" style="vertical-align:middle"}
					{elseif $item.fitness eq 'info'}
						{icon _id=information alt="" style="vertical-align:middle"}								
					{elseif $item.fitness eq 'unknown'}
						{icon _id=no_information alt="" style="vertical-align:middle"}
					{/if}
					{$item.fitness}
				</td>
				<td class="text">{$item.message}</td>
			</tr>
		{foreachelse}
			{norecords _colspan=4}
		{/foreach}
	</table>
{else}
	{$no_iis_properties}
{/if}

<h2>{tr}PHP scripting language properties{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$php_properties key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.setting}</td>
			<td class="text">
				{if $item.fitness eq 'good'}
					{icon _id=accept alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad'}
					{icon _id=exclamation alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'info'}
					{icon _id=information alt="" style="vertical-align:middle"}							
				{elseif $item.fitness eq 'unknown'}
					{icon _id=no_information alt="" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=4}
	{/foreach}
</table>

<h2>{tr}PHP Security properties{/tr}</h2>
{tr}To check the file integrity of your Tiki installation, go to <a href="tiki-admin_security.php">Admin->Security</a>{/tr}.
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$security key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.setting}</td>
			<td class="text">
				{if $item.fitness eq 'good' or $item.fitness eq 'safe'}
					{icon _id=accept alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'bad' or $item.fitness eq 'risky'}
					{icon _id=exclamation alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'ugly'}
					{icon _id=error alt="" style="vertical-align:middle"}
				{elseif $item.fitness eq 'info'}
					{icon _id=information alt="" style="vertical-align:middle"}							
				{elseif $item.fitness eq 'unknown'}
					{icon _id=no_information alt="" style="vertical-align:middle"}
				{/if}
				{$item.fitness}
			</td>
			<td class="text">{$item.message}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=4}
	{/foreach}
</table>

<h2>{tr}MySQL Variable Information{/tr}</h2>
<table class="normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
	</tr>
	{cycle values="even,odd" print=false}
	{foreach from=$mysql_variables key=key item=item}
		<tr class="{cycle}">
			<td class="text">{$key}</td>
			<td class="text">{$item.value}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>

<h2>{tr}PHP Info{/tr}</h2>
{tr}For more detailed information about your PHP installation see <a href="tiki-phpinfo.php">Admin->phpinfo</a>{/tr}.
