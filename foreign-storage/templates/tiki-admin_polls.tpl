{* $Id$ *}
{title help="Polls" admpage="polls"}{tr}Admin Polls{/tr}{/title}

<div class="navbar">
	{button href="tiki-admin_polls.php?setlast=1" _text="{tr}Set last poll as current{/tr}"}
	{button href="tiki-admin_polls.php?closeall=1" _text="{tr}Close all polls but last{/tr}"}
	{button href="tiki-admin_polls.php?activeall=1" _text="{tr}Activate all polls{/tr}"}
	{if $pollId neq '0'}{button pollId=0 cookietab=1 _text="{tr}Create poll{/tr}"}{/if}
</div>

{tabset}
	{if $pollId eq '0'}
		{assign var='title' value="{tr}Create poll{/tr}"}
	{else}
		{assign var='title' value="{tr}Edit poll{/tr}"}
	{/if}
	{tab name=$title}
		<form action="tiki-admin_polls.php" method="post">
			<input type="hidden" name="pollId" value="{$pollId|escape}" />
			<table class="formcolor">
				<tr>
					<td>{tr}Title:{/tr}</td>
					<td><input type="text" name="title" value="{$info.title|escape}" /></td>
				</tr>
				<tr>
					<td>{tr}Active:{/tr}</td>
					<td>
						<select name="active">
							<option value='a' {if $info.active eq 'a'}selected="selected"{/if}>{tr}active{/tr}</option>
							<option value='c' {if $info.active eq 'c'}selected="selected"{/if}>{tr}current{/tr}</option>
							<option value='x' {if $info.active eq 'x'}selected="selected"{/if}>{tr}closed{/tr}</option>
							<option value='t' {if $info.active eq 't'}selected="selected"{/if} style="border-top:1px solid black;">{tr}template{/tr}</option>
							<option value='o' {if $info.active eq 'o'}selected="selected"{/if}>{tr}object{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Options:{/tr}</td>
					<td>
						<div>
							<a href="javascript://toggle quick options" onclick="pollsToggleQuickOptions()">{tr}Toggle Quick Options{/tr}</a>
						</div>
						<div id="tikiPollsQuickOptions" style="display: none">
							<div id="tikiPollsOptions">
								{section name=opt loop=$options}
									<div>
										<input type="hidden" name="optionsId[]" value="{$options[opt].optionId}" />
										<input type="text" name="options[]" value="{$options[opt].title}" />
									</div>
								{/section}
								<div><input type="text" name="options[]" /></div>
							</div>
							<a href="javascript://Add Option"	onclick="pollsAddOption()">{tr}Add Option{/tr}</a>
							{remarksbox type="tip" title="{tr}Tip{/tr}"}
								{tr}Leave box empty to delete an option.{/tr}
							{/remarksbox}
						</div>
					</td>
				</tr>
				{include file='categorize.tpl'}
				<tr>
					<td>{tr}Publish Date:{/tr}</td>
					<td>
						{html_select_date time=$info.publishDate end_year="+1" field_order=$prefs.display_field_order} {tr}at{/tr} 
						{html_select_time time=$info.publishDate display_seconds=false use_24_hours=$use_24hr_clock}
					</td>
				</tr>
				<tr>
					<td>
						<label id="voteConsiderationSpan">{tr}Votes older than these days are not considered{/tr}</label>
					</td>
					<td>
						<input type="text" id="voteConsiderationSpan" name="voteConsiderationSpan" size="5" value="{$info.voteConsiderationSpan|escape}"/>
						<br />
						<i>{tr}0 for no limit{/tr}</i>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
				</tr>
			</table>
		</form>
	{/tab}

	{tab name="{tr}Polls{/tr}"}
		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}
		<table class="normal">
			{assign var=numbercol value=8}
			<tr>
				<th>{self_link _sort_arg='sort_mode' _sort_field='pollId' title="{tr}ID{/tr}"}{tr}ID{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='title' title="{tr}Title{/tr}"}{tr}Title{/tr}{/self_link}</th>
				{if $prefs.poll_list_categories eq 'y'}<th>{tr}Categories{/tr}</th>{assign var=numbercol value=$numbercol+1}{/if}
				{if $prefs.poll_list_objects eq 'y'}<th>{tr}Objects{/tr}</th>{assign var=numbercol value=$numbercol+1}{/if}
				<th>{self_link _sort_arg='sort_mode' _sort_field='active' title="{tr}Active{/tr}"}{tr}Active{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='votes' title="{tr}Votes{/tr}"}{tr}Votes{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='publishDate' title="{tr}Publish{/tr}"}{tr}Publish{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='voteConsiderationSpan' title="{tr}Span{/tr}"}{tr}Span{/tr}{/self_link}</th>
				<th>{tr}Options{/tr}</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{cycle values="odd,even" print=false}
			{section name=user loop=$channels}
				<tr class="{cycle}">
					<td class="id">{$channels[user].pollId}</td>
					<td class="text">
						<a class="tablename" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{$channels[user].title|escape}</a>
					</td>
					{if $prefs.poll_list_categories eq 'y'}
						<td class="text">
							{section name=cat loop=$channels[user].categories}
								{$channels[user].categories[cat].name}
								{if !$smarty.section.cat.last}
									<br />
								{/if}
							{/section}
						</td>
					{/if}
					{if $prefs.poll_list_objects eq 'y'}
						<td class="text">
							{section name=obj loop=$channels[user].objects}
								<a href="{$channels[user].objects[obj].href}">{$channels[user].objects[obj].name}</a>
								{if !$smarty.section.obj.last}
									<br />
								{/if}
							{/section}
						</td>
					{/if}
					<td class="text">{$channels[user].active}</td>
					<td class="integer">{$channels[user].votes}</td>
					<td class="date">{$channels[user].publishDate|tiki_short_datetime}</td>
					<td class="integer">{$channels[user].voteConsiderationSpan|escape}</td>
					<td class="integer">{$channels[user].options}</td>
					<td class="action">
						{self_link pollId=$channels[user].pollId}{icon _id=page_edit}{/self_link}
						<a class="link" href="tiki-admin_poll_options.php?pollId={$channels[user].pollId}" title="{tr}Options{/tr}">{icon _id=table alt="{tr}Options{/tr}"}</a>
						<a class="link" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{icon _id="chart_curve" alt="{tr}Results{/tr}"}</a>
						<a class="link" href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pollId}" title="{tr}Delete{/tr}">{icon _id=cross alt="{tr}Delete{/tr}"}</a>
					</td>
				</tr>
			{sectionelse}
	         {norecords _colspan=$numbercol}
			{/section}
		</table>
		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Add poll to pages{/tr}"}
		<form action="tiki-admin_polls.php" method="post">
			<table class="formcolor">
				<tr>
					<td>{tr}Poll{/tr}</td>
					<td>
						<select name="poll_template">
							{section name=ix loop=$channels}
								{if $channels[ix].active eq 't'}
									<option value="{$channels[ix].pollId|escape}"{if $smarty.section.ix.first} selected="selected"{/if}>{tr}{$channels[ix].title}{/tr}</option>
								{/if}
							{/section}
						</select>
						{remarksbox type="tip" title="Tip"}{tr}This menu shows only Polls with 'status': "template"{/tr}{/remarksbox}
					</td>
				</tr>
				<tr>
					<td>{tr}Title{/tr}</td>
					<td><input type="text" name="poll_title" /></td>
				</tr>
				<tr>
					<td>{tr}Wiki pages{/tr}</td>
					<td>
						<select name="pages[]" multiple="multiple" size="20">
							{section name=ix loop=$listPages}
								<option value="{$listPages[ix].pageName|escape}">{tr}{$listPages[ix].pageName|escape}{/tr}</option>
							{/section}
						</select>
						{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
					</td>
				</tr>
				<tr>
					<td>{tr}Lock the pages{/tr}</td>
					<td><input type="checkbox" name="locked" /></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" name="addPoll" value="{tr}Add{/tr}" /></td>
				</tr>
			</table>
		</form>
	{/tab}
{/tabset}
