{tikimodule error=$module_params.error title=$tpl_module_title name="last_visitors" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if ($nonums eq 'y') or ($showavatars eq 'y')}<ul style="padding-left:0; list-style:none;">{else}<ol>{/if}
		{if !$user}
			<li>
				{if $showavatars eq 'y'}
					<table class="admin">
						<tr class="odd">
							<td width="50">
								<img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No avatar{/tr}" />
							</td>
							<td>
				{/if}
				{if $prefs.allowRegister eq 'y'}
							<a class="linkmodule" href="tiki-register.php" title="{tr}Register{/tr}">{/if}{tr}You{/tr}{if $prefs.allowRegister eq 'y'}</a>
				{/if}
							<div align="right">{$currentLogin|tiki_short_datetime}</div>
				{if $showavatars eq 'y'}
							</td>
						</tr>
					</table>
				{/if}
			</li>
		{/if}
		{cycle values="even,odd" print=false}
		{capture assign='noAvatar'}<img src="img/icons/gradient.gif" width="48" height="48" alt="{tr}No avatar{/tr}" />{/capture}
		{foreach from=$modLastVisitors key=key item=item}
			<li>
				{if $showavatars eq 'y'}
					<table class="admin">
						<tr class="{cycle advance=true}">
							<td width="50">
								{$item.user|avatarize|default:$noAvatar}
							</td>
						<td>
				{/if}
				<a class="linkmodule" href="tiki-user_information.php?view_user={$item.user|escape:"url"}">
					{if $maxlen > 0}{* 0 is default value for maxlen eq to 'no truncate' *}
						{$item.user|userlink:'link':'not_set':'':$maxlen}
					{else}
						{$item.user|userlink}
					{/if}
				</a>
				{if $nodate neq 'y'}
					<div class="date">{$item.currentLogin|tiki_short_datetime}</div>
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
