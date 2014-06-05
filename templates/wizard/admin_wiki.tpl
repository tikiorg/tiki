{* $Id$ *}
<img class="pull-right" src="img/icons/large/wikipages48x48.png" alt="{tr}Set up the Wiki environment{/tr}" />{tr}Set up the Wiki environment{/tr}
<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Admin Wizard{/tr}" title="{tr}Admin Wizard{/tr}" />
    <div class="media-content">
        <fieldset>
	    <legend>{tr}Wiki environment{/tr}</legend>

	    <div class="admin clearfix featurelist">
	    {preference name=feature_categories}
	    {preference name=wiki_auto_toc}
	    <div class="adminoptionboxchild">
		    {tr}See also{/tr} <a href="https://doc.tiki.org/Category" target="_blank">{tr}Category{/tr} @ doc.tiki.org</a>
	    </div>
	    {preference name=feature_jcapture}
	    {preference name=feature_wiki_structure}
	    <div class="adminoptionboxchild">
		    {tr}Look for the <img src="img/icons/camera.png" /> icon in the editor toolbar{/tr}. {tr}Requires Java{/tr}.<br/><a href="https://www.java.com/verify/" target="_blank">{tr}Verify your Java installation{/tr}</a>.<br>
	    </div>
        {preference name=flaggedrev_approval}
        <div id="flaggedrev_approval_childcontainer">
            {if $prefs['feature_categories'] eq 'y'}
                {preference name=flaggedrev_approval_categories}
            {else}
                {remarksbox type="info" title="{tr}Info{/tr}"}
                {tr}Once you have the feature '<strong>Categories</strong>' enabled, you will need to define some content categories, and indicate which ones require revision approval for their wiki pages{/tr}.
                <br><br/>
                {tr}You will be able to set the category ids here when you come back with Categories enabled, or at the corresponding <a href="tiki-admin.php?page=wiki&cookietab=3" target="_blank">Admin Panel</a> with the '<em>Advanced</em>' features shown in the Preference Filters{/tr}.
                {/remarksbox}
            {/if}
        </div>
	</div>
	<br><br>
	<em>{tr}See also{/tr} <a href="tiki-admin.php?page=wiki&amp;alt=Wiki#content1" target="_blank">{tr}Wiki admin panel{/tr}</a></em>

</fieldset>
    </div>
</div>
