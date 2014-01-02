{* $Id$ *}

{title help="Forums" admpage="forums"}{tr}Admin Forums{/tr}{/title}

<div class="t_navbar form-group">
	{if $tiki_p_admin_forum eq 'y' && $forumId > 0 or (isset($dup_mode) and $dup_mode eq 'y')}
		{button class="btn btn-default" href="?" _text="{tr}Create New Forum{/tr}"}
	{/if}
	{if $tiki_p_admin_forum eq 'y' && (!isset($dup_mode) or $dup_mode ne 'y')}
		{button class="btn btn-default" href="tiki-admin_forums.php?dup_mode=y" _text="{tr}Duplicate Forum{/tr}"}
	{/if}
	{if $forumId > 0}
		{button class="btn btn-default" href="tiki-view_forum.php?forumId=$forumId" _text="{tr}View this forum{/tr}"}
	{/if}
	{if $tiki_p_admin_forum eq 'y'}
		{button class="btn btn-default" href="tiki-forum_import.php" _text="{tr}Import forums{/tr}"}
	{/if}
	{if $tiki_p_forum_read eq 'y'}
		{button class="btn btn-default" href="tiki-forums.php" _text="{tr}List forums{/tr}"}
	{/if}
</div>

{tabset}

{tab name="{tr}Forums{/tr}"}
    <h2>{tr}Forums{/tr}</h2>

{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<form action="#">
    <div class="table-responsive">
	<table class="table normal">
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

		{section name=user loop=$channels}
			<tr>
				<td style="text-align:center">
					<input type="checkbox" name="checked[]" value="{$channels[user].forumId|escape}" {if isset($smarty.request.checked) and $smarty.request.checked and in_array($channels[user].forumId,$smarty.request.checked)}checked="checked"{/if}>
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

{if isset($tiki_p_forum_lock) and $tiki_p_forum_lock eq 'y'}
	{if $channels[user].is_locked eq 'y'}
		{self_link _icon='lock_break' _alt="{tr}Unlock{/tr}" lock='n' forumId=$channels[user].forumId}{/self_link}
	{else}
		{self_link _icon='lock_add' _alt="{tr}Lock{/tr}" lock='y' forumId=$channels[user].forumId}{/self_link}
	{/if}
{/if}

{if ($tiki_p_admin eq 'y')
	or ((isset($channels[user].individual) and $channels[user].individual eq 'n')
	and ($tiki_p_admin_forum eq 'y'))
	or ($channels[user].individual_tiki_p_admin_forum eq 'y')
}
			{self_link _icon='page_edit' cookietab='2' _anchor='anchor2' forumId=$channels[user].forumId}{tr}Edit{/tr}{/self_link}

						{if isset($channels[user].individual) and $channels[user].individual eq 'y'}
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
	</div>

	{if $channels}
		<div style="text-align:left">
			<br>
			{tr}Perform action with checked:{/tr}
			<select name="batchaction" onchange="show('groups');">
				<option value="">{tr}...{/tr}</option>
				{if $tiki_p_admin_forum eq 'y'}
					<option value="delsel_x">{tr}Delete{/tr}</option>
				{/if}
			</select>
			<input type="submit" class="btn btn-default btn-sm" name="batchaction" value="{tr}OK{/tr}">
		</div>
	{/if}
</form>

{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/tab}

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
		<div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Name:{/tr}</label>
            <div class="col-sm-9">
				<input type="text" name="name" class="form-control" id="name" value="{$name|escape}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Description:{/tr}</label>
            <div class="col-sm-9">
			    <textarea name="description" rows="4" class="form-control" id="description">{$description|escape}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Show description:{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
				<input type="checkbox" name="show_description" {if $show_description eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Prevent flooding:{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
				<input type="checkbox" name="controlFlood" {if $controlFlood eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Minimum time between posts:{/tr}</label>
            <div class="col-sm-4 checkbox-inline">
				{html_options name=floodInterval class="form-control" options=$flood_options selected=$floodInterval}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Section:{/tr}</label>
            <div class="col-sm-4">
				<select name="section" class="form-control">
					<option value="" {if $forumSection eq ""}selected="selected"{/if}>{tr}None{/tr}</option>
					<option value="__new__">{tr}Create new{/tr}</option>
					{section name=ix loop=$sections}
						<option {if $forumSection eq $sections[ix]}selected="selected"{/if} value="{$sections[ix]|escape}">{$sections[ix]|escape}</option>
					{/section}
				</select>
            </div>
            <div class="col-sm-5">
				<input name="new_section" class="form-control"  type="text">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Moderator user:{/tr}</label>
            <div class="col-sm-9">
			    <input id="moderator_user" class="form-control" type="text" name="moderator" value="{$moderator|escape}">
				{autocomplete element='#moderator_user' type='username'}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="moderator_group">{tr}Moderator group:{/tr}</label>
            <div class="col-sm-9">
				<input id="moderator_group" type="text" class="form-control" name="moderator_group" id="moderator_group" value="{$moderator_group|escape}">
				{autocomplete element='#moderator_group' type='groupname'}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Password protected{/tr}</label>
            <div class="col-sm-4">
		        {html_options name=forum_use_password options=$forum_use_password_options selected=$forum_use_password class=form-control}
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Forum password{/tr}</label>
            <div class="col-sm-9">
				<input type="text" name="forum_password" class="form-control" value="{$forum_password|escape}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Only allow replies to the first message (flat forum):{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
				<input type="checkbox" name="is_flat" {if $is_flat eq 'y'}checked="checked"{/if}>
		    </div>
		</div>

			{include file='categorize.tpl'}

        <div class="form-group">
            <label class="col-sm-3 control-label" for="useMail">
		    	<input type="checkbox" name="useMail" id="useMail" {if $useMail eq 'y'}checked="checked"{/if}> {tr}Send this forums posts to this email:{/tr} </label>
            <div class="col-sm-9">
				<input type="text" class="form-control" name="mail" value="{$mail|escape}">
            </div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">
				<input type="checkbox" name="usePruneUnreplied" {if $usePruneUnreplied eq 'y'}checked="checked"{/if}> {tr}Prune unreplied messages after:{/tr}
			</label>
            <div class="col-sm-4 checkbox-inline">
                {html_options name=pruneUnrepliedAge options=$pruneUnrepliedAge_options selected=$pruneUnrepliedAge}
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">
                <input type="checkbox" name="usePruneOld" {if $usePruneOld eq 'y'}checked="checked"{/if}> {tr}Prune old messages after:{/tr}
            </label>
            <div class="col-sm-4 checkbox-inline">
				{html_options name=pruneMaxAge options=$pruneMaxAge_options selected=$pruneMaxAge}
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Topic list configuration{/tr}</label>
			<div class="col-sm-9">
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
							<input type="checkbox" name="topics_list_replies" {if $topics_list_replies eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_reads" {if $topics_list_reads eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_pts" {if $topics_list_pts eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_lastpost" {if $topics_list_lastpost eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_lastpost_title" {if $topics_list_lastpost_title eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_lastpost_avatar" {if $topics_list_lastpost_avatar eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_author" {if $topics_list_author eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="topics_list_author_avatar" {if $topics_list_author_avatar eq 'y'}checked="checked"{/if}>
						</td>
                    </tr>
				</table>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Posts can be rated{/tr}</label>
            <div class="col-sm-8 checkbox-inline">
    			<input type="checkbox" name="vote_threads" {if $vote_threads eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Display last post titles{/tr}</label>
			<div class="col-sm-4 checkbox-inline">
				{html_options name=forum_last_n options=$forum_last_n_options selected=$forum_last_n}
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Forward messages to this forum to this e-mail address, in a format that can be used for sending back to the inbound forum e-mail address{/tr}</label>
            <div class="col-sm-9">
                <input type="text" name="outbound_address" size="50" value="{$outbound_address|escape}">
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 checkbox-inline" for="name">{tr}Send mails even when the post is generated by inbound mail{/tr}</label>
            <div class="col-sm-8 checkbox-inline">
			    <input type="checkbox" name="outbound_mails_for_inbound_mails" {if $outbound_mails_for_inbound_mails eq 'y'}checked="checked"{/if}>
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="outbound_mails_reply_link">{tr}Append a reply link to outbound mails{/tr}</label>
            <div class="col-sm-8 checkbox-inline">
				<input type="checkbox" name="outbound_mails_reply_link" {if $outbound_mails_reply_link eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="outbound_from">{tr}Originating e-mail address for mails from this forum{/tr}</label>
            <div class="col-sm-9">
		    	<input type="text" name="outbound_from" id="outbound_from" size="50" value="{$outbound_from|escape}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name">{tr}Add messages from this email to the forum{/tr}</label>
            <div class="col-sm-9">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}POP3 server:{/tr}</label>
			    	<div class="col-sm-9">
				    	<input type="text" name="inbound_pop_server" id="inbound_pop_server"  value="{$inbound_pop_server|escape}">
				    </div>
	            </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}User:{/tr}</label>
                    <div class="col-sm-9">
			    	    <input type="text" name="inbound_pop_user" value="{$inbound_pop_user|escape}" autocomplete="off">
				    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}Password:{/tr}</label>
                    <div class="col-sm-9">
                        <input type="password" name="inbound_pop_password" value="{$inbound_pop_password|escape}" autocomplete="off">
                    </div>
                </div>
			</div>
        </div>
		<div class="form-group">
            <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}Use topic smileys{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
				<input type="checkbox" name="topic_smileys" {if $topic_smileys eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}Show topic summary{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
				<input type="checkbox" name="topic_summary" {if $topic_summary eq 'y'}checked="checked"{/if}>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="inbound_pop_server">{tr}User information display{/tr}</label>
            <div class="col-sm-9">

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
							<input type="checkbox" name="ui_avatar" {if $ui_avatar eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="ui_flag" {if $ui_flag eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="ui_posts" {if $ui_posts eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="ui_level" {if $ui_level eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="ui_email" {if $ui_email eq 'y'}checked="checked"{/if}>
						</td>
						<td>
							<input type="checkbox" name="ui_online" {if $ui_online eq 'y'}checked="checked"{/if}>
						</td>
					</tr>		
				</table>
            </div>
        </div>
   		<div class="form-group">
            <label class="col-sm-3 control-label" for="approval_type">{tr}Approval type{/tr}</label>
            <div class="col-sm-4 checkbox-inline">
				{html_options name=approval_type for=approval_type id=approval_type class=form-control options=$approval_options selected=$approval_type}
			</div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="approval_type">{tr}Attachments{/tr}</label>
            <div class="col-sm-4 checkbox-inline">
				{html_options name=att options=$attachment_options selected=$att}
				<br>
				{tr}Store attachments in:{/tr}
				<table class="formcolor">
					<tr>
						<td>
							<input type="radio" name="att_store" value="db" {if $att_store eq 'db'}checked="checked"{/if}> {tr}Database{/tr}
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="att_store" value="dir" {if $att_store eq 'dir'}checked="checked"{/if}> {tr}Path:{/tr} <input type="text" name="att_store_dir" value="{$att_store_dir|escape}" size="14" />
							</td>
						</tr>
						<tr>
							<td>
								{tr}Max attachment size (bytes):{/tr} <input type="text" name="att_max_size" value="{$att_max_size|escape}"><br><i>{tr}Max:{/tr} {$maxAttachSize|escape} ({$maxAttachSize|kbsize})</i>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="att_list_nb"{if $att_list_nb eq 'y'} checked="checked"{/if} id="att_list_nb"><label for="att_list_nb">{tr}Shows number of attachments of the all thread in forum list{/tr}</label>
							</td>
						</tr>
					</table>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="approval_type">{tr}Set topics preferences{/tr}</label>
            <div class="col-sm-9">
			    <a class="link" href="javascript:flip('topicconfig');flip('topicprefshow','inline');flip('topicprefhide','inline');">
				    <span id="topicprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}none{else}inline{/if};">{tr}Show topics preferences{/tr}</span>
				    <span id="topicprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}inline{else}none{/if};">{tr}hide topics preferences{/tr}</span>
				</a>
			</div>
		</div>
	{if $prefs.feature_multilingual eq 'y'}
        <div class="form-group">
            <label class="col-sm-3 control-label" for="approval_type">{tr}Language{/tr}</label>
            <div class="col-sm-9">
			    <select name="forumLanguage" id="forumLanguage"  class="checkbox-inline">
				    <option value="">{tr}Unknown{/tr}</option>
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"{if $forumLanguage eq $languages[ix].value or (!($data.page_id) and $forumLanguage eq '' and $languages[ix].value eq $prefs.language)} selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
				</select>
			</div>
		</div>
	{/if}
        <div id="topicconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_topicconfig) and $smarty.session.tiki_cookie_jar.show_topicconfig eq 'y'}block{else}none{/if};">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="approval_type">{tr}Default ordering for topics:{/tr}</label>
                <div class="col-sm-9 checkbox-inline">
				    {html_options name=topicOrdering options=$topicOrdering_options selected=$topicOrdering}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="approval_type">{tr}Topics per page:{/tr}</label>
                <div class="col-sm-4">
				    <input type="text" name="topicsPerPage" value="{$topicsPerPage|escape}">
				</div>
			</div>
		</div>
	{*if $prefs.forum_thread_defaults_by_forum eq 'y'*}
        <div class="form-group">
            <label class="col-sm-3 control-label" for="approval_type">{tr}Set thread preferences{/tr}</label>
            <div class="col-sm-9">
    			<a class="link" href="javascript:flip('threadconfig');flip('threadprefshow','inline');flip('threadprefhide','inline');">
					<span id="threadprefshow" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}none{else}inline{/if};">{tr}Show threads preferences{/tr}</span>
					<span id="threadprefhide" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}inline{else}none{/if};">{tr}hide threads preferences{/tr}</span>
				</a>
			</div>
		</div>
	    <div id="threadconfig" style="display:{if isset($smarty.session.tiki_cookie_jar.show_threadconfig) and $smarty.session.tiki_cookie_jar.show_threadconfig eq 'y'}block{else}none{/if};">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="approval_type">{tr}Default ordering of threads:{/tr}</label>
                <div class="col-sm-9">
				    {html_options name=threadOrdering options=$threadOrdering_options selected=$threadOrdering}
				</div>
			</div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="approval_type">{tr}Default style of threads:{/tr}</label>
                <div class="col-sm-9">
					{html_options name=threadStyle options=$threadStyle_options selected=$threadStyle}
				</div>
			</div>
        </div>
    {*/if*}
            <div class="form-group">
                <label class="col-sm-3 control-label" for="approval_type">{tr}Default number of comments per page:{/tr}</label>
                <div class="col-sm-9">
					{html_options name=commentsPerPage options=$commentsPerPage_options selected=$commentsPerPage}
				</div>
            </div>
   		<div class="text-center">
            <input type="submit" class="btn btn-default" name="save" value="{tr}Save{/tr}">
        </div>

	</form>

{else}{*duplicate*}
	<h2>{tr}Duplicate Forum{/tr}</h2>
	<form action="tiki-admin_forums.php" method="post" class="form-horizontal" role="form">
        <div class="form-group">
    		<label class="col-sm-3 control-label" for="duplicate_name">{tr}Name{/tr}</label>
            <div class="col-sm-9">
		    	<input type="text" name="duplicate_name" id="duplicate_name" value="{$name|escape}">
            </div>
		</div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="duplicate_description">{tr}Description{/tr}</label>
            <div class="col-sm-9">
                <textarea name="description" rows="4" id="duplicate_description" class="form-control">{$description|escape}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="duplicate_forumId">{tr}Forum{/tr}</label>
            <div class="col-sm-9">
					<select name="duplicate_forumId" id="duplicate_forumId" class="form-control">
						{section name=ix loop=$allForums}
							<option value="{$allForums[ix].forumId}">{$allForums[ix].name}</option>
						{/section}
					</select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="duplicate_categories">{tr}Duplicate categories{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
                <input type="checkbox" name="dupCateg" id="duplicate_categories">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="duplicate_perms">{tr}Duplicate permissions{/tr}</label>
            <div class="col-sm-9 checkbox-inline">
                <input type="checkbox" name="dupPerms" id="duplicate_perms">
            </div>
        </div>
        <div class="text-center">
            <input type="submit" class="btn btn-default" name="duplicate" value="{tr}Duplicate{/tr}">
        </div>
	</form>
{/if}
{/tab}

{/tabset}
