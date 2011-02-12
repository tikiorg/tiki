{* $Id$ *}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $prefs.mobile_mode neq "y"}
		{self_link mobile_mode="y"}{$module_params.to_label}{/self_link}
	{elseif empty($mobile_params.switch_perspective) or $mobile_params.switch_perspective eq "y"}
		{self_link mobile_mode="n" perspective=$mobile_params.switch_perspective back=$mobile_params.stay_on_same_page _script="tiki-switch_perspective.php"}
			{$module_params.from_label}
		{/self_link}
	{else}
		{self_link mobile_mode=n}{$module_params.from_label}{/self_link}
	{/if}
{/tikimodule}
