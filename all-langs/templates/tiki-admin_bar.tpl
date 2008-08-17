{if $tiki_p_admin eq 'y'}
<div id="adminBar">
<ul class="topLevelAdmin">
{section name=top loop=$toplevelfeatures}
	<li{if $toplevel eq $toplevelfeatures[top].feature_id} class="current"{/if}><a href="tiki-magic.php?featurechain={$toplevelfeatures[top].feature_path}">{tr}{$toplevelfeatures[top].feature_name}{/tr}</a></li>
{/section}
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
</div>
{/if}
