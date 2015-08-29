{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<div class="friend-container" data-controller="social" data-action="list_friends">
<ul class="friend-list clearfix">
	{foreach from=$friends item=friend}
		<li>
			{$friend.user|userlink}
			{* Show 'X' remove buttons only when on active user's profile *}
			{if $user eq $username}
				<a class="pull-right remove-friend tips" href="{service controller=social action=remove_friend friend=$friend.user}" data-confirm="{tr _0=$friend.user}Do you really want to remove %0?{/tr}" title=":{tr}Remove Friend{/tr}">{icon name='delete'}</a>
			{/if}
		</li>
	{foreachelse}
		<li>{tr}No friends have been added.{/tr}</li>
	{/foreach}
</ul>
{if $incoming|count > 0}
	<p>{tr}Incoming requests:{/tr}
	<ul class="request-list clearfix">
		{foreach from=$incoming item=candidate}
			<li>
				{$candidate.user|userlink}
				<a class="pull-right remove-friend tips" href="{service controller=social action=remove_friend friend=$candidate.user}" data-confirm="{tr _0=$candidate.user}Do you really want to remove %0?{/tr}" title=":{tr}Remove Friend{/tr}">{icon name='delete'}</a>
				<a class="pull-right add-friend tips" href="{service controller=social action=add_friend username=$candidate.user}" title=":{tr}Accept and add{/tr}">{icon name='add'}</a>
				{if $prefs.social_network_type eq 'follow_approval'}
					<a class="pull-right approve-friend tips" href="{service controller=social action=approve_friend friend=$candidate.user}" title="{tr}Accept Request{/tr}">{icon name='ok'}</a>
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
{if $outgoing|count > 0}
	<p>{tr}Still waiting for approval:{/tr}
	<ul class="request-list clearfix">
		{foreach from=$outgoing item=candidate}
			<li>
				{$candidate.user|userlink}
				<a class="pull-right remove-friend tips" href="{service controller=social action=remove_friend friend=$candidate.user}" data-confirm="{tr _0=$candidate.user}Do you really want to cancel request for %0?{/tr}" title=":{tr}Cancel{/tr}">{icon name='delete'}</a>
			</li>
		{/foreach}
	</ul>
{/if}
{if $showbutton eq 'y'}
    <button class="add-friend btn btn-default"><i class="fa fa-user-plus"></i>&nbsp;{tr}Add Friend{/tr}</button>
{/if}
</div>
{/block}
