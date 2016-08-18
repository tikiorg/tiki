{* $Id$ *}
{strip}
	{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		<div class="power">
			{if !isset($module_params.tiki) or $module_params.tiki neq 'n'}
				{tr}Powered by{/tr} <a href="http://tiki.org" title="&#169; 2002&#8211;{$smarty.now|date_format:"%Y"} {tr}The Tiki Community{/tr}">{tr}Tiki Wiki CMS Groupware{/tr} </a>
			{/if}
            {if !isset($module_params.version) or $module_params.version neq 'n'}
                v{$tiki_version} {if $tiki_uses_svn eq 'y'} (SVN){/if} &quot;{$tiki_star}&quot;
            {/if}
			{if !isset($module_params.credits) or $module_params.credits neq 'n'}
				<span id="credits">
					&nbsp;| {include file='credits.tpl'}
				</span>
			{/if}
		</div>
		{if !isset($module_params.icons) or $module_params.icons neq 'n'}
			<div class="power_icons">
				<a href="http://tiki.org/" title="Tiki"><img alt="{tr}Powered by{/tr} Tiki" src="img/tiki/tikibutton2.png"></a>
				<a href="http://php.net/" title="PHP"><img alt="{tr}Powered by{/tr} PHP" src="img/poweredby/php.png"></a>
				<a href="http://smarty.net/" title="Smarty"><img alt="{tr}Powered by{/tr} Smarty" src="img/poweredby/smarty.png"></a>
			</div>
		{/if}
	{/tikimodule}
{/strip}
