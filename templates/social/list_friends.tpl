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
			<a class="pull-right remove-friend" href="{service controller=social action=remove_friend friend=$friend.user}" data-confirm="{tr _0=$friend.user}Do you really want to remove %0?{/tr}">{icon _id=cross alt="{tr}Remove Friend{/tr}"}</a>
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
				<a class="pull-right remove-friend" href="{service controller=social action=remove_friend friend=$candidate.user}" data-confirm="{tr _0=$candidate.user}Do you really want to remove %0?{/tr}">{icon _id=cross alt="{tr}Reject{/tr}"}</a>
				<a class="pull-right add-friend" href="{service controller=social action=add_friend username=$candidate.user}">{icon _id=add alt="{tr}Accept &amp; Add{/tr}"}</a>
				{if $prefs.social_network_type eq 'follow_approval'}
					<a class="pull-right approve-friend" href="{service controller=social action=approve_friend friend=$candidate.user}">{icon _id=accept alt="{tr}Accept Request{/tr}"}</a>
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
				<a class="pull-right remove-friend" href="{service controller=social action=remove_friend friend=$candidate.user}" data-confirm="{tr _0=$candidate.user}Do you really want to cancel request for %0?{/tr}">{icon _id=cross alt="{tr}Cancel{/tr}"}</a>
			</li>
		{/foreach}
	</ul>
{/if}
<button class="add-friend btn btn-default">{tr}Add Friend{/tr}</button>
</div>
{/block}
