{if $prefs.flaggedrev_approval eq 'y' and $revision_approval}
	{if ($revision_approved or $revision_displayed) and $revision_approved neq $lastVersion}
		{if $lastVersion eq $revision_displayed}
			{remarksbox type=comment title="{tr}Newer content available{/tr}"}
				<p>
					{tr}You are currently viewing the latest version of the page.{/tr}
					{if $revision_approved}
						{tr}You can also view the {self_link}latest approved version{/self_link}.{/tr}
					{/if}
					{if $tiki_p_wiki_approve eq 'y'}
						{tr}You can approve this revision and make it available to a wider audience. Make sure you review all the changes before approving.{/tr}
					{/if}
				</p>
				{if $tiki_p_wiki_approve eq 'y'}
					<form method="post" action="{$page|sefurl}">
						{if $revision_approved}
							<p><a href="tiki-pagehistory.php?page={$page|escape:'url'}&compare&oldver={$revision_approved|escape:'url'}&diff_style={$prefs.default_wiki_diff_style|escape:'url'}">{tr}Show changes since last approved revision{/tr}</a></p>
						{else}
							<p>{tr}This page has no prior approved revision. <strong>All of the content must be reviewed.</strong>{/tr}</p>
						{/if}
						<div class="submit">
							<input type="hidden" name="revision" value="{$revision_displayed|escape}"/>
							<input type="submit" name="approve" value="{tr}Approve current revision{/tr}"/>
						</div>
					</form>
				{/if}
			{/remarksbox}
		{else}
			{remarksbox type=comment title="{tr}Content waiting for approval{/tr}"}
				<p>
					{tr}You are currently viewing the approved version of the page.{/tr}
					{if $revision_approved and $tiki_p_wiki_view_latest eq 'y'}
						{tr}You can also view the {self_link latest=1}latest version{/self_link}.{/tr}
					{/if}
				</p>
			{/remarksbox}
		{/if}
	{elseif $revision_approval and ! $revision_approved and $tiki_p_wiki_view_latest eq 'y'}
		{remarksbox type=comment title="{tr}Content waiting for approval{/tr}"}
			<p>
				{tr}View the {self_link latest=1}latest version{/self_link}.{/tr}
			</p>
		{/remarksbox}
	{/if}
{/if}
