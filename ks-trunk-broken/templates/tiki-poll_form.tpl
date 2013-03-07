<h2>{tr}Vote poll:{/tr}</h2>
<div align="center">
{if $menu_info['active'] == 'x'}
	{remarksbox type="warning" title="{tr}Warning{/tr}"}
		{tr}This poll is closed{/tr}
	{/remarksbox}
{else}
	<div class="cbox">
	<div class="cbox-data">
	{include file='tiki-poll.tpl'}
	</div>
	</div>
	<div><a href="tiki-old_polls.php" class="link">{tr}Other Polls{/tr}</a></div>
{/if}
</div>


