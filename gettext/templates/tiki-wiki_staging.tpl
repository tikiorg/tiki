{if $beingStaged eq 'y'}
<div class="tocnav">
{tr}This is the staging copy of{/tr} <a class="link" href="tiki-index.php?page={$approvedPageName|escape:'url'}">{tr}the approved version of this page.{/tr}</a>
{if $outOfSync eq 'y'}
	{if $canApproveStaging == 'y'}
		<div class="notif-pad">
			{if $mid neq 'tiki-pagehistory.tpl'}
				{if $lastSyncVersion}
				<a class="link" href="tiki-pagehistory.php?page={$page|escape:'url'}&amp;diff2={$lastSyncVersion}&amp;diff_style=sidediff&amp;compare=Compare">{tr}View changes since last approval.{/tr}</a>
				{else}
					{tr}Viewing of changes since last approval is possible only after first approval.{/tr}
				{/if}
			{/if}
			<form action="tiki-approve_staging_page.php" method="post">
				<input type="hidden" name="page" value="{$page|escape}" />
				<div class="notif-pad-2">
					<div class="notif-row">
						{tr}Approve changes{/tr}
						<input type="submit" name="staging_action" value="{tr}Submit{/tr}"/>
					</div>
				</div>
			</form>
			</div>
			{else}
				{tr}Latest changes will be synchronized after approval.{/tr}
			{/if} {*canApproveStaging*}
		{/if}{*outOfSync*}
</div>
{/if} {*beingStaged*}

{if $needsFirstApproval == 'y' and $canApproveStaging == 'y'}
	<div class="tocnav">
		{tr}This is a new staging page that has not been approved before. Edit and manually move it to the category for approved pages to approve it for the first time.{/tr}
	</div>
{/if}

{if $canEditStaging eq 'y' and $hasStaging eq 'y'}
	<div class="tocnav">
	  {tr}There is a staging page (not yet approved) for this page.{/tr}
	 </div>
{/if}
