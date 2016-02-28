{* $Id$ *}
{if !$tsAjax}
	{block name=title}
		{title help="forums" admpage="forums"}{tr}Forums{/tr}{/title}
	{/block}
	<div class="t_navbar margin-bottom-md">
		{if $tiki_p_admin_forum eq 'y'}
			{button href="tiki-admin_forums.php" _type="link" class="btn btn-link" _icon_name="wrench" _text="{tr}Admin{/tr}"}
		{/if}
		{if $tiki_p_forum_read eq 'y' and $prefs.feature_forum_rankings eq 'y'}
			{button href="tiki-forum_rankings.php" _type="link" class="btn btn-link" _icon_name="ranking" _text="{tr}Rankings{/tr}"}
		{/if}
	</div>
	{if !$tsOn}
		{if $channels or ($find ne '')}
			{if $prefs.feature_forums_search eq 'y' or $prefs.feature_forums_name_search eq 'y'}
				{if $prefs.feature_forums_name_search eq 'y'}
					<form method="get" class="form" role="form" action="tiki-forums.php">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									{icon name="search"}
								</span>
								<input type="text" name="find" class="form-control" value="{$find|escape}" placeholder="{tr}Find{/tr}...">
								<div class="input-group-btn">
									<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
									<input type="submit" class="btn btn-default" value="{tr}Search by name{/tr}" name="search">
								</div>
							</div>
						</div>
					</form>
				{/if}
				{if $prefs.feature_forums_search eq 'y' and $prefs.feature_search eq 'y'}
                    <div class="row margin-bottom-md">
                    <div class="col-md-5 col-md-offset-7">
					<form class="form" method="get" role="form" action="{if $prefs.feature_search_fulltext neq 'y'}tiki-searchindex.php{else}tiki-searchresults.php{/if}">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									{icon name="search"}
								</span>
								<input name="highlight" type="text" class="form-control" placeholder="{tr}Find{/tr}...">
								<div class="input-group-btn">
									<input type="hidden" name="where" value="forums">
									<input type="hidden" name="filter~type" value="forum post">
									<input type="submit" class="wikiaction btn btn-default" name="search" value="{tr}Search in content{/tr}">
								</div>
							</div>
						</div>
					</form>
                    </div></div>
				{/if}
			{/if}
		{/if}
	{/if}
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
<div id="{$ts_tableid}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $tsOn}style="visibility:hidden;"{/if}> {*the table-responsive class cuts off dropdown menus *}
	<table id="{$ts_tableid}" class="table table-striped table-hover table-forum normal" data-count="{$cant|escape}">
		{block name=forum-header}
		<thead>
			<tr>
				{$numbercol = 1}
				<th id="name">{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>

				{if $prefs.forum_list_topics eq 'y'}
					{$numbercol = $numbercol + 1}
					<th id="threads" class="text-right">{self_link _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}</th>
				{/if}

				{if $prefs.forum_list_posts eq 'y'}
					{$numbercol = $numbercol + 1}
					<th id="comments" class="text-right">{self_link _sort_arg='sort_mode' _sort_field='comments'}{tr}Posts{/tr}{/self_link}</th>
				{/if}

				{if $prefs.forum_list_ppd eq 'y'}
					{$numbercol = $numbercol + 1}
					<th id="ppd">{tr}PPD{/tr}</th>
				{/if}

				{if $prefs.forum_list_lastpost eq 'y'}
					{$numbercol = $numbercol + 1}
					<th id="lastPost">{self_link _sort_arg='sort_mode' _sort_field='lastPost'}{tr}Last Post{/tr}{/self_link}</th>
				{/if}

				{if $prefs.forum_list_visits eq 'y'}
					{$numbercol = $numbercol + 1}
					<th id="hits" class="text-right">{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Visits{/tr}{/self_link}</th>
				{/if}
				{$numbercol = $numbercol + 1}
				<th id="actions"></th>
			</tr>
		</thead>
		{/block}
		<tbody>
			{assign var=section_old value=""}
			{section name=user loop=$channels}
				{assign var=section value=$channels[user].section}
				{if $section ne $section_old}
					{assign var=section_old value=$section}
					<td class="third info" colspan="{$numbercol}">{$section|escape}</td>
				{/if}
				{block name=forum-row}
				<tr>
					<td class="text">
						{if (isset($channels[user].individual) and $channels[user].individual eq 'n')
							or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
							<a class="forumname" href="{$channels[user].forumId|sefurl:'forum'}">{$channels[user].name|addongroupname|escape}</a>
						{else}
							{$channels[user].name|addongroupname|escape}
						{/if}
						{if $prefs.forum_list_desc eq 'y'}
							<div class="help-block">
								{capture name="parsedDesc"}{wiki}{$channels[user].description}{/wiki}{/capture}
								{if strlen($smarty.capture.parsedDesc) < $prefs.forum_list_description_len}
									{$smarty.capture.parsedDesc}
								{else}
									{$smarty.capture.parsedDesc|strip_tags|truncate:$prefs.forum_list_description_len:"...":true}
								{/if}
							</div>
						{/if}
					</td>
					{if $prefs.forum_list_topics eq 'y'}
						<td class="integer">{$channels[user].threads}</td>
					{/if}
					{if $prefs.forum_list_posts eq 'y'}
						<td class="integer">{$channels[user].comments}</td>
					{/if}
					{if $prefs.forum_list_ppd eq 'y'}
						<td class="integer">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
					{/if}
					{if $prefs.forum_list_lastpost eq 'y'}
						<td class="text">
							{if isset($channels[user].lastPost)}
								{$channels[user].lastPost|tiki_short_datetime}<br>
								{if $prefs.forum_reply_notitle neq 'y'}<small><i>{$channels[user].lastPostData.title|escape}</i>{/if}
								{tr}by{/tr} {$channels[user].lastPostData.userName|username}</small>
							{/if}
						</td>
					{/if}
					{if $prefs.forum_list_visits eq 'y'}
						<td class="integer">{$channels[user].hits}</td>
					{/if}
					<td class="action">
						{capture name=forum_actions}
							{strip}
								{$libeg}<a href="{$channels[user].forumId|sefurl:'forum'}">
									{icon name="view" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>{$liend}
								{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
									{$libeg}<a href="tiki-admin_forums.php?forumId={$channels[user].forumId}&amp;cookietab=2#content_admin_forums1-2">
										{icon name="edit" _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
								{/if}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.forum_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.forum_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
				{/block}
			{sectionelse}
				{if !$tsOn || ($tsOn && $tsAjax)}
					{norecords _colspan=$numbercol _text="{tr}No forums found{/tr}"}
				{else}
					{norecords _colspan=$numbercol _text="{tr}Loading{/tr}..."}
				{/if}
			{/section}
		</tbody>
	</table>
</div>
{if !$tsOn}
	{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/if}
