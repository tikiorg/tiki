{assign var="default_diff_style" value="inlinediff-full"}

{if $show_translation_module}

	{tikimodule error=$module_params.error title=$tpl_module_title name="translation" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if $trads|@count eq '1'}<p>{tr}There are no translations of this page.{/tr}<p>{/if}
			{if $prefs.quantify_changes eq 'y'}
				<div>
					{tr}Up-to-date-ness:{/tr} {$mod_translation_quantification}%
				</div>
				{$mod_translation_gauge}
			{/if}
			{if $mod_translation_better_known or $mod_translation_better_other}
				<div>			
					{if $from_edit_page ne 'y'}
						<b>{tr}Incoming:{/tr}</b>
					{else}
						{tr}To <strong>continue translating</strong>, select the language to translate from:{/tr}
					{/if}			
					{if $mod_translation_better_known}
						<ul>
							{foreach item=better from=$mod_translation_better_known}
								<li>
									{if $from_edit_page eq 'y'}
										<a title="{tr}update from it{/tr}" href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_from alt="{tr}update from it{/tr}" style="vertical-align:middle"} {$better.lang|langname}</a> ({$better.page|escape})
									{else}
										{if $tiki_p_edit eq 'y'}
											<a href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_from alt="{tr}update from it{/tr}" style="vertical-align:middle"}</a>
									{/if}
									<a href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style={$default_diff_style}" title="{$better.page|escape}">					
									{if $show_language eq 'y'} 
										{$better.lang|langname}</a> 
									{else}
										{$better.page|escape}</a> ({$better.lang})
									{/if}
									{/if}
								</li>
							{/foreach}
						</ul>
					{elseif $prefs.change_language eq 'y'}{* only show if users can set a preferred language *}
						<div id="mod-translation-better-intro" style="display:block">{tr}None match your{/tr} <a href="tiki-user_preferences.php" title="{tr}Set your preferred languages.{/tr}">{tr}preferred languages{/tr}</a>.</div>
					{/if} {* $mod_translation_better_known *}
					
					{if $mod_translation_better_other}
						{if $prefs.change_language eq 'y'}
							<a href="javascript:void(0)" onclick="intro=document.getElementById('mod-translation-better-intro');if(intro)intro.style.display='none';document.getElementById('mod-translation-better-ul').style.display='block';this.style.display='none'" class="linkmenu more">{icon _id=plus_small alt="{tr}More...{/tr}" width="11" height="8" style="vertical-align:middle;border:0"} {tr}More...{/tr}</a>
						{/if}
						<ul id="mod-translation-better-ul"{if $prefs.change_language eq 'y'} style="display:none"{/if}>
							{foreach item=better from=$mod_translation_better_other}
								<li>
									{if $from_edit_page eq 'y'}
										<a title="{tr}update from it{/tr}" href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_from alt="{tr}update from it{/tr}" style="vertical-align:middle"} {$better.lang|langname}</a> ({$better.page|escape})
									{else}
										{if $tiki_p_edit eq 'y'}
											<a href="tiki-editpage.php?page={if isset($stagingPageName) && $hasStaging == 'y'}{$stagingPageName|escape:'url'}{else}{$page|escape:'url'}{/if}&amp;source_page={$better.page|escape:'url'}&amp;oldver={$better.last_update|escape:'url'}&amp;newver={$better.current_version|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_from alt="{tr}update from it{/tr}" style="vertical-align:middle"}</a>
										{/if}
										<a href="tiki-index.php?page={$better.page|escape:'url'}&amp;no_bl=y">{icon _id=page alt="{tr}view{/tr}" style="vertical-align:middle"}</a>
										<a href="tiki-index.php?page={$better.page|escape:'url'}&amp;no_bl=y" title="{$better.page|escape}">
										{if $show_language eq 'y'}
											{$better.lang|langname}</a> 
										{else}
											{$better.page|escape}</a> ({$better.lang})
										{/if}
									{/if}
								</li>
							{/foreach}
						</ul>
					{/if}
				</div><br />
			{/if}
			
			{if $mod_translation_worst_known or $mod_translation_worst_other}
				<div>			
					<b>{tr}Outgoing:{/tr}</b>
					{if $mod_translation_worst_known}
					<ul>
						{foreach item=worst from=$mod_translation_worst_known}
						<li>
							{if $tiki_p_edit eq 'y'}
								<a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_to alt="{tr}update it{/tr}" style="vertical-align:middle"}</a>
							{/if}
							<a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style={$default_diff_style}" title="{$worst.page|escape}">
							{if $show_language eq 'y'}
							{$worst.lang|langname}</a> 
							{else}
							{$worst.page|escape}</a> ({$worst.lang})
							{/if}
						</li>
						{/foreach}
					</ul>
					{elseif $prefs.change_language eq 'y'}
						<div id="mod-translation-worst-intro" style="display:block">{tr}None match your{/tr} <a href="tiki-user_preferences.php">{tr}preferred languages{/tr}</a>.</div>
					{/if}
					{if $mod_translation_worst_other}
		{if $prefs.change_language eq 'y'}
					<a href="javascript:void(0)" onclick="intro=document.getElementById('mod-translation-worst-intro');if(intro)intro.style.display='none';document.getElementById('mod-translation-worst-ul').style.display='block';this.style.display='none'" class="linkmenu more">{icon _id=plus_small alt="{tr}More...{/tr}" width="11" height="8" style="vertical-align:middle;border:0"}{tr}More...{/tr}</a>
		{/if}
					<ul id="mod-translation-worst-ul"{if $prefs.change_language eq 'y'} style="display:none"{/if}>
						{foreach item=worst from=$mod_translation_worst_other}
						<li>
							{if $tiki_p_edit eq 'y'}
								<a href="tiki-editpage.php?page={$worst.page|escape:'url'}&amp;source_page={$page|escape:'url'}&amp;oldver={$worst.last_update|escape:'url'}&amp;newver={$pageVersion|escape:'url'}&amp;diff_style={$default_diff_style}">{icon _id=page_translate_to alt="{tr}update it{/tr}" style="vertical-align:middle"}</a>
							{/if}
							<a href="tiki-index.php?page={$worst.page|escape:'url'}&amp;no_bl=y">{icon _id=page alt="{tr}view{/tr}" style="vertical-align:middle"}</a>
							<a href="tiki-index.php?page={$worst.page|escape:'url'}&amp;no_bl=y" title="{$worst.page|escape}">
							{if $show_language eq 'y'}
							{$worst.lang|langname}</a> 
							{else}
							{$worst.page|escape}</a> ({$worst.lang})
							{/if}
						</li>
						{/foreach}
					</ul>
					{/if}
				</div><br />
			{/if}
		
	{/tikimodule}
{/if}
