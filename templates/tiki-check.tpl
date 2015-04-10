{* $Id$ *}
{title help="Server Check"}{tr}Server Check{/tr}{/title}

<h2>{tr}MySQL or MariaDB Database Properties{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>

	{foreach from=$mysql_properties key=key item=item}
		<tr>
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
</div>

<h2>{tr}MySQL crashed Tables{/tr}</h2>
{remarksbox type="note" title="{tr}Be careful{/tr}"}{tr}The following list is just a very quick look at SHOW TABLE STATUS that tells you, if tables have been marked as crashed. If you are experiencing database problems you should still run CHECK TABLE or myisamchk to make sure{/tr}.{/remarksbox}
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Table{/tr}</th>
		<th>{tr}Comment{/tr}</th>
	</tr>

	{foreach from=$mysql_crashed_tables key=key item=item}
		<tr>
			<td class="text">{$key}</td>
			<td class="text">{$item.Comment}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>
</div>

<h2>{tr}Test sending e-mails{/tr}</h2>
{tr}To test if your installation is capable of sending emails please visit the <a href="tiki-install.php">Tiki Installer</a>{/tr}.

<h2>{tr}Server Information{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
	</tr>

	{foreach from=$server_information key=key item=item}
		<tr>
			<td class="text">{$key}</td>
			<td class="text">{$item.value}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>
</div>

<h2>{tr}Server Properties{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>

	{foreach from=$server_properties key=key item=item}
		<tr>
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
</div>

<h2>{tr}Special directories{/tr}</h2>
{tr}To backup these directories go to <a href="tiki-admin_system.php">Admin->Tiki Cache/SysAdmin</a>{/tr}.
{if count($dirs)}
    <div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>{tr}Directory{/tr}</th>
			<th>{tr}Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>

		{foreach from=$dirs item=d key=k}
			<tr>
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
    </div>
{/if}


<h2>{tr}Apache properties{/tr}</h2>
{if $apache_properties}
    <div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>{tr}Property{/tr}</th>
			<th>{tr}Value{/tr}</th>
			<th>{tr}Tiki Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>

		{foreach from=$apache_properties key=key item=item}
			<tr>
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
    </div>
{else}
	{$no_apache_properties}
{/if}

<h2>{tr}IIS properties{/tr}</h2>
{if $iis_properties}
    <div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>{tr}Property{/tr}</th>
			<th>{tr}Value{/tr}</th>
			<th>{tr}Tiki Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>

		{foreach from=$iis_properties key=key item=item}
			<tr>
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
    </div>
{else}
	{$no_iis_properties}
{/if}

<h2>{tr}PHP scripting language properties{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>

	{foreach from=$php_properties key=key item=item}
		<tr>
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
</div>

<h2>{tr}PHP Security properties{/tr}</h2>
{tr}To check the file integrity of your Tiki installation, go to <a href="tiki-admin_security.php">Admin->Security</a>{/tr}.
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
		<th>{tr}Tiki Fitness{/tr}</th>
		<th>{tr}Explanation{/tr}</th>
	</tr>

	{foreach from=$security key=key item=item}
		<tr>
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
</div>

<h2>{tr}File Gallery Search Indexing{/tr}</h2>
<em>More info <a href="https://doc.tiki.org/Search+within+files">here</a></em>
<div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>{tr}Mimetype{/tr}</th>
			<th>{tr}Tiki Fitness{/tr}</th>
			<th>{tr}Explanation{/tr}</th>
		</tr>

		{foreach from=$file_handlers key=key item=item}
			<tr>
				<td class="text">{$key}</td>
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
				<td class="text">{$item.message|escape}</td>
			</tr>
		{foreachelse}
			{norecords _colspan=3}
		{/foreach}
	</table>
</div>

<h2>{tr}MySQL Variable Information{/tr}</h2>
<div class="table-responsive">
<table class="table normal">
	<tr>
		<th>{tr}Property{/tr}</th>
		<th>{tr}Value{/tr}</th>
	</tr>

	{foreach from=$mysql_variables key=key item=item}
		<tr>
			<td class="text">{$key}</td>
			<td class="text">{$item.value|escape}</td>
		</tr>
	{foreachelse}
		{norecords _colspan=2}
	{/foreach}
</table>
</div>

<h2>{tr}PHP Info{/tr}</h2>
{tr}For more detailed information about your PHP installation see <a href="tiki-phpinfo.php">Admin->phpinfo</a>{/tr}.
