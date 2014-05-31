{* $Id$ *}

<img class="pull-right" src="img/icons/large/categories48x48.png" alt="{tr}Set up Categories{/tr}" />
<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" />
    <div class="media-body">
        {tr}Global content category system. Items of different types (wiki pages, articles, tracker items, etc) can be added to one or more categories. Permissions set for a category will apply to all items in that category, allowing access to be restricted to certain groups, users, etc{/tr}.
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
                    {tr}Once you have <a href="tiki-admin_categories.php" target="_blank">some categories defined</a>, go back to the Admin Wizard step '<strong>Set up Wiki environment</strong>' and define them there{/tr}.
                {/remarksbox}
            {/if}
            <br>
	        <br>
	        <em>{tr}See also{/tr} <a href="http://doc.tiki.org/category" target="_blank">{tr}Categories{/tr} @ doc.tiki.org</a></em>
        </fieldset>
        <br>
    </div>
</div>
