{* $Id$ *}
{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{capture assign="lastup"}{svn_lastup}{/capture}
	{capture assign="svnrev"}{svn_rev}{/capture}
	{if !empty($lastup)}
		<div class="cvsup">{tr}Last update from SVN{/tr} ({$tiki_version}): {$lastup|tiki_long_datetime}
		{if !empty($svnrev)}
			- REV {$svnrev}
		{/if}
		</div>
	{else}
		{tr}No Subversion checkout or unable to determine last update{/tr}
	{/if}
{/tikimodule}
{/strip}
