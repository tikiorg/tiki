{* $Id$ *}

<div class="t_navbar margin-bottom-md btn-group">
	{button class="btn btn-default" href="messu-mailbox.php" _class="btn btn-default" _text="{tr}Mailbox{/tr}"}
	{button class="btn btn-default" href="messu-compose.php" _class="btn btn-default" _text="{tr}Compose{/tr}"}

	{if $tiki_p_broadcast eq 'y'}
		{button class="btn btn-default" href="messu-broadcast.php" _class="btn btn-default" _text="{tr}Broadcast{/tr}"}
	{/if}

	{button class="btn btn-default" href="messu-sent.php" _class="btn btn-default" _text="{tr}Sent{/tr}"}
	{button class="btn btn-default" href="messu-archive.php" _class="btn btn-default" _text="{tr}Archive{/tr}"}

	{if $mess_archiveAfter>0}
		({tr}Auto-archive age for read messages:{/tr} {$mess_archiveAfter} {tr}days{/tr})
	{/if}
</div>
