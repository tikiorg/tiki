{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Each of these profiles create a working instance of some features, such as trackers and wiki pages customized for specific purposes, for example{/tr}. <br><br>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
    <div target="tikihelp" class="tikihelp" style="float:right" title="{tr}Demo Profiles:{/tr}
		{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}. 
		<br/><br/>
		{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.">
        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
    </div>
{tr}They are not to be initially applied in production environments since they cannot be easily reverted and changes and new objects in your site are created for real{/tr}
{/remarksbox}
<div class="adminWizardContent">
    <fieldset>
        <legend>{tr}Profiles:{/tr}</legend>
        <table style="width:100%">
            <tr>
                <td style="width:48%">
                    <b>{tr}Bug Tracker{/tr}</b> (<a href="tiki-admin.php?profile=Bug_Tracker&show_details_for=Bug_Tracker&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile allows you to see a tracker in action with some demo data, and a custom interface in a wiki page to add new items, as well as having them listed for you below.{/tr}
                    <br/><a href="https://doc.tiki.org/Trackers" target="tikihelp" class="tikihelp" title="{tr}Bug Tracker{/tr}:
           	{tr}More details{/tr}:
           	<ul>
    		    <li>{tr}Uses PluginTracker in a wiki page to add items{/tr}</li>
	            <li>{tr}Create some custom feedback for message to the user after item insertion{/tr}</li>
	            <li>{tr}Uses PluginTrackerList to display inserted items{/tr}</li>
	        </ul>
           	{tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display520" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_bug_tracker.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                </td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    <b>{tr}Timesheet{/tr}</b> (<a href="tiki-admin.php?profile=Time_Sheet&show_details_for=Time_Sheet&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile allows recording time spent on projects. It creates two trackers: one to hold the time spent, and the other with the project names{/tr}.
                    <br/><a href="https://doc.tiki.org/Timesheet" target="tikihelp" class="tikihelp" title="{tr}Timesheet{/tr}:
           	{tr}More details{/tr}:
        	<ul>
        	    <li>{tr}Allows to track your time spent on projects{/tr}</li>
        	    <li>{tr}Customize your project categories{/tr}</li>
        	    <li>{tr}Add or edit your timesheet fields as desired{/tr} </li>
        	    <li>{tr}Both trackers are linked, so that project names can be chosen when entering items to the timesheet tracker{/tr}</li>
	        </ul>
           	{tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <a href="https://doc.tiki.org/PluginTimesheet" target="tikihelp" class="tikihelp" title="{tr}Plugin Timesheet{/tr}:
	<em>{tr}See also{/tr} {tr}Plugin Timesheet in doc.tiki.org{/tr}</em>">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display523" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_timesheet.png"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                    <br/>
                </td>
            </tr>
            <tr>
                <td style="width:48%">
                    <b>{tr}Tracker as Calendar{/tr}</b>  (<a href="tiki-admin.php?profile=Tracker_as_Calendar_12&show_details_for=Tracker_as_Calendar_12&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile creates a tracker with some demo data and wiki interface that will be used to display and manage a Calendar of events in a fancy visual way.{/tr}
                    <br/><a href="http://doc.tiki.org/PluginTrackerCalendar" target="tikihelp" class="tikihelp" title="{tr}Tracker as Calendar{/tr}:
           	{tr}More details{/tr}:
        	<ul>
        		<li>{tr}Advanced use of Plugin TrackerList{/tr}</li>
        		<li>{tr}Working example of Plugin TrackerCalendar{/tr}</li>
        	    <li>{tr}Drag & Drop to resize or move events{/tr}</li>
        	    <li>{tr}Several display modes, useful for Project & Resource Management{/tr}</li>
	        </ul>
           	{tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://doc.tiki.org/display722" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_tracker_as_calendar.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                </td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    <b>{tr}Voting System{/tr}</b> (<a href="tiki-admin.php?profile=Voting_System&show_details_for=Voting_System&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile sets up a Voting system in which only members of a group will be able to vote. It creates a tracker, 2 groups of users, one user in each group and a custom wiki page as interface to vote{/tr}.
                    <br/><a href="http://doc.tiki.org/E-Democracy+system" target="tikihelp" class="tikihelp" title="{tr}Voting System{/tr}:
           	{tr}More details{/tr}:
        	<ul>
        	    <li>{tr}Group homepage set for the voting group{/tr}</li>
        	    <li>{tr}Only one vote per member is allowed{/tr}</li>
        		<li>{tr}Results shown in real time (Plugin TrackerStat){/tr}</li>
        	    <li>{tr}Other candidates can be voted beyond the proposed{/tr}</li>
	        </ul>
           	{tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display522" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_voting_system.png"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>
    <br>
</div>

