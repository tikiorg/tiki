{activityframe activity=$activity heading="
<div class='active forum'><hr/></div>
<img class='commentimg' src='addons/tikiorg_organicgrp/img/chat-bubble1.png' alt='event'/>
{if isset($activity.aggregate.user) && $activity.aggregate.user|count > 1}
    {foreach $activity.aggregate.user as $t_user}
        {if $t_user@last} and {/if}{$t_user|userlink}{if !$t_user@last}, {/if}
    {/foreach}
{else}
    {$activity.user|userlink}
{/if}
{tr}has replied to a topic{/tr} {if $activity.topictitle|escape}({$activity.topictitle|escape}){/if} {tr}in{/tr}
<a href=tiki-view_forum.php?forumId={$activity.forum_id|escape}>{$activity.object|forumname|addongroupname}</a>"}

	<div class="active_part2">
		<img src="addons/tikiorg_organicgrp/img/chat-bubble-copy.png" alt="forum"/>
		<div class="avt_title1">
			{*ifsearchexists type="forum post" id="{$activity.object|escape}"*}
				<a href="tiki-view_forum_thread.php?comments_parentId={$activity.object|escape}">{if !$activity.title}{tr}Reply to{/tr} {$activity.topictitle|escape}{/if}{$activity.title|escape}</a> <br/>
			{$activity.content|truncate:80}<br/>
			{*/ifsearchexists*}
			{* If forum deepindexing is off, this ifsearchnotexists/ifsearchexists do not work for replies
			{ifsearchnotexists type="forum post" id="{$activity.object|escape}"}
			{$activity.title|escape} (deleted)
			{/ifsearchnotexists}*}
		</div>
	</div>
{/activityframe}
