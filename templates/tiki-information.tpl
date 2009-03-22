<!-- START of {$smarty.template} -->{* $Id$ *}
<div id="tiki-center">
<br />
<div class="cbox">
	 <div class="cbox-title">
	 {tr}Information{/tr}
	 </div>

	 <div class="simplebox highlight">
	 {$msg|escape}
	 </div>

	<p>

	{if $show_history_back_link eq 'y' }
		<a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
	{/if}
	 <a href="{$prefs.tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
	</p>
</div>
</div><!-- END of {$smarty.template} -->
