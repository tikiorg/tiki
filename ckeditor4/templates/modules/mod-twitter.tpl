{* $Id$ *}

{if $prefs.feature_socialnetworks eq 'y'}
	{tikimodule error=$module_params.error title=$tpl_module_title name="last_tweets" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{modules_list list=$timeline nonums=$nonums rows=$rows}
		{section name=ix loop=$timeline}
			<li>
				{if $module_params.showuser eq 'y'}<span class="TwitName">{$timeline[ix].screen_name}</span>{/if}
				<span class="TwitText">{$timeline[ix].text}</span><br>
				<span class="TwitDate"><a href="http://twitter.com/#!/{$timeline[ix].screen_name}/status/{$timeline[ix].id}">{$timeline[ix].created_at}</a></span>
			</li>
		{/section}
	{/modules_list}
	{/tikimodule}
{/if}
