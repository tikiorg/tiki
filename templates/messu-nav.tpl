{* $Id$ *}

<div class="t_navbar btn-group">
	{button class="btn btn-default" href="messu-mailbox.php" class="btn btn-default" _text="{tr}Mailbox{/tr}"}
	{button class="btn btn-default" href="messu-compose.php" class="btn btn-default" _text="{tr}Compose{/tr}"}

	{if $tiki_p_broadcast eq 'y'}
		{button class="btn btn-default" href="messu-broadcast.php" class="btn btn-default" _text="{tr}Broadcast{/tr}"}
	{/if}

	{button class="btn btn-default" href="messu-sent.php" class="btn btn-default" _text="{tr}Sent{/tr}"}
	{button class="btn btn-default" href="messu-archive.php" class="btn btn-default" _text="{tr}Archive{/tr}"}

	{if $mess_archiveAfter>0}
		({tr}Auto-archive age for read messages:{/tr} {$mess_archiveAfter} {tr}days{/tr})
	{/if}
</div>
