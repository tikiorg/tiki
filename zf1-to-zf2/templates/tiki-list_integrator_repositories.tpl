{* $Id$ *}

{title}{tr}Available Repositories{/tr}{/title}

{if $tiki_p_admin eq 'y'}
	<div class="t_navbar">
		{button href="tiki-admin_integrator.php" class="btn btn-default" _text="{tr}Configure Repositories{/tr}"}
	</div>
{/if}


{* Table with list of repositories (if array is not empty) *}
{if count($repositories) gt 0}
	<div class="table-responsive">
		<table class="table" id="integrator-repositories">
			<tr>
				<th>{tr}Name{/tr}</th>
				<th>{tr}Description{/tr}</th>
			</tr>

			{section name=rep loop=$repositories}
				<tr>
					<td class="text">
						<a href="tiki-integrator.php?repID={$repositories[rep].repID|escape}">
							{$repositories[rep].name}
						</a>
					</td>
					<td class="text">{$repositories[rep].description}</td>
				</tr>
			{/section}
		</table>
	</div>
{else}

{* Here should be panel (let it be style 'info-panel') with info
 * Smth like: "No configured/visible repositories...", but if
 * current user with tiki_p_admin it continue with "Ypu may setup
 * repositories on the following page (or by press button above :)"
 *
 * Moreover such 'info' panels can be everywhere :) -- at least at
 * wiki edit help and comments help ... let it be standart way to
 * display hints. :) -- not separate styles... to be personalized
 * (if smbd needs :) it can contain id attribute... i.e. smth like
 * <div class='info-panel' id='wiki-help'>...</div>
 * <div class='info-panel' id='comments-help'>...</div>
 * <div class='info-panel' id='integrator-no-reps'>...</div>
 *}

{/if}
