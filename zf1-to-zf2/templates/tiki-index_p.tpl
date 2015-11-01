{* $Id$ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
		{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

		{* Index we display a wiki page here *}
		<div id="tiki-main">
			<div id="tiki-mid">
				<table border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td id="centercolumn">
							<div id="tiki-center">

								{if $prefs.feature_page_title eq 'y'}
									<h1 class="pagetitle"><a href="tiki-index_p.php?page={$page|escape:"url"}">{$page}</a>
										{if $lock}
											{capture assign=title}{tr _0=$page_user}Locked by %0{/tr}{/capture}{*FIXME*}
											{icon name='lock' alt="{tr}Locked{/tr}" title=$title}
										{/if}
									</h1>
								{/if}
								<table >
									<tr>
										<td>
											{if $prefs.feature_wiki_description eq 'y'}
												<small>{$description}</small>
											{/if}
											{if $cached_page eq 'y'}
												<small>(cached)</small>
											{/if}
										</td>
										<td style="text-align:right;">

											{if $cached_page eq 'y'}
												<a title="{tr}Refresh{/tr}" href="tiki-index_p.php?page={$page|escape:"url"}&amp;refresh=1">{icon name='refresh'}</a>
											{/if}

											{if $user and $prefs.feature_wiki_notepad eq 'y' and $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
												<a title="{tr}Save to notepad{/tr}" href="tiki-index_p.php?page={$page|escape:"url"}&amp;savenotepad=1">{icon name='floppy' alt="{tr}Save{/tr}"}</a>
											{/if}

										</td>
									</tr>
								</table>

								<div class="wikitext">
									{if $structure eq 'y'}
										<div class="tocnav">
											<table width='100%'>
												{foreach from=$struct_prev_next item=struct name=str key=key}
													<tr>
														<td width='33%'>
															{if $struct.prev_page}
																<a class="tocnavlink" href="tiki-index_p.php?page={$struct.prev_page}&amp;structID={$key}">&lt;&lt;
																	{if $struct.prev_page_alias}
																		{$struct.prev_page_alias}
																	{else}
																		{$struct.prev_page}
																	{/if}
																</a>

															{else}
																&nbsp;
															{/if}
														</td>
														<td align='center' width='33%'>
															{* <a class="tocnavlink" href="tiki-index_p.php?page=">{$key}</a> *}
															{$key}
														</td>
														<td align='right' width='33%'>
															{if $struct.next_page}
																<a class="tocnavlink" href="tiki-index_p.php?page={$struct.next_page}&amp;structID={$key}">
																	{if $struct.next_page_alias}
																		{$struct.next_page_alias}
																	{else}
																		{$struct.next_page}
																	{/if}
																	&gt;&gt;
																</a>
															{else}
																&nbsp;
															{/if}
														</td>
													</tr>
												{/foreach}
											</table>
										</div>
									{/if}
									{$parsed}
									{if $pages > 1}
										<div align="center">
											<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}" class="tips" title=":{tr}First{/tr}">
												{icon name='backward_step'}
											</a>
											<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}" class="tips" title="{tr}Previous{/tr}">
												{icon name='backward'}
											</a>
											<small>{tr}page:{/tr}{$pagenum}/{$pages}</small>
											<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}" class="tips" title=":{tr}Next{/tr}">
												{icon name='forward'}
											</a>
											<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}" class="tips" title=":{tr}Last{/tr}">
												{icon name='forward_step'}
											</a>
										</div>
									{/if}
								</div>

								{if $smarty.capture.editdate_section neq ''}
									<footer class="help-block editdate">{tr}Last modification date:{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$lastUser}">{$lastUser}</a></footer>
								{/if}

							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		{include file='footer.tpl'}
	</body>
</html>
