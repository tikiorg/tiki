{* $Id: mod-users_own_rank.tpl xxxxx 2010-03-28 omstefanov $ *}

{if $prefs.feature_score eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}My score{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="users_own_rank" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

<div style="display: inline">{tr _0=$position _1=$count}%0 out of %1 users.{/tr}&nbsp;</div>
<div style="display: inline-block">
	<div style="display: inline">&nbsp;{$score}</div>
	<div style="display: inline">&nbsp;{$user|userlink}</div>
</div>


{/tikimodule}
{/if}
