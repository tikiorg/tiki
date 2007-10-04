{if $prefs.feature_bot_bar_debug eq 'y'}
<div id="loadstats">
[ {tr}Execution time{/tr}: {elapsed} {tr}secs{/tr} ] &nbsp; 
[ {tr}Memory usage{/tr}: {memusage} ] &nbsp; 
[ {$num_queries} {tr}database queries used{/tr} ] &nbsp; 
[ GZIP {$gzip} ] &nbsp; 
[ {tr}Server load{/tr}: {$server_load} ]
</div>
{/if}