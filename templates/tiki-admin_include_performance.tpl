{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Please see the <a class='rbox-link' target='tikihelp' href='http://dev.tiki.org/Performance'>Performance page</a> on Tiki's developer site.{/tr}{/remarksbox}

<form class="admin" id="performance" name="performance" action="tiki-admin.php?page=performance" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="performance" value="{tr}Apply{/tr}" />
		<input type="reset" name="performancereset" value="{tr}Reset{/tr}" />
	</div>
{tabset}

		{tab name="{tr}Performance{/tr}"}
			{preference name=tiki_minify_javascript}
			{preference name=javascript_cdn}
			{preference name=tiki_cdn}
			{preference name=tiki_cdn_ssl}
			{preference name=tiki_minify_css}
			<div class="adminoptionboxchild" id="tiki_minify_css_childcontainer">
				{preference name=tiki_minify_css_single_file}
			</div>
			{preference name=feature_obzip}
			{preference name=users_serve_avatar_static}
			<div class="adminoptionboxchild">
				{if $gzip_handler ne 'none'}
					<div class="highlight" style="margin-left:30px;">
						{tr}Output compression is active.{/tr}
						<br />
						{tr}Compression is handled by:{/tr} {$gzip_handler}.
					</div>
				{/if}
			</div>
			{preference name=tiki_cachecontrol_session}
			{preference name=smarty_compilation}
		{/tab}
		
		{tab name="{tr}Bytecode Cache{/tr}"}
			{if $opcode_cache}
				<p>{tr 0=$opcode_cache}Using <strong>%0</strong>.These stats affect all PHP applications running on the server.{/tr}</p>

				{if $opcode_stats.warning_xcache_blocked}
					<p>{tr}Configuration setting <em>xcache.admin.enable_auth</em> prevents from accessing statistics. This will also prevent the cache from being cleared when clearing template cache.{/tr}</p>
				{/if}

				<p>
					<img src="http://chart.apis.google.com/chart?cht=p3&amp;chs=250x100&amp;chd=t:{$opcode_stats.memory_used},{$opcode_stats.memory_avail}&amp;chl={tr}Used{/tr}|{tr}Available{/tr}&amp;chtt={tr}Memory{/tr}" width="250" height="100"/>
					<img src="http://chart.apis.google.com/chart?cht=p3&amp;chs=250x100&amp;chd=t:{$opcode_stats.hit_hit},{$opcode_stats.hit_miss}&amp;chl={tr}Hit{/tr}|{tr}Miss{/tr}&amp;chtt={tr}Cache Hits{/tr}" width="250" height="100"/>
				</p>

				{if $opcode_stats.warning_fresh}
					<p>{tr}Few hits recorded. Statistics may not be representative.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_ratio}
					<p>{tr 0=$opcode_cache}Low hit ratio. %0 may be misconfigured and not used.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_starve}
					<p>{tr}Little memory available. Thrashing likely to occur.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_low}
					<p>{tr 0=$opcode_cache}Small amount of memory allocated to %0. Verify the configuration.{/tr}</p>
				{/if}

				{if $opcode_stats.warning_check}
					<p>
						{tr 0=$stat_flag}Configuration <em>%0</em> is enabled. Disabling modification checks can improve performance, but will require manual clear on file updates.{/tr}
						{if $opcode_stats.warning_xcache_blocked}
							{tr 0=$stat_flag}<em>%0</em> should not be disabled due to authentication on XCache.{/tr}
						{/if}
					</p>
				{/if}
			{else}
				{tr}Bytecode cache is not used. Using a bytecode cache (APC, XCache) is highly recommended for production environments.{/tr}
			{/if}
		{/tab}
		
		{tab name="{tr}Wiki{/tr}"}
			{preference name=wiki_cache}
			{preference name=feature_wiki_icache}
			{preference name=wiki_ranking_reload_probability}
		{/tab}

		{tab name="{tr}Database{/tr}"}
				{preference name=log_sql}
				<div class="adminoptionboxchild" id="log_sql_childcontainer">
					{preference name=log_sql_perf_min}
				</div>
			{/tab}
		
		{tab name="{tr}Memcache{/tr}"}
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
			{preference name=wikiplugin_snarf_cache}
		{/tab}

		{tab name="{tr}Major slow down{/tr}"}
			{remarksbox type="note" title="{tr}Major slow down{/tr}"}{tr}These are reported to slow down Tiki. If you have a high-volume site, you may want to deactivate them{/tr}
			{/remarksbox}
			{preference name=wikiplugin_sharethis}
			{preference name=log_sql}
			{preference name=log_mail}
			{preference name=log_tpl}
			{preference name=error_reporting_level}
			{remarksbox type="tip" title="{tr}Tip{/tr}"}
				{tr}Many search options impact performance. Please see <a href="tiki-admin.php?page=search">Search admin panel</a>.{/tr}
			{/remarksbox}
		{/tab}

		{tab name="{tr}Sessions{/tr}"}
				{preference name=session_silent}
				{preference name=tiki_cachecontrol_nosession}
		{/tab}

		{tab name="{tr}Newsletter{/tr}"}
			{preference name=newsletter_throttle}
			<div class="adminoptionboxchild" id="newsletter_throttle_childcontainer">
				{preference name=newsletter_pause_length}
				{preference name=newsletter_batch_size}
			</div>
		{/tab}
{/tabset}
		
	<div class="input_submit_container" style="margin-top: 5px; text-align: center">
		<input type="submit" name="performance" value="{tr}Apply{/tr}" />
	</div>
</form>
