{if $missing}
	<p>
		{tr}Missing dependency. Obtain <a href="http://tiki.org/tiki-download_file.php?fileId=50">visorFreemind.swf</a> and upload it in files/.{/tr}
	</p>
{else}
	<h1 class="pagetitle">{$page|escape}</h1>
	{$mindmap}
{/if}
