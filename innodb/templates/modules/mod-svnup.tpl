{* $Id$ *}
{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if !empty($lastup)}
		<div class="cvsup" style="font-size:x-small;text-align:center;color:#999;">{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
	{/if}
	{if !empty($svnrev)}
		 - REV {$svnrev}
	{/if}
	{if !empty($lastup) or !empty($svnrev)}
		 </div>
	{/if}
{/tikimodule}
{/strip}