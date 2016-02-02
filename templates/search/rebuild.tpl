{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{if $prefs.unified_last_rebuild}
		<div class="alert alert-warning">
			<p>{tr _0=$prefs.unified_last_rebuild|tiki_long_datetime}Your index was last fully rebuilt on %0.{/tr}</p>
		</div>
	{/if}

	{if !empty($stat)}
		{remarksbox type='feedback' title="{tr}Indexed{/tr}"}
			<ul>
				{foreach from=$stat key=what item=nb}
					<li>{$what|escape}: {$nb|escape}</li>
				{/foreach}
			</ul>
		{/remarksbox}
	{else}
		<form method="post" class="no-ajax" action="{service controller=search action=rebuild}" onsubmit="$(this).parent().tikiModal('{tr}Rebuilding index...{/tr}')">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="loggit" value="1">
						{tr}Enable logging{/tr}
					</label>
					<div class="help-block">{tr}Log file is saved as temp/Search_Indexer.log{/tr}</div>
				</div>
			</div>
			<div class="form-group submit">
				<input type="submit" class="btn btn-primary" value="{tr}Rebuild{/tr}">
				{if $queue_count > 0}
					<a class="btn btn-default" href="{service controller=search action=process_queue}">{tr}Process Queue{/tr} <span class="badge">{$queue_count|escape}</span></a>
				{/if}
			</div>
		</form>

		{* If the indexing succeeded, there are clearly no problems, free up some screen space *}
		{remarksbox type=tip title="{tr}Indexing Problems?{/tr}"}
			<p>{tr}If the indexing does not complete, check the log file to see where it ended.{/tr}</p>
			<p>{tr}Last line of log file (web):{/tr} <strong>{$lastLogItemWeb|escape}</strong></p>
			<p>{tr}Last line of log file (console):{/tr} <strong>{$lastLogItemConsole|escape}</strong></p>

			<p>Common failures include:</p>
			<ul>
				<li><strong>{tr}Not enough memory.{/tr}</strong> Larger sites require more memory to re-index.</li>
				<li><strong>{tr}Time limit too short.{/tr}</strong> It may be required to run the rebuild through the command line.</li>
				<li><strong>{tr}High resource usage.{/tr}</strong> Some plugins in your pages may cause excessive load. Blacklisting some plugins during indexing can help.</li>
			</ul>
		{/remarksbox}
	{/if}
	{remarksbox type=tip title="{tr}Command Line Utilities{/tr}"}
		<kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:optimize</kbd><br>
		<kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:rebuild</kbd><br>
		<kbd>php console.php{if not empty($tikidomain)} --site={$tikidomain|replace:'/':''}{/if} index:rebuild --log</kbd><br>
		<p>{tr}Log file is saved as temp/Search_Indexer_console.log{/tr}</p>
	{/remarksbox}
{/block}
