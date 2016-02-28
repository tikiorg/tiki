{* $Id$ *}

{tikimodule error="{if isset($module_params.error)}{$module_params.error}{/if}" title=$tpl_module_title name="last_visitors" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if ($nonums eq 'y') or ($showavatars eq 'y')}<ul style="padding-left:0; list-style:none;">{else}<ol>{/if}
		{if !$user}
			<li>
				{if $showavatars eq 'y'}
					<table class="table">
						<tr class="odd">
							<td width="50">
								<img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No profile picture{/tr}">
							</td>
							<td>
				{/if}
				{if $prefs.allowRegister eq 'y'}
							<a class="linkmodule" href="tiki-register.php{if !empty($prefs.registerKey)}?key={$prefs.registerKey|escape:'url'}{/if}" title="{tr}Register{/tr}">{/if}{tr}You{/tr}{if $prefs.allowRegister eq 'y'}</a>
				{/if}
							<div align="right">{$currentLogin|tiki_short_datetime}</div>
				{if $showavatars eq 'y'}
							</td>
						</tr>
					</table>
				{/if}
			</li>
		{/if}

		{capture assign='noAvatar'}<img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No profile picture{/tr}">{/capture}
		{foreach from=$modLastVisitors key=key item=item}
			<li>
				{if $showavatars eq 'y'}
					<table class="table">
						<tr class="{cycle advance=true}">
							<td width="50">
								{$item.user|avatarize|default:$noAvatar}
							</td>
						<td>
				{/if}
				{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
					{$ustring = "{$item.user|userlink:'userlink':'not_set':'':$maxlen}"}
				{else}
					{$ustring = "{$item.user|userlink}"}
				{/if}
				{if $ustring|substring:0:2 == '<a'}
					{$ustring}
				{else}
					<a class="tips" href="tiki-user_information.php?view_user={$item.user|escape:"url"}" title="{tr}User:{/tr}{$item.user}">
						{$ustring}
					</a>
				{/if}
				{if $nodate neq 'y'}
					{if $item.currentLogin}
						<div class="date">{$item.currentLogin|tiki_short_datetime}</div>
					{else}
						<div class="date">{tr}Never logged in{/tr}</div>
					{/if}
				{/if}
				{if $showavatars eq 'y'}
							</td>
						</tr>
					</table>
				{/if}
			</li>
		{/foreach}
	{if ($nonums eq 'y') or ($showavatars eq 'y')}</ul>{else}</ol>{/if}
{/tikimodule}
