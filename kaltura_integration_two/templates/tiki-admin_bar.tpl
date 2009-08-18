{if $prefs.feature_magic eq 'y' and $tiki_p_admin eq 'y'}
<div id="adminBar">
	<ul class="topLevelAdmin">
	{section name=top loop=$toplevelfeatures}
		<li{if $toplevel eq $toplevelfeatures[top].feature_id} class="current"{/if}><a href="tiki-magic.php?featurechain={$toplevelfeatures[top].feature_path}">{tr}{$toplevelfeatures[top].feature_name}{/tr}</a></li>
	{/section}
	{if $feature.feature_count > 0 && $templatename != 'tiki-magic'}
		<li class="configureThis"><a href="tiki-magic.php?featurechain={$feature.feature_path}">{tr}Configure{/tr} {tr}{$feature.feature_name}{/tr}</a></li>
	{/if}
	</ul>
	{if $secondlevel}
	<ul class="secondLevelAdmin">
		{section name=sec loop=$secondlevel}
			{if ($secondlevel[sec].feature_type eq 'feature' && $secondlevel[sec].value eq 'y' && $secondlevel[sec].feature_count > 0) || $secondlevel[sec].feature_type neq 'feature'}
		<li{if $secondlevelId eq $secondlevel[sec].feature_id} class="current"{/if}><a href="tiki-magic.php?featurechain={$secondlevel[sec].feature_path}">{tr}{$secondlevel[sec].feature_name}{/tr}</a></li>	
			{/if}
		{/section}
	</ul>
	{/if}
	{if $thirdlevel}
	<ul class="thirdLevelAdmin">
		{section name=sec loop=$thirdlevel}
			{if ($thirdlevel[sec].feature_type eq 'feature' && $thirdlevel[sec].value eq 'y' && $thirdlevel[sec].feature_count > 0) || $thirdlevel[sec].feature_type neq 'feature'}
		<li{if $thirdlevelId eq $thirdlevel[sec].feature_id} class="current"{/if}><a href="tiki-magic.php?featurechain={$thirdlevel[sec].feature_path}">{tr}{$thirdlevel[sec].feature_name}{/tr}</a></li>	
			{/if}
		{/section}
	</ul>
	{/if}
</div>
{/if}
