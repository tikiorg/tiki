{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="users_rank" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if !empty($users_rank)}
		{modules_list list=$users_rank nonums=$nonums}
			{section loop=$users_rank name=u}
				<li>
					{*<div class="licomponent" style="display:inline">{$users_rank[u].position})&nbsp;</div>*}
					<div class="licomponent" style="display:inline">{$users_rank[u].score}</div>
					<div class="licomponent" style="display:inline">&nbsp;{$users_rank[u].login|userlink}</div>
				</li>
			{/section}
		{/modules_list}
	{/if}

{/tikimodule}
