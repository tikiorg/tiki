{* $Id$ *}
{if empty($module_params.silent)}
	{tikimodule error=$module_params.error title=$tpl_module_title name="who_is_there" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $count}
		{if $cluster}
			{foreach from=$logged_cluster_users item=cant key=tikihost}
				<div>
					{$cant}
					{if $cant>1}
						{tr}online users{/tr}
					{elseif $cant>0}
						{tr}online user{/tr}
					{/if}
					{tr}on host{/tr} {$tikihost}
				</div>
			{/foreach}
		{else}
			<div>
				{$logged_users}
				{if $logged_users>1}
					{tr}online users{/tr}
				{elseif $logged_users>0}
					{tr}online user{/tr}
				{/if}
			</div>
		{/if}
	{/if}

	{if $list}
		<ul>
			{foreach key=ix item=online_user from=$online_users}
				<li>
					{if $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
						{if $online_user.allowMsgs eq 'n'}
							{icon name='envelope' title="{tr}User does not accept messages{/tr}" class='icon'}
						{else}
							{icon name='envelope' title="{tr}Send a message to{/tr}" href='messu-compose.php?to='|cat:$online_user.user class='icon'}
						{/if}
					{/if}

					{if $online_user.user_information eq 'public'}
						{math equation="x - y" x=$smarty.now y=$online_user.timestamp assign=idle}
						{$online_user.user|userlink:"linkmodule":$idle}
					{else}
						{$online_user.user|escape}
					{/if}

					{if $cluster}<br>({$online_user.tikihost}){/if}
				</li>
			{/foreach}
		</ul>
	{/if}
	{/tikimodule}
{/if}
