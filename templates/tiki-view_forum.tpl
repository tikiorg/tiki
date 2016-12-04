{* $Id$ *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
{if !$tsAjax}
	{$forum_info.name|addonnavbar:'forum'}
	{block name=title}
		{title help="forums" admpage="forums" url=$forum_info.forumId|sefurl:'forum'}{$forum_info.name|addongroupname}{/title}
	{/block}

	{if $forum_info.show_description eq 'y'}
		<div class="description help-block">{wiki}{$forum_info.description}{/wiki}</div>
	{/if}

	<div class="t_navbar margin-bottom-md">
		{assign var=thisforum_info value=$forum_info.forumId}
		{if ($tiki_p_forum_post_topic eq 'y' and ($prefs.feature_wiki_discuss ne 'y' or $prefs.$forumId ne $prefs.wiki_forum_id)) or $tiki_p_admin_forum eq 'y'}
			{if !isset($comments_threadId) or $comments_threadId eq 0}
				{button href="tiki-view_forum.php?openpost=1&amp;forumId=$thisforum_info&amp;comments_threadId=0&amp;comments_threshold=$comments_threshold&amp;comments_offset=$comments_offset&amp;thread_sort_mode=$thread_sort_mode&amp;comments_per_page=$comments_per_page" _onclick="$('#forumpost').show();return false;" _icon_name="create" _type="default" class="btn btn-default" _text="{tr}New Topic{/tr}"}
			{else}
				{button href="tiki-view_forum.php?openpost=1&amp;forumId=$thisforum_info&amp;comments_threadId=0&amp;comments_threshold=$comments_threshold&amp;comments_offset=$comments_offset&amp;thread_sort_mode=$thread_sort_mode&amp;comments_per_page=$comments_per_page" _onclick="$('#forumpost').show();return false;" _icon_name="create" _type="link" class="btn btn-link" _text="{tr}New Topic{/tr}"}
			{/if}
		{/if}
		{if $tiki_p_admin_forum eq 'y'}
			{button href="tiki-admin_forums.php?forumId=$thisforum_info&amp;cookietab=2#content_admin_forums1-2" _icon_name="edit" _type="link" class="btn btn-link" _text="{tr}Edit Forum{/tr}"}
		{/if}
		{if $tiki_p_admin_forum eq 'y' or !isset($all_forums) or $all_forums|@count > 1}
			{* No need for users to go to forum list if they are already looking at the only forum BUT note that all_forums only defined with quickjump feature *}
			{button href="tiki-forums.php" _icon_name="list" _type="link" class="btn btn-link" _text="{tr}Forum List{/tr}"}
		{/if}

		<div class="btn-group pull-right">
			{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
			<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
				{icon name='menu-extra'}
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
				<li class="dropdown-title">
					{tr}Forum actions{/tr}
				</li>
				<li class="divider"></li>
				{if $user and $prefs.feature_user_watches eq 'y'}
					<li>
						{if $user_watching_forum eq 'n'}
							<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic&amp;watch_object={$forumId}&amp;watch_action=add">
								{icon name="watch"} {tr}Monitor topics{/tr}
							</a>
						{else}
							<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic&amp;watch_object={$forumId}&amp;watch_action=remove">
								{icon name="stop-watching"} {tr}Stop monitoring topics{/tr}
							</a>
						{/if}
					</li>
				{/if}
				{if $user and $prefs.feature_user_watches eq 'y'}
					<li>
						{if $user_watching_forum_topic_and_thread eq 'n'}
							<a href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic_and_thread&amp;watch_object={$forumId}&amp;watch_action=add">
								{icon name="watch"} {tr}Monitor topics and threads{/tr}
							</a>
						{else}
							<a class="pull-right tips" href="tiki-view_forum.php?forumId={$forumId}&amp;watch_event=forum_post_topic_and_thread&amp;watch_object={$forumId}&amp;watch_action=remove">
								{icon name="stop-watching"} {tr}Stop monitoring topics and threads{/tr}
							</a>
						{/if}
					</li>
				{/if}
				{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
					<li>
						<a href="tiki-object_watches.php?objectId={$forumId|escape:"url"}&amp;watch_event=forum_post_topic&amp;objectType=forum&amp;objectName={$forum_info.name|escape:"url"}&amp;objectHref={'tiki-view_forum.php?forumId='|cat:$forumId|escape:"url"}">
							{icon name="watch-group"} {tr}Group monitor topics{/tr}
						</a>
					</li>
					<li>
						<a href="tiki-object_watches.php?objectId={$forumId|escape:"url"}&amp;watch_event=forum_post_topic_and_thread&amp;objectType=forum&amp;objectName={$forum_info.name|escape:"url"}&amp;objectHref={'tiki-view_forum.php?forumId='|cat:$forumId|escape:"url"}">
							{icon name="watch-group"} {tr}Group monitor topics and threads{/tr}
						</a>
					</li>
				{/if}
				{if !empty($tiki_p_forum_lock) and $tiki_p_forum_lock eq 'y'}
					<li>
						{if $forum_info.is_locked eq 'y'}
							{self_link lock='n' _icon_name='unlock' _menu_text='y' _menu_icon='y'}
								{tr}Unlock{/tr}
							{/self_link}
						{else}
							{self_link lock='y' _icon_name='lock'  _menu_text='y' _menu_icon='y'}
								{tr}Lock{/tr}
							{/self_link}
						{/if}
					</li>
				{/if}
				{if $prefs.feed_forum eq 'y'}
					<li>
						<a href="tiki-forum_rss.php?forumId={$forumId}">
							{icon name="rss"} {tr}RSS feed{/tr}
						</a>
					</li>
				{/if}
			</ul>
			{if $js == 'n'}</li></ul>{/if}
		</div>
		<div class="categbar" align="right">
			{if $user and $prefs.feature_user_watches eq 'y'}
				{if isset($category_watched) and $category_watched eq 'y'}
					{tr}Watched by categories:{/tr}
					{section name=i loop=$watching_categories}
						<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>
						&nbsp;
					{/section}
				{/if}
			{/if}
		</div>
	</div>
	<div class="breadcrumb">
		<a class="link" href="{if $prefs.feature_sefurl eq 'y'}forums{else}tiki-forums.php{/if}">{tr}Forums{/tr}</a>
		{$prefs.site_crumb_seper}
		<a class="link" href="{$forumId|sefurl:'forum'}">{$forum_info.name|addongroupname|escape}</a>
	</div>

	{include file='utilities/feedback.tpl'}
	{if $tiki_p_forum_post_topic eq 'y'}
		{if $comment_preview eq 'y'}
			<br><br>
			<b>{tr}Preview{/tr}</b>
			<div class="commentscomment">
				<div class="commentheader">
					<table>
						<tr>
							<td>
								<div class="commentheader">
									<span class="commentstitle">{$comments_preview_title|escape}</span>
									<br>
									{tr}by{/tr} {$user|userlink}
								</div>
							</td>
							<td valign="top" align="right">
								<div class="commentheader">
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="commenttext">
					{$comments_preview_data}
					<br>
				</div>
			</div>
		{/if}


		<div id="forumpost" style="display:{if $comments_threadId > 0 or $openpost eq 'y' or $warning eq 'y' or !empty($comment_title) or !empty($smarty.request.comments_previewComment)}block{else}none{/if};">
			{if $comments_threadId > 0}
				{tr}Editing:{/tr} {$comment_title|escape} (<a class="forumbutlink" href="tiki-view_forum.php?openpost=1&amp;forumId={$forum_info.forumId}&amp;comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">{tr}Post New{/tr}</a>)
			{/if}
			<form method="post" enctype="multipart/form-data" action="tiki-view_forum.php" id="editpageform">
				<input type="hidden" name="comments_offset" value="{$comments_offset|escape}">
				<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}">
				<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}">
				<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}">
				<input type="hidden" name="forumId" value="{$forumId|escape}">
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="comments_title">{tr}Title{/tr}</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="comments_title" id="comments_title" value="{$comment_title|escape}">
						</div>
					</div>
					{if $forum_info.forum_use_password ne 'n'}
						<div class="form-group">
							<label class="col-sm-2 control-label" for="comment_password">{tr}Password{/tr}</label>
							<div class="col-sm-10">
								<input type="password" name="comment_password" id="comment_password" class="form-control">
							</div>
						</div>
					{/if}
					{if $tiki_p_admin_forum eq 'y' or $forum_info.topic_smileys eq 'y'}
					<div class="form-group">
						<label class="col-sm-2 control-label" for="comments_topictype">{tr}Type{/tr}</label>
						<div class="col-sm-2">
							{if $tiki_p_admin_forum eq 'y'}
									<select name="comment_topictype" id="comment_topictype" class="form-control comment_topictype">
										<option value="n" {if $comment_topictype eq 'n'}selected="selected"{/if}>{tr}Normal{/tr}</option>
										<option value="a" {if $comment_topictype eq 'a'}selected="selected"{/if}>{tr}Announce{/tr}</option>
										<option value="h" {if $comment_topictype eq 'h'}selected="selected"{/if}>{tr}Hot{/tr}</option>
										<option value="s" {if $comment_topictype eq 's'}selected="selected"{/if}>{tr}Sticky{/tr}</option>
										<option value="d" {if $comment_topictype eq 'd'}selected="selected"{/if}>{tr}Deliberation{/tr}</option>
									</select>
								{/if}
							</div>
							<div class="col-sm-2">
								{if $forum_info.topic_smileys eq 'y'}
									<select name="comment_topicsmiley" class="form-control comment_topicsmiley">
										<option value="" {if $comment_topicsmiley eq ''}selected="selected"{/if}>{tr}no feeling{/tr}</option>
										<option value="icon_frown.gif" {if $comment_topicsmiley eq 'icon_frown.gif'}selected="selected"{/if}>{tr}frown{/tr}</option>
										<option value="icon_exclaim.gif" {if $comment_topicsmiley eq 'icon_exclaim.gif'}selected="selected"{/if}>{tr}exclaim{/tr}</option>
										<option value="icon_idea.gif" {if $comment_topicsmiley eq 'icon_idea.gif'}selected="selected"{/if}>{tr}idea{/tr}</option>
										<option value="icon_mad.gif" {if $comment_topicsmiley eq 'icon_mad.gif'}selected="selected"{/if}>{tr}mad{/tr}</option>
										<option value="icon_neutral.gif" {if $comment_topicsmiley eq 'icon_neutral.gif'}selected="selected"{/if}>{tr}neutral{/tr}</option>
										<option value="icon_question.gif" {if $comment_topicsmiley eq 'icon_question.gif'}selected="selected"{/if}>{tr}question{/tr}</option>
										<option value="icon_sad.gif" {if $comment_topicsmiley eq 'icon_sad.gif'}selected="selected"{/if}>{tr}sad{/tr}</option>
										<option value="icon_smile.gif" {if $comment_topicsmiley eq 'icon_smile.gif'}selected="selected"{/if}>{tr}happy{/tr}</option>
										<option value="icon_wink.gif" {if $comment_topicsmiley eq 'icon_wink.gif'}selected="selected"{/if}>{tr}wink{/tr}</option>
									</select>
								{/if}
							</div>
						</div>
					{/if}

					{if $forum_info.topic_summary eq 'y'}
						<div class="form-group">
							<label class="col-sm-2 control-label">{tr}Summary{/tr}</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="comment_topicsummary" id="comment_topicsummary" value="{$comment_topicsummary|escape}" maxlength="240">
							</div>
						</div>
					{/if}
					<div class="form-group">
						<label class="col-sm-2 control-label" for="editpost">{tr}Message{/tr}</label>
						<div class="col-sm-10">
							{textarea id="editpost" class="form-control" name="comments_data" _simple="y" codemirror="y" syntax="tiki" _toolbars=$prefs.feature_forum_parse}{$comment_data}{/textarea}
						</div>
					</div>
					{if ($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and $tiki_p_admin_forum eq 'y') or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y')}
						<div class="form-group">
							<label class="col-sm-2 control-label" for="userfile1">{tr}Attach a file{/tr}</label>
							<div class="col-sm-10">
								<input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}">
								<input name="userfile1" id="userfile1" class="form-control" type="file">{tr}Maximum size:{/tr} {$forum_info.att_max_size|kbsize}
							</div>
						</div>
					{/if}

					{if $prefs.feature_contribution eq 'y'}
						{include file='contribution.tpl'}
					{/if}
					<script>
						function showDeliberationItemRating(me, btn, input, ratings) {
							btn.find('.deliberationConfigureItemRating').remove();
							btn.append(me.find('div.deliberationConfigureItemRating[data-val="' + input.val() + '"]').clone());
						}

						function configureDeliberationItemRatings(me) {
							me = $(me);
							var btn = me.find('.deliberationConfigureItemRatings'),
								input = btn.next('input.deliberatioRatingOverrideSelector'),
								dialog = btn.prev('div.deliberationItemRatings').clone(),
								ratings = dialog.find('.deliberationConfigureItemRating');

							showDeliberationItemRating(me, btn, input, ratings);

							btn.click(function() {


								ratings
									.hover(function() {
										$(this).addClass('ui-statue-hover');
									},function() {
										$(this).removeClass('ui-statue-hover');
									})
									.click(function() {
										ratings.removeClass('ui-state-highlight');
											$(this).addClass('ui-state-highlight');
									});

								ratings.filter('[data-val="' + input.val() + '"]').addClass('ui-state-highlight');

								var btns = {};
								btns[tr('Ok')] = function() {
									input.val(dialog.find('div.deliberationConfigureItemRating.ui-state-highlight').data('val'));
									showDeliberationItemRating(me, btn, input, ratings);
									dialog.dialog('close');
								};

								btns[tr('Cancel')] = function() {
									dialog.dialog('close');
								};

								dialog.dialog({
									modal: true,
									title: tr('Configure Deliberation Item Ratings'),
									buttons: btns
								});

								return false;
							});
						}
					</script>
					{jq}
						$('select.comment_topictype')
							.change(function() {
								if ($('select.comment_topictype').val() == 'd') {
									$('tr.forum_deliberation').show();
								} else {
									$('tr.forum_deliberation').hide();
								}
							})
							.change();

						var itemMaster;
						$('.forum_deliberation_add_item').click(function() {
							var thisItem;
							if (!itemMaster) {
								$.tikiModal(tr('Loading...'));
								$.get('tiki-ajax_services', {controller: 'comment', action: "deliberation_item"}, function(itemInput) {
									itemMaster = itemInput;
									thisItem = $(itemMaster).insertBefore('div.forum_deliberation_items_toolbar');
									configureDeliberationItemRatings(thisItem);
									$.tikiModal();
								});
							} else {
								thisItem = $(itemMaster).insertBefore('div.forum_deliberation_items_toolbar');
								configureDeliberationItemRatings(thisItem);
							}

							return false;
						});
					{/jq}
					<div class="form-group forum_deliberation" style="display: none;">
						<label class="col-sm-2 control-label">{tr}Deliberation{/tr}</label>
						<div class="col-sm-10 forum_deliberation_items">
							<div class="forum_deliberation_items_toolbar">
								{button href="#" _class="forum_deliberation_add_item" _text="{tr}Add Deliberation Item{/tr}"}
							</div>
						</div>
					</div>

					{if $prefs.feature_antibot eq 'y'}
						{include file='antibot.tpl' tr_style="formcolor"}
					{/if}

					{if $prefs.feature_freetags eq 'y' and $tiki_p_freetags_tag eq 'y'}
						{include file='freetag.tpl'}
					{/if}

					{if $user and $prefs.feature_user_watches eq 'y' and (!isset($comments_threadId) or $comments_threadId eq 0)}
						<div class="form-group">
							<label class="col-sm-2 control-label">{tr}Watch for replies{/tr}</label>
							<div class="col-sm-10">
								<input type="radio" name="set_thread_watch" value="y" id="thread_watch_yes" checked="checked">
								<label for="thread_watch_yes">{tr}Send me an email when someone replies to my topic{/tr}</label>
								<br>
								<input type="radio" name="set_thread_watch" value="n" id="thread_watch_no">
								<label for="thread_watch_no">{tr}Don't send me any emails{/tr}</label>
							</div>
						</div>
					{/if}
					{if empty($user) && $prefs.feature_user_watches eq 'y'}
						<div class="form-group">
							<label for="anonymous_email" class="col-sm-2 control-label">{tr}If you would like to be notified when someone replies to this topic<br>please tell us your e-mail address:{/tr}</label></td>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="anonymous_email" name="anonymous_email">
							</div>
						</div>
					{/if}

					<div class="form-group">
						<label class="col-sm-2 control-label" for="anonymous_name">{tr}Post{/tr}</label>
						<div class="col-sm-10">
							{if empty($user)}
								{tr}Enter your name:{/tr}&nbsp;<input type="text" maxlength="50" id="anonymous_name" name="anonymous_name">
							{/if}
							<input type="submit" class="btn btn-primary btn-sm" name="comments_postComment" value="{tr}Post{/tr}" {if empty($user)}onclick="setCookie('anonymous_name',document.getElementById('anonymous_name').value);needToConfirm=false;"{/if}>
							<input type="submit" class="btn btn-default btn-sm" name="comments_previewComment" value="{tr}Preview{/tr}" {if empty($user)}onclick="setCookie('anonymous_name',document.getElementById('anonymous_name').value);needToConfirm=false;"{/if}>
							<input type="submit" class="btn btn-default btn-sm" name="comments_postCancel" value="{tr}Cancel{/tr}" {if $comment_preview neq 'y'}onclick="hide('forumpost');window.location='#header';return false;"{/if}>
						</div>
					</div>
				</div>
			</form>
			{remarksbox title="{tr}Editing posts{/tr}"}
				{tr}Use wiki syntax when editing the content of posts - HTML is not allowed. Please click on the following link for documentation on wiki syntax:{/tr} {wiki}[http://doc.tiki.org/Wiki+Syntax]{/wiki}
			{/remarksbox}
		</div> <!-- end forumpost -->
	{/if}
	{if $prefs.feature_forum_content_search eq 'y' and $prefs.feature_search eq 'y'}
        <div class="row margin-bottom-md">
		<div class="col-md-5 col-md-offset-7">
			<form id="search-form" class="form" role="form" method="get" action="tiki-search{if $prefs.feature_forum_local_tiki_search eq 'y'}index{else}results{/if}.php">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							{icon name="search"}
						</span>
						<input name="highlight" id="findinforums" type="text" class="form-control" placeholder="{tr}Find{/tr}...">
						<div class="input-group-btn">
							<input type="hidden" name="where" value="forums">
							<input type="hidden" name="forumId" value="{$forum_info.forumId}">
							<input type="submit" class="wikiaction btn btn-default" name="search" value="{tr}Find{/tr}">
						</div>
					</div>
				</div>
			</form>
		</div>
        </div>
	{/if}
{/if}
{if $tiki_p_admin_forum eq 'y' && ($comments_coms|@count > 0 || $queued > 0 || $reported > 0)}
	<div class="panel panel-primary">
		<div class="panel-heading">
			{tr}Moderator actions on selected topics{/tr}
		</div>
		<div class="panel-body">
			<div class="pull-left">
				{if $comments_coms|@count > 1}
					<button
						type="submit"
						form="view_forum"
						formaction="{bootstrap_modal controller=forum action=merge_topic}"
						title=":{tr}Merge{/tr}"
						class="btn btn-default btn-sm tips confirm-submit"
					>
						{icon name="merge"}
					</button>
				{/if}
				{if $all_forums|@count > 1 && $comments_coms|@count > 0}
					<button
						type="submit"
						form="view_forum"
						formaction="{bootstrap_modal controller=forum action=move_topic}"
						title=":{tr}Move{/tr}"
						class="btn btn-default btn-sm tips confirm-submit"
					>
						{icon name="move"}
					</button>
				{/if}
				{if $comments_coms|@count > 0}
					<button
						type="submit"
						form="view_forum"
						formaction="{bootstrap_modal controller=forum action=lock_topic}"
						title=":{tr}Lock{/tr}"
						class="btn btn-default btn-sm tips confirm-submit"
					>
						{icon name="lock"}
					</button>
					<button
						type="submit"
						form="view_forum"
						formaction="{bootstrap_modal controller=forum action=unlock_topic}"
						title=":{tr}Unlock{/tr}"
						class="btn btn-default btn-sm tips confirm-submit"
					>
						{icon name="unlock"}
					</button>
					<button
						type="submit"
						form="view_forum"
						formaction="{bootstrap_modal controller=forum action=delete_topic}"
						title=":{tr}Delete{/tr}"
						class="btn btn-default btn-sm tips confirm-submit"
					>
						{icon name="remove"}
					</button>
				{/if}
			</div>
			<div class="pull-right">
				{if $reported > 0}
					<a class="btn btn-default btn-sm tips" href="tiki-forums_reported.php?forumId={$forumId}" title=":{tr}Reported messages{/tr}">{tr}Reported{/tr} <span class="badge">{$reported}<span></a>
				{/if}
				{if $queued > 0}
					<a class="btn btn-default btn-sm tips" href="tiki-forum_queue.php?forumId={$forumId}" title=":{tr}Queued messages{/tr}">{tr}Queued{/tr} <span class="badge">{$queued}</span></a>
				{/if}
			</div>
		</div>
	</div>
{/if}
<form id="view_forum" method="post">
	<input type="hidden" name="comments_offset" value="{$comments_offset|escape}">
	<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}">
	<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}">
	<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}">
	<input type="hidden" name="forumId" value="{$forumId|escape}">
	{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
	<div id="{$ts_tableid}-div" class="{if $js === 'y'}table-responsive{/if} ts-wrapperdiv" {if $tsOn}style="visibility:hidden;"{/if}>
		<table id="{$ts_tableid}" class="table normal table-striped table-hover table-forum" data-count="{$comments_cant|escape}">
			{block name=forum-header}
			<thead>
				<tr>
					{$cntcol = 0}
					{if $tiki_p_admin_forum eq 'y'}
						<th id="checkbox">
							{select_all checkbox_names='forumtopic[]'}
						</th>
						{$cntcol = $cntcol + 1}
					{/if}
					<th id="type">{self_link _sort_arg='thread_sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
					{$cntcol = $cntcol + 1}
					{if $forum_info.topic_smileys eq 'y'}
						<th id="smiley">{self_link _sort_arg='thread_sort_mode' _sort_field='smiley'}{tr}Emot{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					<th id="title">{self_link _sort_arg='thread_sort_mode' _sort_field='title'}{tr}Title{/tr}{/self_link}</th>
					{$cntcol = $cntcol + 1}
					{if $forum_info.topics_list_replies eq 'y'}
						<th id="replies">{self_link _sort_arg='thread_sort_mode' _sort_field='replies'}{tr}Replies{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $forum_info.topics_list_reads eq 'y'}
						<th id="hits">{self_link _sort_arg='thread_sort_mode' _sort_field='hits'}{tr}Reads{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $forum_info.vote_threads eq 'y' and ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
						<th id="rating">{tr}Rating <br/>(avg/max){/tr}</th>
						{$cntcol = $cntcol + 1}
						{if $prefs.rating_results_detailed eq 'y' and $prefs.rating_results_detailed_percent neq 'y'}
							<th id="rating2">{tr}Detailed results <br/>(counts){/tr}</th>
							{$cntcol = $cntcol + 1}
						{elseif $prefs.rating_results_detailed eq 'y' and $prefs.rating_results_detailed_percent eq 'y'}
							<th id="rating3">{tr}Detailed results <br/>(counts/%){/tr}</th>
							{$cntcol = $cntcol + 1}
						{/if}
					{/if}
					{if $forum_info.topics_list_pts eq 'y'}
						<th id="average">{self_link _sort_arg='thread_sort_mode' _sort_field='average'}{tr}pts{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $forum_info.topics_list_lastpost eq 'y' or $forum_info.topics_list_lastpost_avatar eq 'y'}
						<th id="lastpost">{self_link _sort_arg='thread_sort_mode' _sort_field='lastPost'}{tr}Last Post{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $forum_info.topics_list_author eq 'y' or $forum_info.topics_list_author_avatar eq 'y'}
						<th id="poster">{self_link _sort_arg='thread_sort_mode' _sort_field='userName'}{tr}Author{/tr}{/self_link}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $forum_info.att_list_nb eq 'y'}
						<th id="atts">{tr}Files{/tr}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $prefs.feature_multilingual eq 'y'}
						<th id="lang">{tr}Language{/tr}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					{if $prefs.forum_category_selector_in_list eq 'y'}
						<th id="category">{tr}Category{/tr}</th>
						{$cntcol = $cntcol + 1}
					{/if}
					<th id="actions"></th>
					{$cntcol = $cntcol + 1}
				</tr>
			</thead>
			{/block}
			<tbody>
				{section name=ix loop=$comments_coms}
					{if $userinfo && $comments_coms[ix].lastPost > $userinfo.lastLogin}
						{assign var="newtopic" value="_new"}
					{else}
						{assign var="newtopic" value=""}
					{/if}
					{block name=forum-row}
					<tr>
						{if $tiki_p_admin_forum eq 'y'}
							<td class="checkbox-cell">
								<input type="checkbox" name="forumtopic[]" value="{$comments_coms[ix].threadId|escape}" {if isset($smarty.request.forumtopic) and in_array($comments_coms[ix].threadId,$smarty.request.forumtopic)}checked="checked"{/if}>
							</td>
						{/if}
						<td class="icon">
							{if $newtopic neq ''}
								{assign var=nticon value=$newtopic}
								{assign var=ntalt value="-{tr}New{/tr}"}
							{/if}
							{if $comments_coms[ix].type eq 'n'}
								{tr}Normal{/tr}
							{elseif $comments_coms[ix].type eq 'a'}
								{tr}Announce{/tr}
							{elseif $comments_coms[ix].type eq 'h'}
								{tr}Hot{/tr}
							{elseif $comments_coms[ix].type eq 's'}
								{tr}Sticky{/tr}
							{elseif $comments_coms[ix].type eq 'l'}
								{tr}Locked{/tr}
							{elseif $comments_coms[ix].type eq 'd'}
								{tr}Deliberation{/tr}
							{/if}

							{if $comments_coms[ix].locked eq 'y'}
								{icon name="lock" title=":{tr}Topic locked{/tr}" class="tips"}
							{elseif $forum_info.is_locked eq 'y'}
								{icon name="lock" title=":{tr}Forum locked{/tr}" class="tips"}
							{/if}
						</td>
						{if $forum_info.topic_smileys eq 'y'}
							<td class="icon">
								{if strlen($comments_coms[ix].smiley) > 0}
									<img src='img/smiles/{$comments_coms[ix].smiley}' alt=''>
								{else}
									&nbsp;{$comments_coms[ix].smiley}
								{/if}
							</td>
						{/if}

						<td class="text">
							{if $prefs.feature_sefurl === 'y'}{$sep = '?'}{else}{$sep = '&amp;'}{/if}
							<a {if $comments_coms[ix].is_marked}class="forumnameread"{else}class="forumname"{/if} href="{$comments_coms[ix].threadId|sefurl:'forumthread'}{$sep}topics_offset={math equation="x + y" x=$comments_offset y=$smarty.section.ix.index}{if $comments_threshold}&amp;topics_threshold={$comments_threshold}{/if}{if $thread_sort_mode ne $forum_info.topicOrdering}&amp;topics_sort_mode={$thread_sort_mode}{/if}{if isset($topics_find) and $topics_find}&amp;topics_find={$comments_find}{/if}">
								{$comments_coms[ix].title|escape}
							</a>
							{if $forum_info.topic_summary eq 'y'}
								<div class="subcomment">
									{$comments_coms[ix].summary|truncate:240:"...":true|escape}
								</div>
							{/if}
						</td>
						{if $forum_info.topics_list_replies eq 'y'}
							<td class="integer"><span class="badge">{$comments_coms[ix].replies}</span></td>
						{/if}
						{if $forum_info.topics_list_reads eq 'y'}
							<td class="integer"><span class="badge">{$comments_coms[ix].hits}</span></td>
						{/if}
						{if $forum_info.vote_threads eq 'y' and ($tiki_p_ratings_view_results eq 'y' or $tiki_p_admin eq 'y')}
							<td class="integer">{rating_result_avg type=comment id=$comments_coms[ix].threadId }&nbsp;&nbsp;&nbsp;</td>
							{if $prefs.rating_results_detailed eq 'y'}
								<td class="text">{rating_result type=comment id=$comments_coms[ix].threadId }</td>
							{/if}
						{/if}
						{if $forum_info.topics_list_pts eq 'y'}
							<td class="integer"><span class="badge">{$comments_coms[ix].average|string_format:"%.2f"}</span></td>
						{/if}
						{if $forum_info.topics_list_lastpost eq 'y'}
							<td class="text">
								{if $forum_info.topics_list_lastpost_avatar eq 'y' and $prefs.feature_userPreferences eq 'y'}
									<div style="float:left;padding-right:2px">{$comments_coms[ix].lastPostData.userName|avatarize}</div>
								{/if}
								<div style="float:left;">
									{$comments_coms[ix].lastPost|tiki_short_datetime} {* date_format:"%b %d [%H:%M]" *}
									{if $comments_coms[ix].replies}
										<br>
										<small>{if $forum_info.topics_list_lastpost_title eq 'y'}<i>{$comments_coms[ix].lastPostData.title|escape}</i> {/if}{tr}by{/tr} {$comments_coms[ix].lastPostData.userName|userlink}</small>
									{/if}
								</div>
							</td>
						{elseif $forum_info.topics_list_lastpost_avatar eq 'y' and $prefs.feature_userPreferences eq 'y'}
							<td class="text">
								{$comments_coms[ix].lastPostData.userName|avatarize}
							</td>
						{/if}
						{if $forum_info.topics_list_author eq 'y'}
							<td class="text">
								{if $forum_info.topics_list_author_avatar eq 'y' and $prefs.feature_userPreferences eq 'y'}
									<div style="float:left;padding-right:2px">
										{$comments_coms[ix].userName|avatarize}
									</div>
								{/if}
								<div style="float:left">
									{$comments_coms[ix].userName|userlink}</td>
								</div>
						{elseif $forum_info.topics_list_author_avatar eq 'y' and $prefs.feature_userPreferences eq 'y'}
							<td class="text">
								{$comments_coms[ix].userName|avatarize}
							</td>
						{/if}

						{if $forum_info.att_list_nb eq 'y'}
							<td style="text-align:center;">
								{if !empty($comments_coms[ix].nb_attachments)}<a href="tiki-view_forum_thread.php?comments_parentId={$comments_coms[ix].threadId}&amp;view_atts=y#attachments" title="{tr}Attachments{/tr}">{/if}
								<span>
									{$comments_coms[ix].nb_attachments}
								</span>
								{if !empty($comments_coms[ix].nb_attachments)}</a>{/if}
							</td>
						{/if}

						{if $prefs.feature_multilingual eq 'y'}
							<td>
								{$forum_info.forumLanguage}
							</td>
						{/if}

						{if $prefs.forum_category_selector_in_list eq 'y'}
							<td>{categoryselector type="forum post" object=$comments_coms[ix].threadId categories=$prefs.forum_available_categories}</td>
						{/if}

						<td class="text" nowrap="nowrap">
							{capture name=view_forum_actions}
								{strip}
									{if ( $tiki_p_admin_forum eq 'y' or ($comments_coms[ix].userName == $user && $tiki_p_forum_post eq 'y') ) and $forum_info.is_locked neq 'y' and $comments_coms[ix].locked neq 'y'}
										{$libeg}<a href="tiki-view_forum.php?openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;thread_sort_mode={$thread_sort_mode}&amp;comments_per_page={$comments_per_page}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
									{/if}
									{if $prefs.feature_forum_topics_archiving eq 'y' && $tiki_p_admin_forum eq 'y'}
										{if $comments_coms[ix].archived eq 'y'}
											<a href="{bootstrap_modal controller=forum action=unarchive_topic forumId={$forum_info.forumId} comments_parentId={$comments_coms[ix].threadId} comments_offset={$comments_offset} thread_sort_mode={$thread_sort_mode} comments_per_page={$comments_per_page}}">
												{icon name='file-archive-open' _menu_text='y' _menu_icon='y' alt="{tr}Unarchive{/tr}"}
											</a>{$liend}
										{else}
											<a href="{bootstrap_modal controller=forum action=archive_topic forumId={$forum_info.forumId} comments_parentId={$comments_coms[ix].threadId} comments_offset={$comments_offset} thread_sort_mode={$thread_sort_mode} comments_per_page={$comments_per_page}}">
												{icon name='file-archive' _menu_text='y' _menu_icon='y' alt="{tr}Archive{/tr}"}
											</a>{$liend}
										{/if}
									{/if}
									{if $tiki_p_admin_forum eq 'y'}
										<a href="{bootstrap_modal controller=forum action=delete_topic forumId={$forum_info.forumId} forumtopic={$comments_coms[ix].threadId} comments_offset={$comments_offset} thread_sort_mode={$thread_sort_mode} comments_per_page={$comments_per_page}}">
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
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.view_forum_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.view_forum_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
					{/block}
				{sectionelse}
					{if !$tsOn || ($tsOn && $tsAjax)}
						{norecords _colspan=$cntcol _text="{tr}No topics found{/tr}"}
					{else}
						{norecords _colspan=$cntcol _text="{tr}Retrieving topics...{/tr}"}
					{/if}
				{/section}
			</tbody>
		</table>
	</div>
</form>
{if !$tsAjax}
	{if !$tsOn}
		{pagination_links cant=$comments_cant step=$comments_per_page offset=$comments_offset offset_arg='comments_offset'}{/pagination_links}
	{/if}
	{if $forum_info.forum_last_n > 0 && count($last_comments)}
		{* Last n titles *}
		<div class="table-responsive">
		<table class="table">
			<tr>
				<th>{tr}Last{/tr} {$forum_info.forum_last_n} {tr}posts in this forum{/tr}</th>
			</tr>
			{section name=ix loop=$last_comments}
				<tr>
					<td>
						{if $last_comments[ix].parentId eq 0}
							{assign var="idt" value=$last_comments[ix].threadId}
						{else}
							{assign var="idt" value=$last_comments[ix].parentId}
						{/if}
						<a class="forumname" href="tiki-view_forum_thread.php?comments_parentId={$idt}&amp;topics_threshold={$comments_threshold}&amp;topics_offset={math equation="x + y" x=$comments_offset y=$smarty.section.ix.index}&amp;topics_sort_mode={$thread_sort_mode}&amp;topics_find={$comments_find}&amp;forumId={$forum_info.forumId}">{$last_comments[ix].title|escape}</a>
					</td>
				</tr>
			{/section}
		</table>
		</div>
		<br>
	{/if}

	{if !$tsOn}
		<div class="col-md-8" styles="padding-top:15px">
			<div class="panel panel-default" id="filter-panel">
				<div class="panel-heading filter-panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#filterCollapse" class="collapsed">
							Filter Posts
						</a>
					</h4>
				</div>
				<div id="filterCollapse" class="panel-collapse collapse">
					<div class="panel-body">
						<form id='time_control' class="form-horizontal" method="post" action="tiki-view_forum.php">
							{if $comments_offset neq 0}
								<input type="hidden" name="comments_offset" value="0"><!--reset the offset when starting a new filtered search-->
							{/if}
							{if $comments_threadId neq 0}
								<input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}">
							{/if}
							{if $comments_threshold neq 0}
								<input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}">
							{/if}
							<input type="hidden" name="thread_sort_mode" value="{$thread_sort_mode|escape}">
							<input type="hidden" name="forumId" value="{$forumId|escape}">
							<div class="form-group">
								<label class="col-md-4 control-label input-sm" for="filter_time">{tr}Last post date{/tr}</label>
								<div class="col-md-8">
									<select id="filter_time" name="time_control" class="form-control input-sm">
										<option value="" {if $smarty.request.time_control eq ''}selected="selected"{/if}>{tr}All posts{/tr}</option>
										<option value="3600" {if $smarty.request.time_control eq 3600}selected="selected"{/if}>{tr}Last hour{/tr}</option>
										<option value="86400" {if $smarty.request.time_control eq 86400}selected="selected"{/if}>{tr}Last 24 hours{/tr}</option>
										<option value="172800" {if $smarty.request.time_control eq 172800}selected="selected"{/if}>{tr}Last 48 hours{/tr}</option>
									</select>
								</div>
							</div>
							{if $prefs.feature_forum_topics_archiving eq 'y'}
								<div class="form-group">
									<label class="col-md-4 control-label input-sm" for="show_archived">Show archived posts</label>
									<div class="col-md-8">
										<input type="checkbox" id="show_archived" name="show_archived" {if $show_archived eq 'y'}checked="checked"{/if}>
									</div>
								</div>
							{/if}
							{if $user}
								<div class="form-group">
									<label class="col-md-4 control-label input-sm" for="filter_poster">Containing posts by</label>
									<div class="col-md-8">
										<select id="filter_poster" class="form-control input-sm" name="poster">
											<option value=""{if empty($smarty.request.poster)} selected="selected"{/if}>
												All posts
											</option>
											<option value="_me" {if isset($smarty.request.poster) and $smarty.request.poster eq '_me'} selected="selected"{/if}>
												Me
											</option>
										</select>
									</div>
								</div>
							{/if}
							<div class="form-group">
								<label class="col-md-4 control-label input-sm" for="filter_type">Type</label>
								<div class="col-md-8">
									<select id="filter_type" name="filter_type" class="form-control input-sm">
										<option value=""{if empty($smarty.request.filter_type)}selected="selected"{/if}>
											{tr}All posts{/tr}
										</option>
										<option value="n" {if isset($smarty.request.filter_type) and $smarty.request.filter_type eq 'n'} selected="selected"{/if}>
											{tr}normal{/tr}
										</option>
										<option value="a" {if isset($smarty.request.filter_type) and $smarty.request.filter_type eq 'a'} selected="selected"{/if}>
											{tr}announce{/tr}
										</option>
										<option value="h"{if isset($smarty.request.filter_type) and $smarty.request.filter_type eq 'h'} selected="selected"{/if}>
											{tr}hot{/tr}
										</option>
										<option value="s"{if isset($smarty.request.filter_type) and $smarty.request.filter_type eq 's'} selected="selected"{/if}>
											{tr}sticky{/tr}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label input-sm" for="filter_replies">Replies</label>
								<div class="col-md-8">
									<select id="filter_replies" name="reply_state" class="form-control input-sm">
										<option value=""{if empty($smarty.request.reply_state)} selected="selected"{/if}>
											{tr}All posts{/tr}
										</option>
										<option value="none"{if isset($smarty.request.reply_state) and $smarty.request.reply_state eq 'none'} selected="selected"{/if}>
											{tr}Posts with no replies{/tr}
										</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-4">
									<input type="submit" class="btn btn-default btn-sm" id="filter_submit" value="{tr}Filter{/tr}">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	{/if}

	{if empty($user) and $prefs.javascript_enabled eq "y"}
		{jq}
			var js_anonymous_name = getCookie('anonymous_name');
			if (js_anonymous_name) document.getElementById('anonymous_name').value = js_anonymous_name;
		{/jq}
	{/if}
	{jq}
		var $forum = $("#editpageform");
		$forum.submit(function() {
			// prevent double submission
			if (!$forum.data("sub")) {
				$forum.tikiModal('Save in Progress...');
				$forum.data("sub", true);
				$forum.submit();
			}
		});
	{/jq}
{/if}
