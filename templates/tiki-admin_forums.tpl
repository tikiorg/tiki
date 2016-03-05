{* $Id$ *}
{if !$tsAjax}
	{title help="Forums" admpage="forums" url='tiki-admin_forums.php'}{tr}Admin Forums{/tr}{/title}

	<div class="t_navbar margin-bottom-md">
		{if $tiki_p_admin_forum eq 'y' && $forumId > 0 or (isset($dup_mode) and $dup_mode eq 'y')}
			{button class="btn btn-default" href="?" _icon_name="add" _text="{tr}Create Forum{/tr}"}
		{/if}
		{if $tiki_p_admin_forum eq 'y' && (!isset($dup_mode) or $dup_mode ne 'y')}
			{button class="btn btn-default" href="tiki-admin_forums.php?dup_mode=y" _icon_name="copy" _text="{tr}Duplicate{/tr}"}
		{/if}
		{if $forumId > 0}
			{button _type="link" class="btn btn-link" href="tiki-view_forum.php?forumId=$forumId" _icon_name="view" _text="{tr}View{/tr}"}
		{/if}
		{if $tiki_p_admin_forum eq 'y'}
			{button _type="link" class="btn btn-link" href="tiki-forum_import.php" _icon_name="import" _text="{tr}Import{/tr}"}
		{/if}
		{if $tiki_p_forum_read eq 'y'}
			{button _type="link" class="btn btn-link" href="tiki-forums.php" _icon_name="list" _text="{tr}List{/tr}"}
		{/if}
	</div>
{/if}
{include file='utilities/feedback.tpl'}
{tabset}

	{tab name="{tr}Forums{/tr}"}
		<h2>{tr}Forums{/tr}</h2>

		{if ($channels or ($find ne '')) && !$tsOn}
			{include file='find.tpl'}
		{/if}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<form method='post' id="admin_forums">
			<div id="{$ts_tableid}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $tsOn}style="visibility:hidden;"{/if}>
				<table  id="{$ts_tableid}" class="table table-striped table-hover" data-count="{$cant|escape}">
					{$numbercol = 0}
					<thead>
						<tr>
							{if $channels}
								{$numbercol = $numbercol+1}
								<th id="checkbox" style="text-align:center">
									{select_all checkbox_names='checked[]'}
								</th>
							{/if}
							<th id="name">
								{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}
								{$numbercol = $numbercol+1}
							</th>
							<th id="threads">
								{self_link _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}
								{$numbercol = $numbercol+1}
							</th>
							<th id="comments">
								{self_link _sort_arg='sort_mode' _sort_field='comments'}{tr}Comments{/tr}{/self_link}
								{$numbercol = $numbercol+1}
							</th>
							<th id="users">{tr}Users{/tr}</th>
							{$numbercol = $numbercol+1}
							<th id="age">{tr}Age{/tr}</th>
							{$numbercol = $numbercol+1}
							<th id="ppd">{tr}PPD{/tr}</th>
							{$numbercol = $numbercol+1}
							<th id="hits">
								{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Hits{/tr}{/self_link}
								{$numbercol = $numbercol+1}
							</th>
							<th id="actions"></th>
							{$numbercol = $numbercol+1}
						</tr>
					</thead>
					<tbody>
						{section name=user loop=$channels}
							<tr>
								<td style="text-align:center">
									<input type="checkbox" name="checked[]" value="{$channels[user].forumId|escape}" {if isset($smarty.request.checked) and $smarty.request.checked and in_array($channels[user].forumId,$smarty.request.checked)}checked="checked"{/if}>
								</td>
								<td>
									<a class="link" href="{$channels[user].forumId|sefurl:'forum'}" title="{tr}View{/tr}">{$channels[user].name|escape}</a>
								</td>
								<td class="integer"><span class="badge">{$channels[user].threads}<span></td>
								<td class="integer"><span class="badge">{$channels[user].comments}<span></td>
								<td class="integer"><span class="badge">{$channels[user].users}<span></td>
								<td class="integer"><span class="badge">{$channels[user].age}<span></td>
								<td class="integer"><span class="badge">{$channels[user].posts_per_day|string_format:"%.2f"}<span></td>
								<td class="integer"><span class="badge">{$channels[user].hits}<span></td>
								<td class="action">
									{capture name=admin_forum_actions}
										{strip}
											{$libeg}<a href="{$channels[user].forumId|sefurl:'forum'}">
												{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
											</a>{$liend}
											{* the tiki_p_forum_lock permission has not been implemented *}
											{if isset($tiki_p_forum_lock) and $tiki_p_forum_lock eq 'y'}
												{if $channels[user].is_locked eq 'y'}
													{$libeg}{self_link _icon_name='unlock' _menu_text='y' _menu_icon='y' lock='n' forumId=$channels[user].forumId}
														{tr}Unlock{/tr}
													{/self_link}{$liend}
												{else}
													{$libeg}{self_link _icon_name='lock' _menu_text='y' _menu_icon='y' lock='y' forumId=$channels[user].forumId}
														{tr}Lock{/tr}
													{/self_link}{$liend}
												{/if}
											{/if}

											{if ($tiki_p_admin eq 'y')
											or ((isset($channels[user].individual) and $channels[user].individual eq 'n')
											and ($tiki_p_admin_forum eq 'y'))
											or ($channels[user].individual_tiki_p_admin_forum eq 'y')
											}
												{$libeg}{self_link _icon_name='edit' _menu_text='y' _menu_icon='y' cookietab='2' _anchor='anchor2' forumId=$channels[user].forumId}
													{tr}Edit{/tr}
												{/self_link}{$liend}
												{$libeg}{permission_link mode=text type=forum permType=forums id=$channels[user].forumId title=$channels[user].name}{$liend}
												{* go ahead and set action to delete_forum since that is the only action available in the multi selct dropdown *}
												{$libeg}<a href="{bootstrap_modal controller=forum action=delete_forum checked={$channels[user].forumId}}">
													{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
												</a>{$liend}
											{/if}
										{/strip}
									{/capture}
									{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
									<a
										class="tips"
										title="{tr}Actions{/tr}"
										href="#"
										{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.admin_forum_actions|escape:"javascript"|escape:"html"}{/if}
										style="padding:0; margin:0; border:0"
									>
										{icon name='wrench'}
									</a>
									{if $js === 'n'}
										<ul class="dropdown-menu" role="menu">{$smarty.capture.admin_forum_actions}</ul></li></ul>
									{/if}
								</td>
							</tr>
						{sectionelse}
							{if !$tsOn || ($tsOn && $tsAjax)}
								{norecords _colspan=$numbercol _text="No forums found"}
							{else}
								{norecords _colspan=$numbercol _text="Retrieving forums..."}
							{/if}
						{/section}
					</tbody>
				</table>
			</div>
			{if !$tsAjax}
				{if $channels}
					<div class="text-left form-group">
						<br>
						<label for="action" class="col-lg"></label>
						<div class="col-sm-6 input-group">
							<select name="action" class="form-control" onchange="show('groups');">
								<option value="no_action">
									{tr}Select action to perform with checked{/tr}...
								</option>
								{if $tiki_p_admin_forum eq 'y'}
									<option value="delete_forum">{tr}Delete{/tr}</option>
								{/if}
							</select>
							<span class="input-group-btn">
							<button
								type="submit"
								form='admin_forums'
								formaction="{bootstrap_modal controller=forum}"
								class="btn btn-primary confirm-submit"
							>
								{tr}OK{/tr}
							</button>
						</span>
						</div>
					</div>
				{/if}
			{/if}
		</form>
	{if !$tsOn}
		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/if}
	{/tab}
	{if !$tsAjax}
		{tab name="{tr}Create/Edit Forums{/tr}"}

			{if !isset($dup_mode) or $dup_mode != 'y'}
				{if $forumId > 0}
					<h2>{tr}Edit this Forum:{/tr} {$name|escape}</h2>
					{include file='object_perms_summary.tpl' objectName=$name objectType='forum' objectId=$forumId permType=$permsType}
				{else}
					<h2>{tr}Create New Forum{/tr}</h2>
				{/if}

				<form action="tiki-admin_forums.php" method="post" class="form-horizontal" role="form">
					<input type="hidden" name="forumId" value="{$forumId|escape}">
					<fieldset>
						<legend>{tr}Main details{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="name">{tr}Name{/tr}</label>
							<div class="col-sm-8">
								<input type="text" name="name" class="form-control" id="name" value="{$name|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="name">{tr}Description{/tr}</label>
							<div class="col-sm-8">
								<textarea name="description" rows="4" class="form-control" id="description">{$description|escape}</textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="section">{tr}Section{/tr}</label>
							<div class="col-sm-4">
								<select name="section" id="section" class="form-control">
									<option value="" {if $forumSection eq ""}selected="selected"{/if}>{tr}None{/tr}</option>
									<option value="__new__">{tr}Create new{/tr}</option>
									{section name=ix loop=$sections}
										<option {if $forumSection eq $sections[ix]}selected="selected"{/if} value="{$sections[ix]|escape}">{$sections[ix]|escape}</option>
									{/section}
								</select>
							</div>
							<div class="col-sm-4">
								<input name="new_section" class="form-control" type="text">
							</div>
						</div>

						{include file='categorize.tpl' labelcol='4' inputcol='8'}
						{if $prefs.feature_multilingual eq 'y'}
							<div class="form-group">
								<label class="col-sm-4 control-label" for="forumLanguage">{tr}Language{/tr}</label>
								<div class="col-sm-8">
									<select name="forumLanguage" id="forumLanguage" class="checkbox-inline">
										<option value="">{tr}Unknown{/tr}</option>
										{section name=ix loop=$languages}
											<option value="{$languages[ix].value|escape}"{if $forumLanguage eq $languages[ix].value or (empty($data.page_id) and $forumLanguage eq '' and $languages[ix].value eq $prefs.language)} selected="selected"{/if}>{$languages[ix].name}</option>
										{/section}
									</select>
								</div>
							</div>
						{/if}
						{if $prefs.feature_file_galleries eq 'y' && $prefs.forum_image_file_gallery}
							<div class="form-group">
								<label class="col-sm-4 control-label" for="image">{tr}Image{/tr}</label>
								<div class="col-sm-8">
									{file_selector name="image" value=$image type="image/*" galleryId=$prefs.forum_image_file_gallery}
									<div class="help-block">
										{tr}Image symbolizing the forum.{/tr}
									</div>
								</div>
							</div>
						{/if}
						<div class="form-group">
							<label class="col-sm-4 control-label" for="is_flat">{tr}Only allow replies to the first message (flat forum){/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="is_flat" id="is_flat" {if $is_flat eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="moderator_user">{tr}Moderator user{/tr}</label>
							<div class="col-sm-8">
								<input id="moderator_user" class="form-control" type="text" name="moderator" value="{$moderator|escape}">
								{autocomplete element='#moderator_user' type='username'}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="moderator_group">{tr}Moderator group{/tr}</label>
							<div class="col-sm-8">
								<input id="moderator_group" type="text" class="form-control" name="moderator_group" id="moderator_group" value="{$moderator_group|escape}">
								{autocomplete element='#moderator_group' type='groupname'}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="forum_use_password">{tr}Password protected{/tr}</label>
							<div class="col-sm-4">
								{html_options name=forum_use_password options=$forum_use_password_options selected=$forum_use_password class=form-control}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="forum_password">{tr}Forum password{/tr}</label>
							<div class="col-sm-8">
								<input type="text" name="forum_password" id="forum_password" class="form-control" value="{$forum_password|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="controlFlood">{tr}Prevent flooding{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="controlFlood" id="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="floodInterval">{tr}Minimum time between posts{/tr}</label>
							<div class="col-sm-4 checkbox-inline">
								{html_options name=floodInterval id=floodInterval class="form-control" options=$flood_options selected=$floodInterval}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="useMail"><input type="checkbox" name="useMail" id="useMail" {if $useMail eq 'y'}checked="checked"{/if}> {tr}Send this forums posts to this email{/tr} </label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="mail" value="{$mail|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="usePruneUnreplied">
								<input type="checkbox" name="usePruneUnreplied" id="usePruneUnreplied" {if $usePruneUnreplied eq 'y'}checked="checked"{/if}> {tr}Prune unreplied messages after{/tr}
							</label>
							<div class="col-sm-4 checkbox-inline">
								{html_options name=pruneUnrepliedAge options=$pruneUnrepliedAge_options selected=$pruneUnrepliedAge}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="usePruneOld">
								<input type="checkbox" name="usePruneOld" id="usePruneOld" {if $usePruneOld eq 'y'}checked="checked"{/if}> {tr}Prune old messages after{/tr}
							</label>
							<div class="col-sm-4 checkbox-inline">
								{html_options name=pruneMaxAge options=$pruneMaxAge_options selected=$pruneMaxAge}
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Forum-Mailing list synchronization{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="outbound_address">{tr}Forward messages to this forum to this email address, in a format that can be used for sending back to the inbound forum email address{/tr}</label>
							<div class="col-sm-8">
								<input type="text" name="outbound_address" id="outbound_address" class="form-control" value="{$outbound_address|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="outbound_mails_for_inbound_mails">{tr}Send mails even when the post is generated by inbound mail{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="outbound_mails_for_inbound_mails" id="outbound_mails_for_inbound_mails" {if $outbound_mails_for_inbound_mails eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="outbound_mails_reply_link">{tr}Append a reply link to outbound mails{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="outbound_mails_reply_link" id="outbound_mails_reply_link" {if $outbound_mails_reply_link eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="outbound_from">{tr}Originating email address for mails from this forum{/tr}</label>
							<div class="col-sm-8">
								<input type="text" name="outbound_from" id="outbound_from" class="form-control" value="{$outbound_from|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{tr}Add messages from this email to the forum{/tr}</label>
							<div class="col-sm-8">
								<div class="form-group">
									<label class="col-sm-4 control-label" for="inbound_pop_server">{tr}POP3 server{/tr}</label>
									<div class="col-sm-8">
										<input type="text" name="inbound_pop_server" id="inbound_pop_server" class="form-control" value="{$inbound_pop_server|escape}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="inbound_pop_user">{tr}User{/tr}</label>
									<div class="col-sm-8">
										<input type="text" name="inbound_pop_user" id="inbound_pop_user" class="form-control"value="{$inbound_pop_user|escape}" autocomplete="off">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label" for="inbound_pop_password">{tr}Password{/tr}</label>
									<div class="col-sm-8">
										<input type="password" name="inbound_pop_password" id="inbound_pop_password" class="form-control" value="{$inbound_pop_password|escape}" autocomplete="off">
									</div>
								</div>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Forums list{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="show_description">{tr}Show description{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="show_description" id="show_description" {if $show_description eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="forum_last_n">{tr}Display last post titles{/tr}</label>
							<div class="col-sm-4 checkbox-inline">
								{html_options name=forum_last_n id=forum_last_n options=$forum_last_n_options selected=$forum_last_n}
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Forum topics (threads) list{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="topicOrdering">{tr}Default ordering for topics{/tr}</label>
							<div class="col-sm-8 checkbox-inline">
								{html_options name=topicOrdering id=topicOrdering options=$topicOrdering_options selected=$topicOrdering}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="topicsPerPage">{tr}Topics per page{/tr}</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="topicsPerPage" id="topicsPerPage" value="{$topicsPerPage|escape}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{tr}Topic list configuration{/tr}</label>
							<div class="col-sm-8">
								<div class="checkbox">
									<label for="topics_list_replies">
										<input type="checkbox" name="topics_list_replies" id="topics_list_replies" {if $topics_list_replies eq 'y'}checked="checked"{/if}> {tr}Replies{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_reads">
										<input type="checkbox" name="topics_list_reads" id="topics_list_reads" {if $topics_list_reads eq 'y'}checked="checked"{/if}> {tr}Reads{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_pts">
										<input type="checkbox" name="topics_list_pts" id="topics_list_pts" {if $topics_list_pts eq 'y'}checked="checked"{/if}> {tr}Points{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_lastpost">
										<input type="checkbox" name="topics_list_lastpost" id="topics_list_lastpost" {if $topics_list_lastpost eq 'y'}checked="checked"{/if}> {tr}Last post{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_lastpost_title">
										<input type="checkbox" name="topics_list_lastpost_title" id="topics_list_lastpost_title" {if $topics_list_lastpost_title eq 'y'}checked="checked"{/if}> {tr}Last post title{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_lastpost_avatar">
										<input type="checkbox" name="topics_list_lastpost_avatar" id="topics_list_lastpost_avatar" {if $topics_list_lastpost_avatar eq 'y'}checked="checked"{/if}> {tr}Last post profile picture{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_author">
										<input type="checkbox" name="topics_list_author" id="topics_list_author" {if $topics_list_author eq 'y'}checked="checked"{/if}> {tr}Author{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="topics_list_author_avatar">
										<input type="checkbox" name="topics_list_author_avatar" id="topics_list_author_avatar" {if $topics_list_author_avatar eq 'y'}checked="checked"{/if}> {tr}Author profile picture{/tr}
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="topic_smileys">{tr}Use topic smileys{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="topic_smileys" id="topic_smileys" {if $topic_smileys eq 'y'}checked="checked"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="topic_summary">{tr}Show topic summary{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="topic_summary" id="topic_summary" {if $topic_summary eq 'y'}checked="checked"{/if}>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Forum threads{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="threadOrdering">{tr}Default ordering of threads{/tr}</label>
							<div class="col-sm-8">
								{html_options name=threadOrdering id=threadOrdering options=$threadOrdering_options selected=$threadOrdering}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="threadStyle">{tr}Default style of threads{/tr}</label>
							<div class="col-sm-8">
								{html_options name=threadStyle id=threadStyle options=$threadStyle_options selected=$threadStyle}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="commentsPerPage">{tr}Default number of comments per page{/tr}</label>
							<div class="col-sm-8">
								{html_options name=commentsPerPage id=commentsPerPage options=$commentsPerPage_options selected=$commentsPerPage}
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Posts{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="approval_type">{tr}Approval type{/tr}</label>
							<div class="col-sm-4 checkbox-inline">
								{html_options name=approval_type for=approval_type id=approval_type class=form-control options=$approval_options selected=$approval_type}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">{tr}User information display{/tr}</label>
							<div class="col-sm-8">
								<div class="checkbox">
									<label for="ui_avatar">
										<input type="checkbox" name="ui_avatar" id="ui_avatar" {if $ui_avatar eq 'y'}checked="checked"{/if}> {tr}Profile picture{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_rating_choice_topic">
										<input type="checkbox" name="ui_rating_choice_topic" id="ui_rating_choice_topic" {if $ui_rating_choice_topic eq 'y'}checked="checked"{/if}> {tr}Topic Rating{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_flag">
										<input type="checkbox" name="ui_flag" id="ui_flag" {if $ui_flag eq 'y'}checked="checked"{/if}> {tr}Flag{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_posts">
										<input type="checkbox" name="ui_posts" id="ui_posts" {if $ui_posts eq 'y'}checked="checked"{/if}> {tr}Posts{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_level">
										<input type="checkbox" name="ui_level" id="ui_level" {if $ui_level eq 'y'}checked="checked"{/if}> {tr}User Level{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_email">
										<input type="checkbox" name="ui_email" id="ui_email" {if $ui_email eq 'y'}checked="checked"{/if}> {tr}eMail{/tr}
									</label>
								</div>
								<div class="checkbox">
									<label for="ui_online">
										<input type="checkbox" name="ui_online" id="ui_online" {if $ui_online eq 'y'}checked="checked"{/if}> {tr}Online{/tr}
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="vote_threads">{tr}Posts can be rated{/tr}</label>
							<div class="col-sm-8">
								<input type="checkbox" name="vote_threads" id="vote_threads" {if $vote_threads eq 'y'}checked="checked"{/if}>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Attachments{/tr}</legend>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="att">{tr}Permission{/tr}</label>
							<div class="col-sm-8 checkbox-inline">
								{html_options name=att id=att options=$attachment_options selected=$att}
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 control-label" for="att_store_db">{tr}Store attachments in{/tr}</label>
							<div class="col-sm-2 checkbox-inline">
								<input type="radio" name="att_store" id="att_store_db" value="db" {if $att_store eq 'db'}checked="checked"{/if}> {tr}Database{/tr}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2 col-sm-offset-4 checkbox-inline">
								<input type="radio" name="att_store" value="dir" {if $att_store eq 'dir'}checked="checked"{/if}> {tr}File system{/tr}</div>
							<label class="control-label col-sm-2" for="att_store_dir">{tr}Path{/tr}</label>
							<div class="col-sm-4 checkbox-inline">
								<input type="text" name="att_store_dir" id="att_store_dir" value="{$att_store_dir|escape}" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4" for="att_max_size">{tr}Max attachment size (bytes){/tr}</label>
							<div class="col-sm-8">
								<input type="text" name="att_max_size" id="att_max_size" class="form-control" value="{$att_max_size|escape}">
								<span class="help-block">{tr}Max:{/tr} {$maxAttachSize|escape} ({$maxAttachSize|kbsize})</span>
							</div>
						</div>
						<div class="form-group">
							<label class=" col-sm-4 control-label" for="att_list_nb">{tr}Shows number of attachments of the all thread in forum list{/tr}</label>
								<div class="col-sm-8">
									<input type="checkbox" class="checkbox-inline" id="att_list_nb" name="att_list_nb"{if $att_list_nb eq 'y'} checked="checked"{/if} id="att_list_nb">
								</div>
						</div>
					</fieldset>

					<div class="text-center">
						<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
					</div>
				</form>

			{else}{*duplicate*}
				<h2>{tr}Duplicate Forum{/tr}</h2>
				<form action="tiki-admin_forums.php" method="post" class="form-horizontal" role="form">
					<div class="form-group">
						<label class="col-sm-4 control-label" for="duplicate_name">{tr}Name{/tr}</label>
						<div class="col-sm-8">
							<input type="text" name="duplicate_name" id="duplicate_name" class="form-control" value="{$name|escape}">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="duplicate_description">{tr}Description{/tr}</label>
						<div class="col-sm-8">
							<textarea name="description" rows="4" id="duplicate_description" class="form-control">{$description|escape}</textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="duplicate_forumId">{tr}Forum{/tr}</label>
						<div class="col-sm-8">
							<select name="duplicate_forumId" id="duplicate_forumId" class="form-control">
								{section name=ix loop=$allForums}
									<option value="{$allForums[ix].forumId}">{$allForums[ix].name}</option>
								{/section}
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="duplicate_categories">{tr}Duplicate categories{/tr}</label>
						<div class="col-sm-8 checkbox-inline">
							<input type="checkbox" name="dupCateg" id="duplicate_categories">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="duplicate_perms">{tr}Duplicate permissions{/tr}</label>
						<div class="col-sm-8 checkbox-inline">
							<input type="checkbox" name="dupPerms" id="duplicate_perms">
						</div>
					</div>
					<div class="text-center">
						<input type="submit" class="btn btn-default" name="duplicate" value="{tr}Duplicate{/tr}">
					</div>
				</form>
			{/if}
		{/tab}
	{/if}
{/tabset}