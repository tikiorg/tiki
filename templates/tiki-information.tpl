{* $Id$ *}
<div id="tiki-center">
<br />
<div class="cbox">
	 <div class="cbox-title">
	 {tr}Information{/tr}
	 </div>

	<div class="simplebox highlight">
	{if is_array($msg)}
		{foreach from=$msg item=line}
	 		{$line|escape}<br />
	 	{/foreach}
	{else}
		{$msg|escape}
	{/if}
	</div>

	<p>

	{if $show_history_back_link eq 'y'}
		<a href="javascript:history.back()" class="linkmenu">{tr}Go back{/tr}</a><br /><br />
	{/if}
	 &nbsp;<a href="{$prefs.tikiIndex}" class="linkmenu">{tr}Return to home page{/tr}</a>
	</p>
</div>
</div>
