{* $Id$ *}
{title help="Polls" admpage="polls"}{tr}Admin Polls{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	<a href="tiki-admin_polls.php?setlast=1" class="btn btn-link" title="">{icon name="previous"} {tr}Set last poll as current{/tr} </a>
	<a href="tiki-admin_polls.php?closeall=1" class="btn btn-link" title="">{icon name="disable"} {tr}Close all polls but last{/tr}</a>
	<a href="tiki-admin_polls.php?activeall=1" class="btn btn-link" title="">{icon name="enable"} {tr}Activate all polls{/tr}</a>
	{if $pollId neq '0'}{button pollId=0 cookietab=1 class="btn btn-default" _icon_name="create" _text="{tr}Create poll{/tr}"}{/if}
</div>

{tabset}

	{if $pollId eq '0'}
		{assign var='title' value="{tr}Create poll{/tr}"}
	{else}
		{assign var='title' value="{tr}Edit poll{/tr}"}
	{/if}
	{tab name=$title}
		<h2>{$title}</h2>
		<form action="tiki-admin_polls.php" method="post" class="form-horizontal">
			<input type="hidden" name="pollId" value="{$pollId|escape}">

			<div class="form-group">
				<label class="col-sm-3 control-label" for="title">{tr}Title{/tr}</label>
				<div class="col-sm-7">
					<input type="text" name="title" id="title" value="{$info.title|escape}" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="active">{tr}Active{/tr}</label>
				<div class="col-sm-7">
					<select name="active" id="active" class="form-control">
						<option value='a' {if $info.active eq 'a'}selected="selected"{/if}>{tr}active{/tr}</option>
						<option value='c' {if $info.active eq 'c'}selected="selected"{/if}>{tr}current{/tr}</option>
						<option value='x' {if $info.active eq 'x'}selected="selected"{/if}>{tr}closed{/tr}</option>
						<option value='t' {if $info.active eq 't'}selected="selected"{/if} style="border-top:1px solid black;">{tr}template{/tr}</option>
						<option value='o' {if $info.active eq 'o'}selected="selected"{/if}>{tr}object{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Options{/tr}</label>
				<div class="col-sm-7">
					<a href="javascript://toggle quick options" onclick="pollsToggleQuickOptions()">{tr}Toggle Quick Options{/tr}</a>
				</div>
			</div>
			<div class="form-group" id="tikiPollsQuickOptions" style="display: none">
				<div id="tikiPollsOptions">
					{section name=opt loop=$options}
					<div>
						<input type="hidden" name="optionsId[]" value="{$options[opt].optionId}">
						<input type="text" name="options[]" value="{$options[opt].title}">
					</div>
					{/section}
					<div class="col-sm-7 col-sm-offset-3 margin-bottom-sm">
						<input type="text" name="options[]" class="form-control">
					</div>
					<a href="javascript://Add Option"	onclick="pollsAddOption()">{tr}Add Option{/tr}</a>
				</div>
				<div class="col-sm-7 col-sm-offset-3">
					{remarksbox type="tip" title="{tr}Tip{/tr}"}
						{tr}Leave box empty to delete an option.{/tr}
					{/remarksbox}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Publish Date{/tr}</label>
				<div class="col-sm-7">
					{html_select_date time=$info.publishDate end_year="+1" field_order=$prefs.display_field_order} {tr}at{/tr}
					{html_select_time time=$info.publishDate display_seconds=false use_24_hours=$use_24hr_clock}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Votes older than x days are not considered{/tr}</label>
				<div class="col-sm-7">
					<input type="text" id="voteConsiderationSpan" name="voteConsiderationSpan" size="5" value="{$info.voteConsiderationSpan|escape}" class="form-control">
					<div class="small-hint">
						{tr}0 for no limit{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				{include file='categorize.tpl'}
			</div>
		    <div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1">
				    <input type="submit" class="btn btn-default btn-sm" name="add" value="{tr}Add{/tr}">
			    </div>
		    </div>
		</form>
	{/tab}

	{tab name="{tr}Polls{/tr}"}
		<h2>{tr}Polls{/tr}</h2>
		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<div class="{if $js === 'y'}table-responsive{/if} poll-table"> {* table-responsive class cuts off css drop-down menus *}
			<table class="table table-striped table-hover">
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
					<th></th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="id">{$channels[user].pollId}</td>
						<td class="text">
							<a class="tablename" href="tiki-poll_results.php?pollId={$channels[user].pollId}">{$channels[user].title|escape}</a>
						</td>
						{if $prefs.poll_list_categories eq 'y'}
							<td class="text">
								{section name=cat loop=$channels[user].categories}
									{$channels[user].categories[cat].name}
									{if !$smarty.section.cat.last}
										<br>
									{/if}
								{/section}
							</td>
						{/if}
						{if $prefs.poll_list_objects eq 'y'}
							<td class="text">
								{section name=obj loop=$channels[user].objects}
									<a href="{$channels[user].objects[obj].href}">{$channels[user].objects[obj].name}</a>
									{if !$smarty.section.obj.last}
										<br>
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
							{capture name=admin_poll_actions}
								{strip}
									{$libeg}<a href="tiki-admin_poll_options.php?pollId={$channels[user].pollId}">
										{icon name='list' _menu_text='y' _menu_icon='y' alt="{tr}Options{/tr}"}
									</a>{$liend}
									{$libeg}<a class="link" href="tiki-poll_results.php?pollId={$channels[user].pollId}">
										{icon name="chart" _menu_text='y' _menu_icon='y' alt="{tr}Results{/tr}"}
									</a>{$liend}
									{$libeg}{self_link pollId=$channels[user].pollId _menu_text='y' _menu_icon='y' _icon_name="edit"}
										{tr}Edit{/tr}
									{/self_link}{$liend}
									{$libeg}<a href="tiki-admin_polls.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].pollId}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.admin_poll_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.admin_poll_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=$numbercol}
				{/section}
			</table>
		</div>
		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Add poll to pages{/tr}"}
		<h2>{tr}Add poll to pages{/tr}</h2>
		<form action="tiki-admin_polls.php" method="post" class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Poll{/tr}</label>
				<div class="col-sm-7">
					<select name="poll_template" class="form-control">
						{section name=ix loop=$channels}
							{if $channels[ix].active eq 't'}
								<option value="{$channels[ix].pollId|escape}"{if $smarty.section.ix.first} selected="selected"{/if}>{tr}{$channels[ix].title}{/tr}</option>
							{/if}
						{/section}
					</select></br>
					{remarksbox type="tip" title="Tip"}{tr}This menu shows only Polls with 'status': "template"{/tr}{/remarksbox}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Title{/tr}</label>
				<div class="col-sm-7">
					<input type="text" name="poll_title" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Wiki pages{/tr}</label>
				<div class="col-sm-7">
					<select name="pages[]" multiple="multiple" class="form-control">
						{section name=ix loop=$listPages}
							<option value="{$listPages[ix].pageName|escape}">{tr}{$listPages[ix].pageName|escape}{/tr}</option>
						{/section}
					</select></br>
					{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use Ctrl+Click to select multiple options{/tr}{/remarksbox}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">{tr}Lock the pages{/tr}</label>
				<div class="col-sm-7">
					<input type="checkbox" name="locked">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-7 col-sm-offset-1 margin-bottom-sm">
					<input type="submit" class="btn btn-default btn-sm" name="addPoll" value="{tr}Add{/tr}">
				</div>
			</div>
		</form>
	{/tab}
{/tabset}
