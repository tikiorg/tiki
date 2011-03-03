<div class="navbar">
	{button href="messu-mailbox.php" _text="{tr}Mailbox{/tr}"}
	{button href="messu-compose.php" _text="{tr}Compose{/tr}"}

	{if $tiki_p_broadcast eq 'y'}
		{button  href="messu-broadcast.php" _text="{tr}Broadcast{/tr}"}
	{/if}

	{button href="messu-sent.php" _text="{tr}Sent{/tr}"}
	{button href="messu-archive.php" _text="{tr}Archive{/tr}"}

	{if $mess_archiveAfter>0}
		({tr}Auto-archive age for read messages:{/tr} {$mess_archiveAfter} {tr}days{/tr})
	{/if}
</div>
