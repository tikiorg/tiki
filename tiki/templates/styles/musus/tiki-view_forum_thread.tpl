{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-view_forum_thread.tpl,v 1.4 2004-01-16 13:10:42 musus Exp $ *}
<a 
	class="pagetitle" 
	title="" 
	href="tiki-view_forum.php?topics_offset={$smarty.request.topics_offset}&amp;
	topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
	topics_threshold={$smarty.request.topics_threshold}&amp;
	topics_find={$smarty.request.topics_find}&amp;
	forumId={$forum_info.forumId}">
	{tr}Forum{/tr}:&nbsp;{$forum_info.name}
</a>

<br /><br />

{if $unread > 0}
	<a 
		title="" 
		href="messu-mailbox.php">
		{tr}You have{/tr}&nbsp;{$unread}&nbsp;{tr}unread private messages{/tr}
	</a>
	<br /><br />
{/if}

{if $was_queued eq 'y'}
	<div class="wikitext">
		<em>{tr}Your message has been queued for approval, the message will be posted after a moderator approves it.{/tr}</em>
	</div>
{/if}

<a 
	title="" 
	href="tiki-forums.php">
	{tr}Tiki forums{/tr}
</a>-&gt;
<a 
	title="" 
	href="tiki-view_forum.php?forumId={$forumId}">
	{$forum_info.name}
</a>->
<a 
	title="" 
	href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
	topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
	topics_threshold={$smarty.request.topics_threshold}&amp;
	topics_find={$smarty.request.topics_find}&amp;
	forumId={$forumId}&amp;
	comments_parentId={$smarty.request.comments_parentId}">
	{$thread_info.title}
</a>

[{if $prev_topic}
	<a 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$topics_prev_offset}&amp;
		topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
		topics_threshold={$smarty.request.topics_threshold}&amp;
		topics_find={$smarty.request.topics_find}&amp;
		forumId={$forumId}&amp;comments_parentId={$prev_topic}">
		{tr}prev topic{/tr}
	</a>
	{if $next_topic}|{/if}
{/if}
{if $next_topic}
	<a 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$topics_next_offset}&amp;
		topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
		topics_threshold={$smarty.request.topics_threshold}&amp;
		topics_find={$smarty.request.topics_find}&amp;
		forumId={$forumId}&amp;
		comments_parentId={$next_topic}">
		{tr}next topic{/tr}
	</a>
{/if}
]

<br /><br />

{if $openpost eq 'y'}
	{assign var="postclass" value="forumpostopen"}
	{else}
	{assign var="postclass" value="forumpost"}
{/if}

<table summary="">
	<tbody>
		<tr>
			<td>
				{if $forum_info.ui_avatar eq 'y'}
					{$thread_info.userName|avatarize}<br />
				{/if}
				{$thread_info.userName|userlink}
				{if $forum_info.ui_flag eq 'y'}
					<br />{$thread_info.userName|countryflag}
				{/if}
				{if $thread_info.userName and $forum_info.ui_posts eq 'y'}
					<br /><em>posts:{$thread_info.user_posts}</em>
				{/if}
				{if $thread_info.userName and $forum_info.ui_level eq 'y'}
					<br /><img src="img/icons/{$thread_info.user_level}stars.gif" alt="{$thread_info.user_level} {tr}stars{/tr}" />
				{/if}
			</td>
			<td>
				<table summary="{$thread_info.title}">
					<tbody>
						<tr>
							<td><strong>{$thread_info.title}</strong></td>
							<td style="text-align:right;">
	{if $tiki_p_admin_forum eq 'y' or ($tiki_p_forum_post eq 'y' and ($thread_info.userName == $user)) }
		<a 
			title="{$editIconTitle}" 
			href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}&amp;
			comments_sort_mode={$smarty.request.topics_sort_mode}&amp;
			comments_threshold={$smarty.request.topics_threshold}&amp;
			comments_find={$smarty.request.topics_find}&amp;
			comments_threadId={$thread_info.threadId}&amp;
			openpost=1&amp;
			forumId={$forum_info.forumId}&amp;
			comments_maxComments={$comments_maxComments}">
			{$editIcon $editIconDesc}
		</a>
	{/if}
	{if $tiki_p_admin_forum eq 'y'}
		<a 
			title="{$deleteIconTitle}" 
			href="tiki-view_forum.php?comments_offset={$smarty.request.topics_offset}&amp;
			comments_sort_mode={$smarty.request.topics_sort_mode}&amp;
			comments_threshold={$smarty.request.topics_threshold}&amp;
			comments_find={$smarty.request.topics_find}&amp;
			comments_remove=1&amp;
			comments_threadId={$thread_info.threadId}&amp;
			forumId={$forum_info.forumId}&amp;
			comments_maxComments={$comments_maxComments}">
			{$deleteIcon $deleteIconDesc}
		</a>
	{/if}
	{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a 
			title="{$saveIconTitle}" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
			topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
			topics_threshold={$smarty.request.topics_threshold}&amp;
			topics_find={$smarty.request.topics_find}&amp;
			comments_parentId={$comments_parentId}&amp;
			forumId={$forumId}&amp;
			comments_threshold={$comments_threshold}&amp;
			comments_offset={$comments_offset}&amp;
			comments_sort_mode={$comments_sort_mode}&amp;
			comments_maxComments={$comments_maxComments}&amp;
			savenotepad={$thread_info.threadId}">
			{$saveIcon $saveIconDesc}
		</a>
	{/if}
	{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_topic eq 'n'}
			<a 
				title="{$monitorIconTitle}" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				forumId={$forumId}&amp;
				comments_parentId={$comments_parentId}&amp;
				watch_event=forum_post_thread&amp;
				watch_object={$comments_parentId}&amp;
				watch_action=add">
				{$monitorIcon $monitorIconDesc}
			</a>
		{else}
			<a 
				title="{$monitorStopIconTitle}" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				forumId={$forumId}&amp;
				comments_parentId={$comments_parentId}&amp;
				watch_event=forum_post_thread&amp;
				watch_object={$comments_parentId}&amp;
				watch_action=remove">
				{$monitorStopIcon $monitorStopIconDesc}
			</a>
		{/if}
	{/if}
							</td>
						</tr>
					</tbody>
				</table>
				<br /><br />

				{$thread_info.parsed}<br />

{if count($thread_info.attachments) > 0}
	{section name=ix loop=$thread_info.attachments}
		<a 
			title="{$attachmentIconTitle}" 
			href="tiki-download_forum_attachment.php?attId={$thread_info.attachments[ix].attId}">
			{$attachmentIcon $attachmentIconDesc}&nbsp;
			{$thread_info.attachments[ix].filename}&nbsp;
			({$thread_info.attachments[ix].filesize|kbsize})
		</a>
		{if $tiki_p_admin_forum eq 'y'}
			<a 
				title="{$deleteMiniIconTitle}" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				comments_offset={$smarty.request.topics_offset}&amp;
				comments_sort_mode={$smarty.request.topics_sort_mode}&amp;
				comments_threshold={$smarty.request.topics_threshold}&amp;
				comments_find={$smarty.request.topics_find}&amp;
				forumId={$forum_info.forumId}&amp;
				comments_maxComments={$comments_maxComments}&amp;
				comments_parentId={$comments_parentId}&amp;
				remove_attachment={$thread_info.attachments[ix].attId}">
				{$deleteMiniIcon $deleteMiniIconDesc}
			</a>
		{/if}
		<br />
	{/section}
{/if}
			</td>
		</tr>
		<tr>
			<td>&nbsp;
				{if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
					<a 
						class="admlink" 
						title="" 
						href="messu-compose.php?to={$thread_info.userName}&amp;
						subject=Re:{$thread_info.title}">
						<img src="img/icons/myinfo.gif" alt="{tr}private message{/tr}" />
					</a>
				{/if}
				{if $thread_info.userName and $forum_info.ui_email eq 'y' and strlen($thread_info.user_email) > 0}
					<a 
						title="" 
						href="mailto:{$thread_info.user_email|escape:'hex'}">
						<img src="img/icons/email.gif" alt="{tr}send email to user{/tr}" />
					</a>
				{/if}
				{if $thread_info.userName and $forum_info.ui_online eq 'y'}
					{if $thread_info.user_online eq 'y'}
						<img src='img/icons/online.gif' alt='{tr}user online{/tr}' />
					{else}
						<img src='img/icons/offline.gif' alt='{tr}user offline{/tr}' />
					{/if}
				{/if}
			</td>
			<td>
				<table class="commentinfo" summary="">
	<colgroup>
		<col />
		<col />{if $forum_info.vote_threads eq 'y'}<col />{/if}
		{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}<col />
	</colgroup>
		<tbody>
			<tr>
				<td>
					<strong>{tr}on{/tr}:</strong>&nbsp;
					{$thread_info.commentDate|tiki_short_datetime}
				</td>
{if $forum_info.vote_threads eq 'y'}
	<td>
		<strong>{tr}score{/tr}:</strong>&nbsp;
		{$thread_info.points}
	</td>
	{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}
		<td>
			<strong>{tr}Vote{/tr}:</strong>
			<a 
				title="" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				comments_parentId={$comments_parentId}&amp;
				forumId={$forum_info.forumId}&amp;
				comments_threshold={$comments_threshold}&amp;
				comments_threadId={$thread_info.threadId}&amp;
				comments_vote=1&amp;
				comments_offset={$comments_offset}&amp;
				comments_sort_mode={$comments_sort_mode}&amp;
				comments_maxComments={$comments_maxComments}&amp;
				comments_parentId={$comments_parentId}">
				1
			</a>
			<a 
				title="" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				comments_parentId={$comments_parentId}&amp;
				forumId={$forum_info.forumId}&amp;
				comments_threshold={$comments_threshold}&amp;
				comments_threadId={$thread_info.threadId}&amp;
				comments_vote=2&amp;
				comments_offset={$comments_offset}&amp;
				comments_sort_mode={$comments_sort_mode}&amp;
				comments_maxComments={$comments_maxComments}&amp;
				comments_parentId={$comments_parentId}">
				2
			</a>
			<a 
				title="" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				comments_parentId={$comments_parentId}&amp;
				forumId={$forum_info.forumId}&amp;
				comments_threshold={$comments_threshold}&amp;
				comments_threadId={$thread_info.threadId}&amp;
				comments_vote=3&amp;
				comments_offset={$comments_offset}&amp;
				comments_sort_mode={$comments_sort_mode}&amp;
				comments_maxComments={$comments_maxComments}&amp;
				comments_parentId={$comments_parentId}">
				3
			</a>
			<a 
				title="" 
				href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;
				topics_sort_mode={$smarty.request.topics_sort_mode}&amp;
				topics_threshold={$smarty.request.topics_threshold}&amp;
				topics_find={$smarty.request.topics_find}&amp;
				comments_parentId={$comments_parentId}&amp;
				forumId={$forum_info.forumId}&amp;
				comments_threshold={$comments_threshold}&amp;
				comments_threadId={$thread_info.threadId}&amp;
				comments_vote=4&amp;
				comments_offset={$comments_offset}&amp;
				comments_sort_mode={$comments_sort_mode}&amp;
				comments_maxComments={$comments_maxComments}&amp;
				comments_parentId={$comments_parentId}">
				4
			</a>
			<a 
				title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$thread_info.threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			5
		</a>
		</td>
	{/if}
{/if}
		<td>{tr}reads{/tr}:&nbsp;{$thread_info.hits}</td>
			</tr>
		</tbody>
				</table>

      </td>
    </tr>
  </tbody>
</table>

<br />

{if $tiki_p_admin_form eq 'y' or $thread_info.type ne 'l'}
{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_post eq 'y'}

<input class="button" type="button" name="comments_postComment" value="{tr}new reply{/tr}" onclick="show('{$postclass}');"/>

{if $comment_preview eq 'y'}
<br /><br />

<strong>{tr}Preview{/tr}</strong>

<div class="commentscomment">
  <div class="commentheader">
    <table summary="">
      <tbody>
        <tr>
          <td>
            <div class="commentheader">
              <div class="commentstitle">{$comments_preview_title}</div>
              <br />{tr}by{/tr} {$user}
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="commenttext">
    {$comments_preview_data}<br />
  </div>
</div>
{/if}

<div id="{$postclass}" class="threadpost">
  <br />
  {if $comments_threadId > 0}
    {tr}Editing comment{/tr}: {$comments_threadId} (<a 
			class="forumbutlink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$smarty.request.comments_parentId}&amp;forumId={$forumId}&amp;comments_threadId=0&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
			{tr}post new comment{/tr}
		</a>)
  {/if}
  <form enctype="multipart/form-data" method="post" action="tiki-view_forum_thread.php" id="editpageform">
    <fieldset>
      <legend>{tr}Comments{/tr}</legend>
      <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
      <input type="hidden" name="quote" value="{$quote|escape}" />
      <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
      <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
      <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
      <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode|escape}" />
      <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
      <input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
      <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />
      <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />
      <input type="hidden" name="forumId" value="{$forumId|escape}" />

      <table summary="">
        <tbody>
          <tr>
            <td>{tr}Title{/tr}</td>
            <td><input class="text" type="text" name="comments_title" value="{$comment_title|escape}" /></td>
          </tr>
          {if $forum_info.forum_use_password eq 'a'}
          <tr>
            <td class="forumform">{tr}Password{/tr}</td>
            <td class="forumform"><input type="password" name="password" /></td>
          </tr>
          {/if}
          {if $feature_forum_parse eq 'y'}
          <tr>
            <td>{tr}Quicklinks{/tr}</td>
            <td>
              {assign var="area_name" value="editpost"}
              {include file="tiki-edit_help_tool.tpl"}
            </td>
          </tr>
          {/if}
          {if $feature_smileys eq 'y'}
          <tr>
            <td>{tr}Smileys{/tr}</td>
            <td>{assign var="area_name" value="editpost"}{include file="tiki-smileys.tpl" area_name="editpost"}</td>
          </tr>
          {/if}
          <tr>
            <td>
              {tr}Comment{/tr}
              <br /><br />
              {include file="textareasize.tpl" area_name="editpost" formId="editpageform"}
            </td>
            <td>
              <textarea class="textarea" id="editpost" name="comments_data" rows="{$rows}" cols="{$cols}">{$comment_data|escape}</textarea>
              <input type="hidden" name="rows" value="{$rows}" />
              <input type="hidden" name="cols" value="{$cols}" />
            </td>
          </tr>
          {if ($forum_info.att eq 'att_all') or ($forum_info.att eq 'att_admin' and $tiki_p_admin_form eq 'y') or ($forum_info.att eq 'att_perm' and $tiki_p_forum_attach eq 'y')}
          <tr>
            <td>{tr}Attach file{/tr}</td>
            <td>
              <input type="hidden" name="MAX_FILE_SIZE" value="{$forum_info.att_max_size|escape}" />
              <input name="userfile1" type="file" />
            </td>
          </tr>
          {/if}
          <tr>
            <td>{tr}Post{/tr}</td>
            <td>
              <input class="submit" type="submit" name="comments_previewComment" value="{tr}preview{/tr}" />
              <input class="submit" type="submit" name="comments_postComment" value="{tr}post{/tr}" />
              <input class="button" type="button" name="comments_postComment" value="{tr}cancel{/tr}" onclick="hide('{$postclass}');" />
            </td>
          </tr>
        </tbody>
      </table>
    </fieldset>
  </form>

  <br />

  <div class="commentsedithelp">
    <strong>{tr}Posting comments{/tr}:</strong>
    <br /><br />
    {tr}Use{/tr} [http://www.foo.com] {tr}or{/tr} [http://www.foo.com|description] {tr}for links{/tr}<br />
    {tr}HTML tags are not allowed inside comments{/tr}
  </div>
  <br />
</div>

<br /><br />
{/if}
{/if}

{if $replies_cant > 0}
{* <!-- TOOLBAR --> *}
<div class="forumtoolbar">
  <form method="post" action="tiki-view_forum_thread.php">
    <fieldset>
      <legend>{tr}Comments{/tr}</legend>
      <input type="hidden" name="forumId" value="{$forum_info.forumId|escape}" />
      <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
      <input type="hidden" name="comments_offset" value="0" />
      <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
      <input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
      <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />
      <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />

      <table summary="">
          <tr>
            <td>
              {tr}Comments{/tr}
              <select name="comments_maxComments">
                <option value="10" {if $comments_maxComments eq 10 }selected="selected"{/if}>10</option>
                <option value="20" {if $comments_maxComments eq 20 }selected="selected"{/if}>20</option>
                <option value="30" {if $comments_maxComments eq 30 }selected="selected"{/if}>30</option>
                <option value="999999" {if $comments_maxComments eq 999999 }selected="selected"{/if}>All</option>
              </select>
            </td>
            <td>
              {tr}Sort{/tr}
              <select name="comments_sort_mode">
                <option value="commentDate_desc" {if $comments_sort_mode eq 'commentDate_desc'}selected="selected"{/if}>{tr}Date{/tr}</option>
                <option value="points_desc" {if $comments_sort_mode eq 'points_desc'}selected="selected"{/if}>{tr}Score{/tr}</option>
                <option value="title_desc" {if $comments_sort_mode eq 'title_desc'}selected="selected"{/if}>{tr}Title{/tr}</option>
              </select>
            </td>
            <td>
              {tr}Threshold{/tr}
              <select name="comments_threshold">
                <option value="0" {if $comments_threshold eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
                <option value="0.01" {if $comments_threshold > '0.01'}selected="selected"{/if}>0</option>
                <option value="1" {if $comments_threshold eq 1}selected="selected"{/if}>1</option>
                <option value="2" {if $comments_threshold eq 2}selected="selected"{/if}>2</option>
                <option value="3" {if $comments_threshold eq 3}selected="selected"{/if}>3</option>
                <option value="4" {if $comments_threshold eq 4}selected="selected"{/if}>4</option>
              </select>
            </td>
            <td>
              {tr}Search{/tr}
              <input class="text" type="text" size="7" name="comments_commentFind" value="{$comments_commentFind|escape}" />
            </td>
            <td><input type="submit" name="comments_setOptions" value="{tr}set{/tr}" /></td>
            <td>
              &nbsp;
		<a 
			class="toolbarlink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset=0&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
			{tr}Top{/tr}
		</a>
            </td>
          </tr>
      </table>
    </fieldset>
  </form>
</div>
{* <!-- TOOLBAR ENDS --> *}

<form method="post" action="tiki-view_forum_thread.php">
  <fieldset>
    <legend>{tr}Moderator actions{/tr}</legend>
    <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
    <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
    <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
    <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
    <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode|escape}" />
    <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
    <input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
    <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />
    <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />
    <input type="hidden" name="forumId" value="{$forumId|escape}" />

    {if $tiki_p_admin_forum eq 'y'}
    <table summary="{tr}Moderator actions{/tr}">
      <thead>
        <tr>
          <td colspan="3">{tr}Moderator actions{/tr}</td>
        </tr>
      </thead>
      </tbody>
        <tr>
          <td><input type="submit" name="delsel" value="{tr}delete selected{/tr}" /></td>
          <td>
            {tr}Move to topic:{/tr}
            <select name="moveto">
              {section name=ix loop=$topics}
              {if $topics[ix].threadId ne $comments_parentId}
              <option value="{$topics[ix].threadId|escape}">{$topics[ix].title}</option>
              {/if}
              {/section}
            </select>
            <input class="submit" type="submit" name="movesel" value="{tr}move{/tr}" />
          </td>
          <td>
            {if $reported > 0}
	<em><a 
			title="" 
			href="tiki-forums_reported.php?forumId={$forumId}">
			{tr}reported:{/tr}&nbsp;{$reported}
	</a> | </em>
            {/if}
            <em><a 
			title="" 
			href="tiki-forum_queue.php?forumId={$forumId}">
			{tr}queued:{/tr}{$queued}
	</a></em>
          </td>
        </tr>
      </tbody>
    </table>
    {/if}
    {/if}

    <table summary="">
      <thead>
        <tr>
          <th>{tr}author{/tr}</th>
          <th>{tr}message{/tr}</th>
        </tr>
      </thead>
      <tbody>
        {cycle values="odd,even" print=false}
        {section name=ix loop=$comments_coms}
        <tr>
          <td class="{cycle advance=false}">
            {if $forum_info.ui_avatar eq 'y'}
              {$comments_coms[ix].userName|avatarize}
              <br />
            {/if}
            <br />
            {$comments_coms[ix].userName|userlink}
            {if $forum_info.ui_flag eq 'y'}<br />{$comments_coms[ix].userName|countryflag}{/if}
            {if $comments_coms[ix].userName and $forum_info.ui_posts eq 'y'}
              <br /><em>posts:&nbsp;{$comments_coms[ix].user_posts}</em>
            {/if}
            {if $comments_coms[ix].userName and $forum_info.ui_level eq 'y'}
              <br /><img src="img/icons/{$comments_coms[ix].user_level}stars.gif" alt="{$comments_coms[ix].user_level} {tr}stars{/tr}" />
            {/if}
          </td>
        <td class="{cycle advance=false}">

          <table summary="">
            <tbody>
              <tr>
                <td><strong>{$comments_coms[ix].title}</strong></td>
                <td style="text-align:right;">
                  {if $tiki_p_admin_forum eq 'y'}
                  <input type="checkbox" name="forumthread[]" value="{$comments_coms[ix].threadId|escape}" {if $smarty.request.forumthread and in_array($comments_coms[ix].threadId,$smarty.request.forumthread)}checked="checked"{/if} />
                  {/if}
                  {if $tiki_p_admin_forum eq 'y' or ($tiki_p_forum_post eq 'y' and ($comments_coms[ix].userName == $user)) }
		<a 
			class="admlink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;openpost=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
                    <img src="img/icons/edit.gif" alt="{tr}edit{/tr}" />
		</a>
		<a 
			class="admlink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;comments_remove=1&amp;comments_threadId={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
                    <img src="img/icons2/delete.gif" alt="{tr}remove{/tr}" />
		</a>
                  {/if}
		<a 
			class="admlink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;quote={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
                    <img src="img/icons/linkto.gif" alt="{tr}reply{/tr}" />
		</a>
                  {if $comments_coms[ix].is_reported}
                  <img src="img/icons2/warning.gif" alt="{tr}this post was reported{/tr}" />
                  {else}
                  {if $tiki_p_forums_report eq 'y'}
		<a 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;report={$comments_coms[ix].threadId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
                    <img src="img/icons2/1.gif" alt="{tr}report this post{/tr}" />
		</a>
                  {/if}
                  {/if}
                  {if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a 
			title="{tr}Save to notepad{/tr}" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;savenotepad={$comments_coms[ix].threadId}">
                    <img src="img/icons/ico_save.gif" alt="{tr}save{/tr}" />
		</a>
                  {/if}
                </td>
              </tr>
            </tbody>
          </table>

          <br /><br />
          {$comments_coms[ix].parsed}
          <br />

          {if count($comments_coms[ix].attachments) > 0}
          {section name=iz loop=$comments_coms[ix].attachments}
	<a 
		title="" 
		href="tiki-download_forum_attachment.php?attId={$comments_coms[ix].attachments[iz].attId}">
            <img src="img/icons/attachment.gif" alt="{tr}attachment{/tr}" />
            {$comments_coms[ix].attachments[iz].filename} ({$comments_coms[ix].attachments[iz].filesize|kbsize})
	</a>
          {if $tiki_p_admin_forum eq 'y'}
	<a 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_find={$smarty.request.topics_find}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;comments_offset={$smarty.request.topics_offset}&amp;comments_sort_mode={$smarty.request.topics_sort_mode}&amp;comments_threshold={$smarty.request.topics_threshold}&amp;comments_find={$smarty.request.topics_find}&amp;forumId={$forum_info.forumId}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}&amp;remove_attachment={$comments_coms[ix].attachments[iz].attId}">
            [
            {tr}del{/tr}
            ]
	</a>
          {/if}
          <br />
          {/section}
          {/if}
        </td>
      </tr>
      <tr>
        <td class="{cycle advance=false}">
          &nbsp;
          {if $feature_messages eq 'y' and $tiki_p_messages eq 'y'}
	<a 
		class="admlink" 
		title="" 
		href="messu-compose.php?to={$comments_coms[ix].userName}&amp;subject=Re:{$comments_coms[ix].title}">
            <img src="img/icons/myinfo.gif" alt="{tr}private message{/tr}" />
	</a>
          {/if}
          {if $comments_coms[ix].userName and $forum_info.ui_email eq 'y' and strlen($comments_coms[ix].user_email) > 0}
	<a 
		title="" 
		href="mailto:{$comments_coms[ix].user_email|escape:'hex'}">
            <img src="img/icons/email.gif" alt="{tr}send email to user{/tr}" />
	</a>
          {/if}
          {if $comments_coms[ix].userName and $forum_info.ui_online eq 'y' }
          {if $comments_coms[ix].user_online eq 'y'}
          <img src="img/icons/online.gif" alt="{tr}user online{/tr}" />
          {else}
          <img src="img/icons/offline.gif" alt="{tr}user offline{/tr}" />
          {/if}
          {/if}
        </td>
        <td class="{cycle}">

          <table class="commentinfo">
            <colgroup><col />{if $forum_info.vote_threads eq 'y'}<col />{/if}{if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}<col />{/if}</colgroup>
            <thead>
              <tr>
                <td>{tr}on{/tr}: {$comments_coms[ix].commentDate|tiki_short_datetime}</td>
                {if $forum_info.vote_threads eq 'y'}
                <td><strong>{tr}score{/tr}:</strong> {$comments_coms[ix].points}</td>
                {if $tiki_p_admin_forum eq 'y' or $tiki_p_forum_vote eq 'y'}
                <td>
                  <strong>{tr}Vote{/tr}:</strong>
                  <a 
			class="forumvotelink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=1&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			1
		</a>
                  <a 
			class="forumvotelink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=2&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			2
		</a>
                  <a 
			class="forumvotelink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=3&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			3
		</a>
                  <a 
			class="forumvotelink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=4&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			4
		</a>
                  <a 
			class="forumvotelink" 
			title="" 
			href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_threadId={$comments_coms[ix].threadId}&amp;comments_vote=5&amp;comments_offset={$comments_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}&amp;comments_parentId={$comments_parentId}">
			5
		</a>
                </td>
                {/if}
                {/if}
              </tr>
            </tbody>
          </table>

        </td>
      </tr>
      <tr>
        <td colspan="2" class="threadseparator"></td>
      </tr>
      {/section}
      {if $replies_cant > 0}
    </tbody>
  </table>
  {/if}
</form>

<br />

<div align="center" class="mini">
    {if $comments_prev_offset >= 0}
    [
	<a 
		class="prevnext" 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_prev_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
		{tr}prev{/tr}
	</a>
    ]
    &nbsp;
    {/if}
    {tr}Page{/tr}: {$comments_actual_page}/{$comments_cant_pages}
    {if $comments_next_offset >= 0}
    &nbsp;
    [
	<a 
		class="prevnext" 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$comments_next_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
		{tr}next{/tr}
	</a>
    ]
    {/if}
    {if $direct_pagination eq 'y'}
    <br />
    {section loop="$comments_cant_pages" name="foo"}
    {assign var="selector_offset" value="$smarty.section.foo.index|times:$comments_maxComments"}
	<a 
		class="prevnext" 
		title="" 
		href="tiki-view_forum_thread.php?topics_offset={$smarty.request.topics_offset}&amp;topics_sort_mode={$smarty.request.topics_sort_mode}&amp;topics_threshold={$smarty.request.topics_threshold}&amp;topics_find={$smarty.request.topics_find}&amp;comments_parentId={$comments_parentId}&amp;forumId={$forum_info.forumId}&amp;comments_threshold={$comments_threshold}&amp;comments_offset={$selector_offset}&amp;comments_sort_mode={$comments_sort_mode}&amp;comments_maxComments={$comments_maxComments}">
		{$smarty.section.foo.index_next}
	</a>
    &nbsp;
    {/section}
    {/if}
</div>
<br />

<em>{$comments_below} {tr}Comments below your current threshold{/tr}</em>

<table summary="{tr}Comments below your current threshold{/tr}">
  <tbody>
    <tr>
      <td>
        <form id="time_control" method="post" action="tiki-view_forum_thread.php">
          <fieldset>
            <legend>{tr}Comments below your current threshold{/tr}</legend>
            <input type="hidden" name="comments_offset" value="{$comments_offset|escape}" />
            <input type="hidden" name="comments_threadId" value="{$comments_threadId|escape}" />
            <input type="hidden" name="comments_parentId" value="{$comments_parentId|escape}" />
            <input type="hidden" name="comments_threshold" value="{$comments_threshold|escape}" />
            <input type="hidden" name="comments_sort_mode" value="{$comments_sort_mode|escape}" />
            <input type="hidden" name="topics_offset" value="{$smarty.request.topics_offset|escape}" />
            <input type="hidden" name="topics_find" value="{$smarty.request.topics_find|escape}" />
            <input type="hidden" name="topics_sort_mode" value="{$smarty.request.topics_sort_mode|escape}" />
            <input type="hidden" name="topics_threshold" value="{$smarty.request.topics_threshold|escape}" />
            <input type="hidden" name="forumId" value="{$forumId|escape}" />
            <em>{tr}Show posts{/tr}:</em>
            <select name="time_control" onchange="javascript:document.getElementById('time_control').submit();">
              <option value="" {if $smarty.request.time_control eq ''}selected="selected"{/if}>{tr}All posts{/tr}</option>
              <option value="3600" {if $smarty.request.time_control eq 3600}selected="selected"{/if}>{tr}Last hour{/tr}</option>
              <option value="86400" {if $smarty.request.time_control eq 86400}selected="selected"{/if}>{tr}Last 24 hours{/tr}</option>
              <option value="172800" {if $smarty.request.time_control eq 172800}selected="selected"{/if}>{tr}Last 48 hours{/tr}</option>
            </select>
          </fieldset>
        </form>
      </td>
      <td>
        {if $feature_forum_quickjump eq 'y'}
        <form id="quick" method="post" action="tiki-view_forum.php">
          <fieldset>
            <legend>{tr}Jump to forum{/tr}</legend>
            <em>{tr}Jump to forum{/tr}:</em>
            <select name="forumId" onchange="javascript:document.getElementById('quick').submit();">
              {section name=ix loop=$all_forums}
              <option value="{$all_forums[ix].forumId|escape}" {if $all_forums[ix].forumId eq $forumId}selected="selected"{/if}>{$all_forums[ix].name}</option>
              {/section}
            </select>
          </fieldset>
        </form>
        {else}
        &nbsp;
        {/if}
      </td>
    </tr>
  </tbody>
</table>
