{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Please see the <a class='alert-link' target='tikihelp' href='http://dev.tiki.org/Performance'>Performance page</a> on Tiki's developer site.{/tr}{/remarksbox}

<form class="admin form-horizontal" id="performance" name="performance" action="tiki-admin.php?page=performance" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="performance" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>

	{tabset}

		{tab name="{tr}Performance{/tr}"}
			<h2>{tr}Performance{/tr}</h2>
			{preference name=tiki_minify_javascript}
			<div class="adminoptionboxchild" id="tiki_minify_javascript_childcontainer">
				{preference name=tiki_minify_late_js_files}
			</div>
			{preference name=javascript_cdn}
			{preference name=tiki_cdn}
			{preference name=tiki_cdn_ssl}
			{preference name=tiki_minify_css}
			<div class="adminoptionboxchild" id="tiki_minify_css_childcontainer">
				{preference name=tiki_minify_css_single_file}
			</div>
			{preference name=feature_obzip}
			<div class="adminoptionboxchild">
				{if $gzip_handler ne 'none'}
					<div class="highlight" style="margin-left:30px;">
						{tr}Output compression is active.{/tr}
						<br>
						{tr}Compression is handled by:{/tr} {$gzip_handler}.
					</div>
				{/if}
			</div>
			{preference name=tiki_cachecontrol_session}
			{preference name=smarty_compilation}
			{preference name=users_serve_avatar_static}

			<fieldset>
				<legend>{tr}PHP Settings{/tr}</legend>
				<p>{tr}Some PHP.INI settings that can increase performance{/tr}</p>
				<div class="adminoptionboxchild">
					<p>
						{tr _0=$realpath_cache_size_ini}'realpath_cache_size setting': %0{/tr}
						{tr _0=$realpath_cache_size_percent}(percentage used %0 %{/tr})
						{help url="php.ini#Performance"
							desc="realpath_cache_size : {tr}Determines the size of the realpath cache to be used by PHP.{/tr}"}
					</p>
					<p>{tr _0=$realpath_cache_ttl}'realpath_cache_ttl setting': %0 seconds{/tr}
					{help url="php.ini#Performance"
					desc="realpath_cache_ttl : {tr}Duration of time (in seconds) for which to cache realpath information for a given file or directory.{/tr}"}
				</div>
			</fieldset>
		{/tab}

		{tab name="{tr}Bytecode Cache{/tr}"}
			<h2>{tr}Bytecode Cache{/tr}</h2>
			{if $opcode_cache}
				<p>{tr _0=$opcode_cache}Using <strong>%0</strong>. These stats affect all PHP applications running on the server.{/tr}</p>

				{if $opcode_stats.warning_xcache_blocked}
					<p>{tr}Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache.{/tr}</p>
				{/if}

				<p>
					<table style="width=520px;border: 0px;text-align:center">
						<tr>
							<td><img src="{$memory_graph|escape}" width="250" height="100"></td>
							<td><img src="{$hits_graph|escape}" width="250" height="100"></td>
						</tr>
						<tr>
							<td style="width=260px">
								{tr}Memory Used{/tr}: {$opcode_stats.memory_used * 100}% - {tr}Available{/tr}: {$opcode_stats.memory_avail * 100}%
							</td>
							<td style="width=260px">
								{tr}Cache Hits{/tr}: {$opcode_stats.hit_hit * 100}% - {tr}Misses{/tr}: {$opcode_stats.hit_miss * 100}%
							</td>
						</tr>
					</table>
					<hr>
				</p>

				{if $opcode_stats.warning_fresh}
					<p>{tr}Few hits recorded. Statistics may not be representative.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_ratio}
					<p>{tr _0=$opcode_cache}Low hit ratio. %0 may be misconfigured and not used.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_starve}
					<p>{tr}Little memory available. Thrashing likely to occur.{/tr} {tr}The values to increase are apc.shm_size (for APC), xcache.size (for XCache) or opcache.memory_consumption (for OPcache).{/tr}</p>
				{/if}

				{if $opcode_stats.warning_low}
					<p>{tr _0=$opcode_cache}Small amount of memory allocated to %0. Verify the configuration.{/tr} {tr}The values to increase are apc.shm_size (for APC), xcache.size (for XCache) or opcache.memory_consumption (for OPcache).{/tr}</p>
				{/if}

				{if $opcode_stats.warning_check}
					<p>
						{tr _0=$stat_flag}Configuration <em>%0</em> is enabled. Disabling modification checks can improve performance, but will require manual clear on file updates.{/tr}
						{if $opcode_stats.warning_xcache_blocked}
							{tr _0=$stat_flag}<em>%0</em> should not be disabled due to authentication on XCache.{/tr}
						{/if}
					</p>
				{/if}
				{if $opcode_stats.warning_check}
					<p>{tr}Clear all APC caches:{/tr} {self_link apc_clear=true}{tr}Clear Caches{/tr}{/self_link}</p>
				{/if}
			{else}
				{tr}Bytecode cache is not used. Using a bytecode cache (OPcache, APC, XCache, WinCache) is highly recommended for production environments.{/tr}
			{/if}
		{/tab}

		{tab name="{tr}Wiki{/tr}"}
			<h2>{tr}Wiki{/tr}</h2>
			{preference name=wiki_cache}
			{preference name=feature_wiki_icache}
			{preference name=wiki_ranking_reload_probability}
		{/tab}

		{tab name="{tr}Database{/tr}"}
			<h2>{tr}Database{/tr}</h2>
			{preference name=log_sql}
			<div class="adminoptionboxchild" id="log_sql_childcontainer">
				{preference name=log_sql_perf_min}
			</div>
		{/tab}

		{tab name="{tr}Memcache{/tr}"}
			<h2>{tr}Memcache{/tr}</h2>
			{preference name=memcache_enabled}
			<div class="adminoptionboxchild" id="memcache_enabled_childcontainer">
				{preference name=memcache_compress}
				{preference name=memcache_prefix}
				{preference name=memcache_expiration}
				{preference name=memcache_servers}
				{preference name=memcache_wiki_data}
				{preference name=memcache_wiki_output}
				{preference name=memcache_forum_output}
			</div>
		{/tab}

		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			{preference name=wikiplugin_snarf_cache}
		{/tab}

		{tab name="{tr}Major slowdown{/tr}"}
			<h2>{tr}Major slowdown{/tr}</h2>
			{remarksbox type="note" title="{tr}Major slowdown{/tr}"}
				{tr}These are reported to slow down Tiki. If you have a high-volume site, you may want to deactivate them{/tr}
			{/remarksbox}
			{preference name=wikiplugin_sharethis}
			{preference name=log_sql}
			{preference name=log_mail}
			{preference name=log_tpl}
			{preference name=category_browse_count_objects}
			{preference name=error_reporting_level}
			{remarksbox type="tip" title="{tr}Tip{/tr}"}
				{tr}Many search options impact performance. Please see <a href="tiki-admin.php?page=search">Search admin panel</a>.{/tr}
			{/remarksbox}
		{/tab}

		{tab name="{tr}Sessions{/tr}"}
			<h2>{tr}Sessions{/tr}</h2>
			{preference name=session_silent}
			{preference name=tiki_cachecontrol_nosession}
		{/tab}

		{tab name="{tr}Newsletter{/tr}"}
			<h2>{tr}Newsletter{/tr}</h2>
			{preference name=newsletter_throttle}
			<div class="adminoptionboxchild" id="newsletter_throttle_childcontainer">
				{preference name=newsletter_pause_length}
				{preference name=newsletter_batch_size}
			</div>
		{/tab}

		{tab name="{tr}Time and memory limits{/tr}"}
			<h2>{tr}Time and memory limits{/tr}</h2>
			{preference name=allocate_memory_tracker_export_items}
			{preference name=allocate_time_tracker_export_items}
			{preference name=allocate_time_tracker_clear_items}
			{preference name="allocate_memory_unified_rebuild"}
			{preference name="allocate_time_unified_rebuild"}
		{/tab}

	{/tabset}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="performance" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>
