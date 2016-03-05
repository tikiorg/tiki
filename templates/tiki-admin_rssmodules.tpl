{title help="Feeds"}{tr}External Feeds{/tr}{/title}

{remarksbox type="tip" title="{tr}Tips{/tr}"}
	{tr}This page is to configure settings of external feeds read/imported by Tiki. To generate/export feeds, look for "Feeds" on the admin panel, or{/tr}
	<a class="alert-link" href="tiki-admin.php?page=rss">{tr}Click Here{/tr}</a>.
	<hr/>
	{tr}To use feeds in a text area (Wiki page, etc), a <a class="alert-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{rss id=x}{/literal}, where x is the ID of the feed.{/tr}
	{tr}To use them to generate articles, use the <a class="alert-link" href="https://doc.tiki.org/Article+generator" target="_blank">Article generator <img src="img/icons/newspaper_go.png"></a> for that specific feed{/tr}.
{/remarksbox}

{if $preview eq 'y'}
	{remarksbox type="info" title="{tr}Content for the feed{/tr}"}
		{if $feedtitle ne ''}
			<h3>{$feedtitle.title|escape}</h3>
		{/if}
		<ul>
			{section name=ix loop=$items}
				<li><a href="{$items[ix].url|escape}" class="link">{$items[ix].title|escape}</a>{if $items[ix].pubDate ne ""}<br><span class="rssdate">({$items[ix].pubDate|escape})</span>{/if}</li>
			{/section}
		</ul>
	{/remarksbox}
{/if}

{tabset name="admin_rssmodules"}

	{tab name="{tr}External Feeds{/tr}"}
		<h2>{tr}External Feeds{/tr}</h2>
		<div align="center">
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
			<table class="table table-striped table-hover">
				<tr>
					<th>{self_link _sort_arg='sort_mode' _sort_field='rssId'}{tr}ID{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='lastUpdated'}{tr}Last update{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='showTitle'}{tr}Show Title{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='showPubDate'}{tr}Show Date{/tr}{/self_link}</th>
					<th></th>
				</tr>
				{section name=chan loop=$channels}
					<tr>
						<td class="id">{$channels[chan].rssId|escape}</td>
						<td class="text">
							{$channels[chan].name|escape}
							<span class="help-block">
								{if $channels[chan].description}{$channels[chan].description|escape|nl2br}<br>{/if}
								{tr}Site:{/tr} <a href="{$channels[chan].siteurl|escape}">{$channels[chan].sitetitle|escape}</a><br>
								{tr}Feed:{/tr} <a class="link" href="{$channels[chan].url|escape}">{$channels[chan].url|truncate:50:"...":true}</a>
							</span>
						</td>
						<td class="text">
							{if $channels[chan].lastUpdated eq '1000000'}{tr}Never{/tr}{else}{$channels[chan].lastUpdated|tiki_short_datetime}{/if}
							<span class="help-block">{tr}Refresh rate:{/tr} {$channels[chan].refresh|duration}</span>
						</td>
						<td class="text">{$channels[chan].showTitle|escape}</td>
						<td class="text">{$channels[chan].showPubDate|escape}</td>
						<td class="action">
							{capture name=rss_actions}
								{strip}
									{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;view={$channels[chan].rssId|escape}">
										{icon name="rss" _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;refresh={$channels[chan].rssId|escape}">
										{icon name="refresh" _menu_text='y' _menu_icon='y' alt="{tr}Refresh{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;rssId={$channels[chan].rssId|escape}">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
									{if $prefs.feature_articles eq 'y'}
										{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;article={$channels[chan].rssId|escape}">
											{icon name='textfile' _menu_text='y' _menu_icon='y' alt="{tr}Article generator{/tr}"}
										</a>{$liend}
									{/if}
									{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;clear={$channels[chan].rssId|escape}">
										{icon name='trash' _menu_text='y' _menu_icon='y' alt="{tr}Clear Cache{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-admin_rssmodules.php?offset={$offset|escape}&amp;sort_mode={$sort_mode|escape}&amp;remove={$channels[chan].rssId|escape}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.rss_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.rss_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=6}
				{/section}
			</table>

			{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

		</div>
	{/tab}

	{if $rssId > 0}
		{assign var="feedEditLabel" value="{tr}Edit Feed{/tr}"}
	{else}
		{assign var="feedEditLabel" value="{tr}Create Feed{/tr}"}
	{/if}
	{tab name=$feedEditLabel}
		<h2>{$feedEditLabel}
		{if $rssId > 0}
			{$name|escape}</h2>
			{button href="tiki-admin_rssmodules.php" cookietab="2" _keepall="y" _icon_name="create" _text="{tr}Create new external feed{/tr}"}
		{else}
			</h2>
		{/if}
		<form action="tiki-admin_rssmodules.php" method="post" class="form-horizontal">
			<input type="hidden" name="rssId" value="{$rssId|escape}">
			<div class="form-group">
				<label for="name" class="control-label col-sm-3">{tr}Name{/tr}</label>
				<div class="col-sm-9">
					<input type="text" name="name" value="{$name|escape}" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="url" class="control-label col-sm-3">{tr}URL{/tr}</label>
				<div class="col-sm-9">
					<input type="url" name="url" value="{$url|escape}" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="description" class="control-label col-sm-3">{tr}Description{/tr}</label>
				<div class="col-sm-9">
					<textarea name="description" rows="4" class="form-control">{$description|escape}</textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="refresh" class="control-label col-sm-3">{tr}Refresh rate{/tr}</label>
				<div class="col-sm-9">
					<select class="form-control" name="refresh">
						{foreach [1, 5, 10, 15, 20, 30, 45, 60, 90, 120, 360, 720, 1440] as $min}
							<option value="{$min|escape}" {if $refresh eq ($min*60)}selected="selected"{/if}>{($min*60)|duration}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-9 col-sm-offset-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="showTitle" {if $showTitle eq 'y'}checked="checked"{/if}>
							{tr}Show feed title{/tr}
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-9 col-sm-offset-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="showPubDate" {if $showPubDate eq 'y'}checked="checked"{/if}>
							{tr}Show publish date{/tr}
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-9 col-sm-offset-3">
					<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
				</div>
			</div>
		</form>
	{/tab}

	{if $articleConfig}
		{tab name="{tr}Article Generator{/tr}"}
			<h2>{tr _0='"'|cat:$articleConfig.feed_name|cat:'"'|escape}Article Generator for %0{/tr}</h2>
			{remarksbox type="tip" title="{tr}Tips{/tr}"}
					{tr}Once you have defined the settings below, each new item in this rss feed will generate a new article{/tr}.
					<a target="tikihelp" href="https://doc.tiki.org/Article+generator" class="tikihelp" style="float:none" title="{tr}Article Generator:{/tr}
						{tr}From the point when you defined the settings onwards, new items in the feed become articles each time the feed is refreshed. But only new ones.{/tr}">
						<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
					</a>
					<hr>
					{tr}You can enable <strong>Show source</strong> for the <a href="tiki-article_types.php" target="_blank">article type</a> (hidden by default), to allow users to read the full content{/tr}.
			{/remarksbox}

			<form method="post" action="">
				<p>
					<input id="article_active" type="checkbox" name="enable" value="1"{if $articleConfig.active} checked="checked"{/if}>
					<label for="article_active">{tr}Enable{/tr}</label>
				</p>
				{if $prefs.feature_submissions eq 'y'}
					<p>
						<input id="article_submission" type="checkbox" name="submission" value="1"{if $articleConfig.submission} checked="checked"{/if}>
						<label for="article_submission">{tr}Use Article Submission System{/tr}</label>
					</p>
				{/if}
				<p>
					<label for="article_expiry">{tr}Expiration{/tr}</label>
					<input type="text" name="expiry" id="article_expiry" value="{$articleConfig.expiry|escape}" size="3"> {tr}days{/tr}
				</p>
				<p>
					<label for="article_future_publish">{tr}Publish in the future{/tr}</label>
					<input type="text" name="future_publish" id="article_future_publish" value="{$articleConfig.future_publish|escape}" size="4"> {tr}minutes{/tr} ({tr}-1 to use original publishing date from the feed{/tr})
				</p>
				<h3>{tr}Default Settings{/tr}</h3>
				<p>
					<label for="article_type">{tr}Type{/tr}</label>
					<select name="type" id="article_type">
						{foreach from=$types item=t}
							<option value="{$t.type|escape}"{if $t.type eq $articleConfig.atype} selected="selected"{/if}>{$t.type|escape}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label for="article_topic">{tr}Topic{/tr}</label>
					<select name="topic" id="article_topic">
						<option value="0">{tr}None{/tr}
						{foreach from=$topics item=t}
							<option value="{$t.topicId|escape}"{if $t.topicId eq $articleConfig.topic} selected="selected"{/if}>{$t.name|escape}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label for="article_rating">{tr}Rating{/tr}</label>
					<select name="rating" id="article_rating">
						{foreach from=$ratingOptions item=v}
							<option{if $v eq $articleConfig.rating} selected="selected"{/if}>{$v|escape}</option>
						{/foreach}
					</select>
				</p>
				<h3>{tr}Custom Settings for Source Categories{/tr}</h3>
				{if !$sourcecats}
					<p>{tr}No source categories detected for this feed{/tr}</p>
				{/if}
				<table>
					<tr>
						<th>{tr}Source Category{/tr}
						<th>{tr}Type{/tr}</th>
						<th>{tr}Topic{/tr}</th>
						<th>{tr}Rating{/tr}</th>
						<th>{tr}Priority (10 is highest){/tr}</th>
					</tr>
					{foreach $sourcecats as $sourcecat => $settings}
						<tr>
							<td>
								{$sourcecat|escape}
							</td>
							<td>
								<select name="custom_atype[{$sourcecat|escape}]">
									<option value="">{tr}Default{/tr}</option>
									{foreach from=$types item=t}
										<option value="{$t.type|escape}"{if $t.type eq $article_custom_info[$sourcecat].atype} selected="selected"{/if}>{$t.type|escape}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<select name="custom_topic[{$sourcecat|escape}]">
									<option value="">{tr}Default{/tr}</option>
									<option value="0" {if $article_custom_info[$sourcecat].topic === "0"} selected="selected"{/if}>{tr}None{/tr}</option>
									{foreach from=$topics item=t}
										<option value="{$t.topicId|escape}"{if $t.topicId eq $article_custom_info[$sourcecat].topic} selected="selected"{/if}>{$t.name|escape}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<select name="custom_rating[{$sourcecat|escape}]">
									<option value="">{tr}Default{/tr}</option>
									{foreach from=$ratingOptions item=v}
										<option value="{$v|escape}"{if $v === $article_custom_info[$sourcecat].rating} selected="selected"{/if}>{$v|escape}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<select name="custom_priority[{$sourcecat|escape}]">
									{foreach from=$ratingOptions item=v}
										<option value="{$v|escape}"{if $v === $article_custom_info[$sourcecat].priority} selected="selected"{/if}>{$v|escape}</option>
									{/foreach}
								</select>
							</td>
						</tr>
					{/foreach}
				</table>

				<h3>{tr}Categorize Created Articles{/tr}</h3>
				<p>
					{include file='categorize.tpl'}
				</p>
				<p>
					<input type="submit" class="btn btn-default btn-sm" value="{tr}Configure{/tr}">
				</p>
			</form>
		{/tab}
	{/if}
{/tabset}
