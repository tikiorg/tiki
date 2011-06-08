{* $Id$ *}

{title help="Forums" admpage="forums"}{tr}Admin Forums{/tr}{/title}

<div class="navbar">
	{if $tiki_p_admin_forum eq 'y' && $forumId > 0 or $dup_mode eq 'y'}
		{button href="?" _text="{tr}Create New Forum{/tr}"}
	{/if}
	{if $tiki_p_admin_forum eq 'y' && $dup_mode ne 'y'}
		{button href="tiki-admin_forums.php?dup_mode=y" _text="{tr}Duplicate Forum{/tr}"}
	{/if}
	{if $forumId > 0}
		{button href="tiki-view_forum.php?forumId=$forumId" _text="{tr}View this forum{/tr}"}
	{/if}
	{if $tiki_p_admin eq 'y'}
		{button href="tiki-forum_import.php" _text="{tr}Import forums{/tr}"}
	{/if}
	{if $tiki_p_forum_read eq 'y'}
		{button href="tiki-forums.php" _text="{tr}List forums{/tr}"}
	{/if}
</div>

{tabset}

{tab name="{tr}Forums{/tr}"}

{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<form action="#">
	<table class="normal">
		{assign var=numbercol value=8}
		<tr>
			{if $channels}
				{assign var=numbercol value=$numbercol+1}
				<th style="text-align:center">
					{select_all checkbox_names='checked[]'}
				</th>
			{/if}
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}
			</th>
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='threads'}{tr}Topics{/tr}{/self_link}
			</th>
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='comments'}{tr}Coms{/tr}{/self_link}
			</th>
			<th>{tr}Users{/tr}</th>
			<th>{tr}Age{/tr}</th>
			<th>{tr}PPD{/tr}</th>
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='hits'}{tr}Hits{/tr}{/self_link}
			</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td style="text-align:center">
					<input type="checkbox" name="checked[]" value="{$channels[user].forumId|escape}" {if $smarty.request.checked and in_array($channels[user].forumId,$smarty.request.checked)}checked="checked"{/if} />
				</td>
				<td>
					<a class="link" href="tiki-view_forum.php?forumId={$channels[user].forumId}" title="{tr}View{/tr}">{$channels[user].name|escape}</a>
				</td>
				<td class="integer">{$channels[user].threads}</td>
				<td class="integer">{$channels[user].comments}</td>
				<td class="integer">{$channels[user].users}</td>
				<td class="integer">{$channels[user].age}</td>
				<td class="integer">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
				<td class="integer">{$channels[user].hits}</td>
				<td class="action">
					<a class="link" href="tiki-view_forum.php?forumId={$channels[user].forumId}" title="{tr}View{/tr}">{icon _id='table' alt="{tr}View{/tr}"}</a>

{if $tiki_p_forum_lock eq 'y'}
	{if $channels[user].is_locked eq 'y'}
		{self_link _icon='lock_break' _alt="{tr}Unlock{/tr}" lock='n' forumId=$channels[user].forumId}{/self_link}
	{else}
		{self_link _icon='lock_add' _alt="{tr}Lock{/tr}" lock='y' forumId=$channels[user].forumId}{/self_link}
	{/if}
{/if}

{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
			{self_link _icon='page_edit' cookietab='2' _anchor='anchor2' forumId=$channels[user].forumId}{tr}Edit{/tr}{/self_link}

						{if $channels[user].individual eq 'y'}
							<a class="link" href="tiki-objectpermissions.php?objectName=Forum+{$channels[user].name|escape}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}" title="{tr}Active Perms{/tr}">{icon _id='key_active' alt="{tr}Active Perms{/tr}"}</a>
						{else}
							<a class="link" href="tiki-objectpermissions.php?objectName=Forum+{$channels[user].name|escape}&amp;objectType=forum&amp;permType=forums&amp;objectId={$channels[user].forumId}" title="{tr}Perms{/tr}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
						{/if}
						{self_link _icon='cross' remove=$channels[user].forumId}{tr}Delete{/tr}{/self_link}
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=$numbercol}
		{/section}
	</table>
	
	{if $channels}
		<div style="text-align:left">
			<br />
			{tr}Perform action with checked:{/tr}
			<select name="batchaction" onchange="show('groups');">
				<option value="">{tr}...{/tr}</option>
				{if $tiki_p_admin_forum eq 'y'}
					<option value="delsel_x">{tr}Delete{/tr}</option>
				{/if}
			</select>
			<input type="submit" name="batchaction" value="{tr}OK{/tr}" />
		</div>
	{/if}
</form>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/tab}

{tab name="{tr}Create/Edit Forums{/tr}"}

{if $dup_mode != 'y'}
	{if $forumId > 0}
		<h2>{tr}Edit this Forum:{/tr} {$name|escape}</h2>
		{include file='object_perms_summary.tpl' objectName=$name objectType='forum' objectId=$forumId permType=$permsType}
	{else}
		<h2>{tr}Create New Forum{/tr}</h2>
	{/if}

	<form action="tiki-admin_forums.php" method="post">
		<input type="hidden" name="forumId" value="{$forumId|escape}" />
		<table class="formcolor">
			<tr>
				<td>{tr}Name:{/tr}</td>
				<td>
					<input type="text" name="name" size="50" value="{$name|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Description:{/tr}</td>
				<td>
					<textarea name="description" rows="4" cols="40">{$description|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td>{tr}Show description:{/tr}</td>
				<td>
					<input type="checkbox" name="show_description" {if $show_description eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Prevent flooding:{/tr}</td>
				<td>
					<input type="checkbox" name="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if} />
					{tr}Minimum time between posts:{/tr}
					{html_options name=floodInterval options=$flood_options selected=$floodInterval}
				</td>
			</tr>
			<tr>
				<td>{tr}Section:{/tr}</td>
				<td>
					<select name="section">
						<option value="" {if $forumSection eq ""}selected="selected"{/if}>{tr}None{/tr}</option>
						<option value="__new__">{tr}Create new{/tr}</option>
						{section name=ix loop=$sections}
							<option {if $forumSection eq $sections[ix]}selected="selected"{/if} value="{$sections[ix]|escape}">{$sections[ix]|escape}</option>
						{/section}
					</select>
					<input name="new_section" type="text" />
				</td>
			</tr>
			<tr>
				<td>{tr}Moderator user:{/tr}</td>
				<td>
					<input id="moderator_user" type="text" name="moderator" value="{$moderator|escape}"/>
					{autocomplete element='#moderator_user' type='username'}
				</td>
			</tr>
			<tr>
				<td>{tr}Moderator group:{/tr}</td>
				<td>
					<input id="moderator_group" type="text" name="moderator_group" value="{$moderator_group|escape}"/>
					{autocomplete element='#moderator_group' type='groupname'}
				</td>
			</tr>
			<tr>
				<td>{tr}Password protected{/tr}</td>
				<td>
					{html_options name=forum_use_password options=$forum_use_password_options selected=$forum_use_password}
				</td>
			</tr>
			<tr>
				<td>{tr}Forum password{/tr}</td>
				<td>
					<input type="text" name="forum_password" value="{$forum_password|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Only allow replies to the first message (flat forum):{/tr}</td>
				<td>
					<input type="checkbox" name="is_flat" {if $is_flat eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			{include file='categorize.tpl'}

			<tr>
				<td>
					<input type="checkbox" name="useMail" {if $useMail eq 'y'}checked="checked"{/if} /> {tr}Send this forums posts to this email:{/tr}
				</td>
				<td>
					<input type="text" name="mail" value="{$mail|escape}" />
					</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" name="usePruneUnreplied" {if $usePruneUnreplied eq 'y'}checked="checked"{/if} /> {tr}Prune unreplied messages after:{/tr}
				</td>
				<td>
					{html_options name=pruneUnrepliedAge options=$pruneUnrepliedAge_options selected=$pruneUnrepliedAge}
				</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" name="usePruneOld" {if $usePruneOld eq 'y'}checked="checked"{/if} /> {tr}Prune old messages after:{/tr}</td>
				<td>
					{html_options name=pruneMaxAge options=$pruneMaxAge_options selected=$pruneMaxAge}
				</td>
			</tr>
			<tr>
				<td>{tr}Topic list configuration{/tr}</td>
				<td>
					<table class="formcolor">
						<tr>
							<td>{tr}Replies{/tr}</td>
							<td>{tr}Reads{/tr}</td>
							<td>{tr}Points{/tr}</td>
							<td>{tr}Last post{/tr}</td>
							<td>{tr}Last post title{/tr}</td>
							<td>{tr}Last post avatar{/tr}</td>
							<td>{tr}Author{/tr}</td>
							<td>{tr}Author avatar{/tr}</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="topics_list_replies" {if $topics_list_replies eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_reads" {if $topics_list_reads eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_pts" {if $topics_list_pts eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_lastpost" {if $topics_list_lastpost eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_lastpost_title" {if $topics_list_lastpost_title eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_lastpost_avatar" {if $topics_list_lastpost_avatar eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_author" {if $topics_list_author eq 'y'}checked="checked"{/if} />
							</td>
							<td>
								<input type="checkbox" name="topics_list_author_avatar" {if $topics_list_author_avatar eq 'y'}checked="checked"{/if} />
							</td>

						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>{tr}Posts can be rated{/tr}</td>
				<td>
					<input type="checkbox" name="vote_threads" {if $vote_threads eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Display last post titles{/tr}</td>
				<td>
					{html_options name=forum_last_n options=$forum_last_n_options selected=$forum_last_n}
				</td>
			</tr>
			<tr>
				<td>
					{tr}Forward messages to this forum to this e-mail address, in a format that can be used for sending back to the inbound forum e-mail address{/tr}
				</td>
				<td>
					<input type="text" name="outbound_address" size="50" value="{$outbound_address|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Send mails even when the post is generated by inbound mail{/tr}</td>
				<td>
					<input type="checkbox" name="outbound_mails_for_inbound_mails" {if $outbound_mails_for_inbound_mails eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Append a reply link to outbound mails{/tr}</td>
				<td>
					<input type="checkbox" name="outbound_mails_reply_link" {if $outbound_mails_reply_link eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Originating e-mail address for mails from this forum{/tr}</td>
				<td>
					<input type="text" name="outbound_from" size="50" value="{$outbound_from|escape}" />
				</td>
			</tr>
			<tr>
				<td>{tr}Add messages from this email to the forum{/tr}</td>
				<td>
					<table>
						<tr>
							<td>{tr}POP3 server:{/tr}</td>
							<td>
								<input type="text" name="inbound_pop_server" value="{$inbound_pop_server|escape}" />
							</td>
						</tr>
						<tr>
							<td>{tr}User:{/tr}</td>
							<td>
								<input type="text" name="inbound_pop_user" value="{$inbound_pop_user|escape}" />
							</td>
						</tr>
						<tr>
							<td>{tr}Password:{/tr}</td>
							<td>
								<input type="password" name="inbound_pop_password" value="{$inbound_pop_password|escape}" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>{tr}Use topic smileys{/tr}</td>
				<td>
					<input type="checkbox" name="topic_smileys" {if $topic_smileys eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Show topic summary{/tr}</td>
				<td>
					<input type="checkbox" name="topic_summary" {if $topic_summary eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}User information display{/tr}</td>
				<td>
					<table class="formcolor">
						<tr>
							<td>{tr}Avatar{/tr}</td>
							<td>{tr}Flag{/tr}</td>
							<td>{tr}Posts{/tr}</td>
							<td>{tr}User Level{/tr}</td>
							<td>{tr}eMail{/tr}</td>
							<td>{tr}Online{/tr}</td>
						</tr>
					<tr>
						<td>
							<input type="checkbox" name="ui_avatar" {if $ui_avatar eq 'y'}checked="checked"{/if} />
						</td>
						<td>
							<input type="checkbox" name="ui_flag" {if $ui_flag eq 'y'}checked="checked"{/if} />
						</td>
						<td>
							<input type="checkbox" name="ui_posts" {if $ui_posts eq 'y'}checked="checked"{/if} />
						</td>
						<td>
							<input type="checkbox" name="ui_level" {if $ui_level eq 'y'}checked="checked"{/if} />
						</td>
						<td>
							<input type="checkbox" name="ui_email" {if $ui_email eq 'y'}checked="checked"{/if} />
						</td>
						<td>
							<input type="checkbox" name="ui_online" {if $ui_online eq 'y'}checked="checked"{/if} />
						</td>
					</tr>		
				</table>
			</td>
		</tr>
		<tr>
			<td>{tr}Approval type{/tr}</td>
			<td>
				{html_options name=approval_type options=$approval_options selected=$approval_type}
			</td>
		</tr>
		<tr>
			<td>{tr}Attachments{/tr}</td>
			<td>
				{html_options name=att options=$attachment_options selected=$att}
				<br />
				{tr}Store attachments in:{/tr}
				<table class="formcolor">
					<tr>
						<td>
							<input type="radio" name="att_store" value="db" {if $att_store eq 'db'}checked="checked"{/if} /> {tr}Database{/tr}
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="att_store" value="dir" {if $att_store eq 'dir'}checked="checked"{/if} /> {tr}Path:{/tr} <input type="text" name="att_store_dir" value="{$att_store_dir|escape}" size="14" />
							</td>
						</tr>
						<tr>
							<td>
								{tr}Max attachment size (bytes):{/tr} <input type="text" name="att_max_size" value="{$att_max_size|escape}" /><br /><i>{tr}Max:{/tr} {$maxAttachSize|escape} ({$maxAttachSize|kbsize})</i>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="att_list_nb"{if $att_list_nb eq 'y'} checked="checked"{/if} id="att_list_nb" /><label for="att_list_nb">{tr}Shows number of attachments of the all thread in forum list{/tr}</label>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td>{tr}Set topics preferences{/tr}</td>
				<td>
					<a class="link" href="javascript:flip('topicconfig');flip('topicprefshow','inline');flip('topicprefhide','inline');">
						<span id="topicprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}none{else}inline{/if};">{tr}Show topics preferences{/tr}</span>
						<span id="topicprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}inline{else}none{/if};">{tr}hide topics preferences{/tr}</span>
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">

					<table id="topicconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}block{else}none{/if}; border: 0;">
						<tr>
							<td>{tr}Default ordering for topics:{/tr}</td>
							<td>
								{html_options name=topicOrdering options=$topicOrdering_options selected=$topicOrdering}
							</td>
						</tr>
						<tr>
							<td>{tr}Topics per page:{/tr}</td>
							<td>
								<input type="text" name="topicsPerPage" value="{$topicsPerPage|escape}" />
							</td>
						</tr>
					</table>

				</td>
			</tr>

			{if $prefs.forum_thread_defaults_by_forum eq 'y'}
				<tr><td>{tr}Set thread preferences{/tr}</td>
					<td>
						<a class="link" href="javascript:flip('threadconfig');flip('threadprefshow','inline');flip('threadprefhide','inline');">
							<span id="threadprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}none{else}inline{/if};">{tr}Show threads preferences{/tr}</span>
							<span id="threadprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}inline{else}none{/if};">{tr}hide threads preferences{/tr}</span>
							</a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						
						<table id="threadconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}block{else}none{/if}; border: 0;">
							<tr>
								<td>{tr}Default ordering for threads:{/tr}</td>
								<td>
									{html_options name=threadOrdering options=$threadOrdering_options selected=$threadOrdering}
								</td>
							</tr>
							<tr>
								<td>{tr}Default style for threads:{/tr}</td>
								<td>
									{html_options name=threadStyle options=$threadStyle_options selected=$threadStyle}
								</td>
							</tr>
							<tr>
								<td>{tr}Default number of comments per page:{/tr}</td>
								<td>
									{html_options name=commentsPerPage options=$commentsPerPage_options selected=$commentsPerPage}
								</td>
							</tr>
						</table>

					</td>
				</tr>
			{/if}
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
			</tr>
		</table>
	</form>

{else}{*duplicate*}
	<h2>{tr}Duplicate Forum{/tr}</h2>
	<form action="tiki-admin_forums.php" method="post">
		<table class="formcolor">
			<tr>
				<td>{tr}Name{/tr}</td>
				<td><input type="text" size="50" name="name" value="{$name|escape}" /></td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}</td>
				<td><textarea name="description" rows="4" cols="50">{$description|escape}</textarea></td>
			</tr>
			<tr>
				<td>{tr}Forum{/tr}</td>
				<td>
					<select name="forumId">
						{section name=ix loop=$allForums}
							<option value="{$allForums[ix].forumId}">{$allForums[ix].name}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>{tr}Duplicate categories{/tr}</td>
				<td><input type="checkbox" name="dupCateg" /></td>
			</tr>
			<tr>
				<td>{tr}Duplicate perms{/tr}</td>
				<td><input type="checkbox" name="dupPerms" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="duplicate" value="{tr}Duplicate{/tr}" /></td>
			</tr>
		</table>
	</form>
{/if}
{/tab}

{/tabset}
