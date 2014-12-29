{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="directory_stats" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}

	{tr}Sites:{/tr} {$modDirStats.valid}<br>
	{tr}Sites to validate:{/tr} {$modDirStats.invalid}<br>
	{tr}Categories:{/tr} {$modDirStats.categs}<br>
	{tr}Searches:{/tr} {$modDirStats.searches}<br>
	{tr}Visited links:{/tr} {$modDirStats.visits}<br>

{/tikimodule}
