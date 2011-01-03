{title help="User+Calendar"}{tr}Mini Calendar: Preferences{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="navbar">
	{button href="tiki-minical.php#add" _text="{tr}Add{/tr} "}
	{button href="tiki-minical_prefs.php" _text="{tr}Prefs{/tr}"}
	{button href="tiki-minical.php?view=daily" _text="{tr}Daily{/tr}"}
	{button href="tiki-minical.php?view=weekly" _text="{tr}Weekly{/tr}"}
	{button href="tiki-minical.php?view=list" _text="{tr}List{/tr}"}
</div>

<h2>{tr}Preferences{/tr}</h2>
<form action="tiki-minical_prefs.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Calendar Interval in daily view{/tr}</td>
			<td>
				<select name="minical_interval">
					<option value="300" {if $minical_interval eq 300}selected="selected"{/if}>5 {tr}minutes{/tr}</option>
					<option value="600" {if $minical_interval eq 600}selected="selected"{/if}>10 {tr}minutes{/tr}</option>
					<option value="900" {if $minical_interval eq 900}selected="selected"{/if}>15 {tr}minutes{/tr}</option>
					<option value="1800" {if $minical_interval eq 1800}selected="selected"{/if}>30 {tr}minutes{/tr}</option>
					<option value="3600" {if $minical_interval eq 3600}selected="selected"{/if}>1 {tr}hour{/tr}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Start hour for days{/tr}</td>
			<td>
				<select name="minical_start_hour">
					{html_options output=$hours values=$hours selected=$minical_start_hour}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}End hour for days{/tr}</td>
			<td>
				<select name="minical_end_hour">
					{html_options output=$hours values=$hours selected=$minical_end_hour}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Upcoming events{/tr}</td>
			<td>
				<select name="minical_upcoming">
					{html_options output=$upcoming values=$upcoming selected=$minical_upcoming}
				</select>
			</td>
		</tr>
		<tr>
			<td>{tr}Reminders{/tr}</td>
			<td>
				<select name="minical_reminders">
					<option value="0" {if $prefs.minical_reminders eq 0}selected="selected"{/if}>{tr}no reminders{/tr}</option>
					<option value="60" {if $prefs.minical_reminders eq 60}selected="selected"{/if}>1 {tr}min{/tr}</option>
					<option value="120" {if $prefs.minical_reminders eq 120}selected="selected"{/if}>2 {tr}min{/tr}</option>
					<option value="300" {if $prefs.minical_reminders eq 300}selected="selected"{/if}>5 {tr}min{/tr}</option>
					<option value="600" {if $prefs.minical_reminders eq 600}selected="selected"{/if}>10 {tr}min{/tr}</option>
					<option value="900" {if $prefs.minical_reminders eq 900}selected="selected"{/if}>15 {tr}min{/tr}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>	
	</table>
</form>
<a name="import"></a>
<h2>{tr}Import CSV file{/tr}</h2>
<form  enctype="multipart/form-data"  action="tiki-minical_prefs.php" method="post">
	<table class="formcolor">
		<tr>
			<td>{tr}Upload file:{/tr}</td>
			<td><input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" />
				<input size="16" name="userfile1" type="file" /><input type="submit" name="import" value="{tr}import{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Admin Topics{/tr}</h2>
<form  enctype="multipart/form-data"  action="tiki-minical_prefs.php" method="post">
	<table class="normal">
		<tr>
			<td>{tr}Name:{/tr}</td><td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>{tr}Upload file:{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="10000000000000" /><input size="16" name="userfile1" type="file" /></td>
		</tr>
		<tr>
			<td>{tr}Or enter path or URL:{/tr}</td><td><input type="text" name="path" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="addtopic" value="{tr}Add Topic{/tr}" />
			</td>
		</tr>
	</table>
</form>
{if count($topics) > 0}
	<div class="simplebox">
		<table>
			<tr>
				{section name=numloop loop=$topics}
					<td>
						{if $topics[numloop].isIcon eq 'y'}
							<img src="{$topics[numloop].path}" alt="{tr}topic image{/tr}" />
						{else}
							<img src="tiki-view_minical_topic.php?topicId={$topics[numloop].topicId}" alt="{tr}topic image{/tr}" />
						{/if}
						{$topics[numloop].name}
						[<a class="link" href="tiki-minical_prefs.php?removetopic={$topics[numloop].topicId}">x</a>]
					</td>
					{* see if we should go to the next row *}
					{if not ($smarty.section.numloop.rownum mod $cols)}
						{if not $smarty.section.numloop.last}
							</tr>
							<tr>
						{/if}
					{/if}
					{if $smarty.section.numloop.last}
						{* pad the cells not yet created *}
						{math equation = "n - a % n" n=$cols a=$data|@count assign="cells"}
						{if $cells ne $cols}
							{section name=pad loop=$cells}
								<td>&nbsp;</td>
							{/section}
						{/if}
						</tr>
					{/if}
				{/section}
		</table>
	</div>
{/if}
