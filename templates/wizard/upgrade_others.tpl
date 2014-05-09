{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_upgrade48x48.png" alt="{tr}Upgrade Wizard{/tr}" title="{tr}Upgrade Wizard{/tr}"/></div>
{tr}Other features and settings{/tr}.
<br/><br/>
<div class="adminWizardContent">

    <fieldset>
        <legend>{tr}Ratings in Forums{/tr}</legend>
        <div class="adminWizardIconright"><img src="img/icons/large/rating48x48.png" alt="{tr}Ratings{/tr}" title="{tr}Ratings{/tr}"/></div>
        <ul>
            <li>{tr}New option per forum: "User information display > <strong>Topic Rating</strong>" by each user{/tr}
                <a href="http://doc.tiki.org/Rating" target="tikihelp" class="tikihelp" title="{tr}Topic Rating by each user:{/tr}
                {tr}Since Tiki12.2, there is a new forum setting to allow the optional display of the Rating by each user to that forum thread topic in each reply{/tr}.
                <br/><br/>
                {tr}This setting is useful to ease the task to reach consensus on deliberations (in forum threads) by identifying in a more clear way the position (topic rating) of each person on that topic at each moment on the discussion{/tr}.
                <br/><br/>
                {tr}Click to read more{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>
    </fieldset>

    <fieldset>
        <legend>{tr}Sysadmin Tasks{/tr}</legend>
        <div class="adminWizardIconright"><img src="img/icons/large/xfce4-appfinder48x48.png" alt="{tr}Search{/tr}" title="{tr}Search{/tr}"/></div>
        <b>{tr}Search Index{/tr}</b>:
        <ul>
            <li>{tr}You can rebuild the unified search index (feature '<b>Advanced Search</b>') by visiting example.com/tiki-admin.php?page=search&rebuild=now or through setting a <b>cron job</b>{/tr}
                <a href="http://doc.tiki.org/Cron+Job+to+Rebuild+Search+Index" target="tikihelp" class="tikihelp" title="{tr}Cron Job to Rebuild Search Index:{/tr}
                {tr}Starting in Tiki9, if you had a large site you should set up a Cron job to regularly rebuild the search index.{/tr}
                <br/><br/>
                {tr}Starting in Tiki11, the syntax to rebuild the search index changed{/tr}.
                {tr}Example{/tr}
                <pre>0 0 * * * cd /path_to_tiki;php console.php index:rebuild</pre>
                {tr}Click to read more{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>
        <div class="adminWizardIconright"><img src="img/icons/large/console.png" alt="{tr}Console{/tr}" title="{tr}Console{/tr}"/></div>
        <b>{tr}Console{/tr}</b>:
        <ul>
            <li>{tr}Starting in Tiki11, <b>console.php</b> script exists to help you administer your Tiki instance via the command line{/tr}.
                <a href="http://doc.tiki.org/Console" target="tikihelp" class="tikihelp" title="{tr}Console (console.php script):{/tr}
                {tr}All the other command line scripts from before Tiki11 (ex.: php installer/shell.php) will continue to work, but all future developments will be on this new console.php script{/tr}.
                <br/><br/>
                {tr}Example: Database update{/tr}
                <pre>php console.php database:update</pre>
                {tr}Click to read more{/tr}
	    	">
                    <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                </a>
            </li>
        </ul>
    </fieldset>

    <fieldset>
        <legend>{tr}Other Features{/tr}</legend>
        {preference name=conditions_enabled}
        <div class="adminoptionboxchild" id="conditions_enabled_childcontainer">
            {preference name=conditions_page_name}
            {preference name=conditions_minimum_age}
        </div>
        {preference name=feature_jcapture}
        <div class="adminoptionboxchild" id="feature_jcapture_childcontainer">
            {preference name=fgal_for_jcapture}
        </div>
        {preference name=feature_docs}
        {preference name=feature_draw}
        <div class="adminoptionboxchild" id="feature_draw_childcontainer">
            {preference name=feature_draw_hide_buttons}
            {preference name=feature_draw_separate_base_image}
            <div class="adminoptionboxchild" id="feature_draw_separate_base_image_childcontainer">
                {preference name=feature_draw_in_userfiles}
            </div>
        </div>
    </fieldset>

</div>
