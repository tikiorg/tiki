{if $prefs.flaggedrev_approval eq 'y'}
	{if $revision_approval and $revision_approved neq $revision_displayed}
		{remarksbox type=comment title="{tr}Newer content available{/tr}"}
			{if $lastVersion eq $revision_displayed}
				<p>
					{tr}You are currently viewing the latest version of the page.{/tr}
					{if $revision_approved}
						{tr}You can also view the {self_link}latest approved version{/self_link}.{/tr}
					{/if}
				</p>
			{else}
				<p>
					{tr}You are currently viewing the approved version of the page.{/tr}
					{if $revision_approved}
						{tr}You can also view the {self_link latest=1}latest version{/self_link}.{/tr}
					{/if}
				</p>
			{/if}
		{/remarksbox}
	{/if}
{/if}
