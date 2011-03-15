{strip}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<small>[ {tr}Execution time:{/tr} {elapsed} {tr}secs{/tr} ] &nbsp; [ {tr}Memory usage:{/tr} {memusage} ] &nbsp; [ {$num_queries} {tr}database queries used in {/tr} {$elapsed_in_db|truncate:3:''} {tr}secs{/tr} ]{if $server_load and $server_load ne '?'} &nbsp; [ {tr}Server load:{/tr} {$server_load} ]{/if}</small>
{/tikimodule}
{/strip}
