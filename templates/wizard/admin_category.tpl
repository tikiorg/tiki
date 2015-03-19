{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Global content category system. Items of different types (wiki pages, articles, tracker items, etc) can be added to one or more categories. Permissions set for a category will apply to all items in that category, allowing access to be restricted to certain groups, users, etc{/tr}.</br></br>
	<div class="media-body">
		{icon name="admin_category" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Categories{/tr}</legend>
			<br>
			{tr}Categories are set up in the admin categories panel. Please see the Categories item in the Admin menu{/tr}.<br>
			<br>
			{tr}.. or ..{/tr} <a href="tiki-admin_categories.php" target="_blank">{tr}Set up categories here{/tr}</a><br>
			<br>
			{if $prefs['flaggedrev_approval'] eq 'y' && empty($prefs['flaggedrev_approval_categories'])}
				{remarksbox type="info" title="{tr}Info{/tr}"}
					{tr}You have the feature '<strong>Revision Approval</strong>' enabled, but you haven't defined yet which content categories require revision approval for their wiki pages{/tr}.
					{tr}Once you have <a href="tiki-admin_categories.php" target="_blank">some categories defined</a>, go back to the Configuration Wizard step '<strong>Set up Wiki environment</strong>' and define them there{/tr}.
				{/remarksbox}
			{/if}
			<br>
			<br>
			<em>{tr}See also{/tr} <a href="http://doc.tiki.org/category" target="_blank">{tr}Categories{/tr} @ doc.tiki.org</a></em>
		</fieldset>
		<br>
	</div>
</div>
