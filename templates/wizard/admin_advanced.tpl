{* $Id$ *}

<img class="pull-right" src="img/icons/large/icon-configuration48x48.png" alt="{tr}Set up Workspaces & Areas{/tr}" />
<div class="media">
    <img class="pull-left" src="img/icons/large/wizard_admin48x48.png" alt="{tr}Configuration Wizard{/tr}" title="{tr}Configuration Wizard{/tr}" />
    <div class="media-body">
        {tr}If you are an experienced Tiki site administrator, consider whether the advanced features below would be useful for your use case. They are useful for creating a similar set of Tiki objects for different groups of users with like permissions{/tr}.
        <fieldset>
	        <legend>{tr}Workspaces{/tr}</legend>
	        <img src="img/icons/large/areas48x48.png" class="pull-right" />
	        {preference name=workspace_ui}
	        <em>{tr}See also{/tr} <a href="https://doc.tiki.org/Workspaces UI" target="_blank">{tr}Workspaces UI in doc.tiki.org{/tr}</a></em>
        </fieldset>
        <fieldset>
	        <legend>{tr}Dependencies{/tr}</legend>
	        <div class="admin clearfix featurelist">
	            {preference name=feature_categories}
	            {preference name=feature_perspective}
	            {preference name=namespace_enabled}
	            <div class="adminoptionboxchild">
		            {tr}Enable using the same wiki page name in different contexts{/tr}. {tr}E.g. ns1:_:MyPage and ns2:_:MyPage{/tr}.
	            </div>
	        </div>
	        <br>
	        <em>{tr}See also{/tr} <a href="tiki-admin.php?page=workspace" target="_blank">{tr}Workspaces & Areas admin panel{/tr}</a></em>
        </fieldset>
    </div>
</div>