{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{icon name="wrench" size=3 iclass="pull-right"}
	{tr}If you are an experienced Tiki site administrator, consider whether the advanced features below would be useful for your use case. They are useful for creating a similar set of Tiki objects for different groups of users with like permissions{/tr}.
	</br></br>
	<div class="media-body">
		{icon name="admin_workspace" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Workspaces{/tr}</legend>
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
