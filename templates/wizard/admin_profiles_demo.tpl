{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Check out some profiles{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Each of these profiles create a working instance of some features, such as trackers and wiki pages customized for specific purposes, for example{/tr}. <br><br>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
	<div target="tikihelp" class="tikihelp" style="float:right" title="{tr}Demo Profiles:{/tr} 
		{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}. 
		{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.">
		<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
	</div>
	{tr}They are not to be initially applied in production environments since they cannot be easily reverted and changes and new objects in your site are created for real{/tr}
{/remarksbox}
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Demo Profiles{/tr}</legend>
	<table style="width:100%">
	<tr>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_bug_tracker48x48.png" alt="{tr}Bug Tracker{/tr}" /></div>
	<b>{tr}Bug Tracker{/tr}</b> (<a href="tiki-admin.php?profile=Bug_Tracker&show_details_for=Bug_Tracker&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile allows you to see a tracker in action with some demo data, and a custom interface in a wiki page to add new items, as well as having them listed for you below.{/tr}
	<a href="https://doc.tiki.org/Trackers" target="tikihelp" class="tikihelp" title="{tr}Bug Tracker{/tr}: 
	<ul>
		<li>{tr}Uses PluginTracker in a wiki page to add items{/tr}</li>
	    <li>{tr}Create some custom feedback for message to the user after item insertion{/tr}</li>
	    <li>{tr}Uses PluginTrackerList to display inserted items{/tr}</li>
	</ul>">
		<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
	</a>
	<div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
		<a href="http://tiki.org/display520" class="internal" rel="box" title="{tr}Click to expand{/tr}">
			<img src="http://tiki.org/display520"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
		</a>
		<div class="mini" style="width:100px;">
			<div class="thumbcaption">{tr}Click to expand{/tr}</div>
		</div>
	</div>
	</td>
	<td style="width:4%"> 
	&nbsp;
	</td>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_dynamic_items_list48x48.png" alt="{tr}Dynamic Items List{/tr}" /></div>
	<b>{tr}Dynamic Items List{/tr}</b> (<a href="tiki-admin.php?profile=Dynamic_items_list_demo&show_details_for=Dynamic_items_list_demo&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile creates two linked trackers. One that allow pre-selecting the items in a drop down list based on the items in a previous drop down field. And a second tracker that holds the options displayed in the drop down fields.{/tr}
	<a href="https://doc.tiki.org/Dynamic+items+list" target="tikihelp" class="tikihelp" title="{tr}Dynamic Items List{/tr}: 
	<ul>
		<li>{tr}Useful for Geographic data (State, Country/Province, ...){/tr}</li>
	    <li>{tr}Useful for Types and Subtypes{/tr}</li>
	    <li>{tr}Useful for Program Names and Versions{/tr}</li>
	    <li>{tr}Easily manage the options in the second tracker without editing the dropdown in the first tracker{/tr} </li> 
	</ul>">
		<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
	</a>
	<div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
		<a href="http://tiki.org/display521" class="internal" rel="box" title="{tr}Click to expand{/tr}">
			<img src="http://tiki.org/display521"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
		</a>
		<div class="mini" style="width:100px;">
			<div class="thumbcaption">{tr}Click to expand{/tr}</div>
		</div>
	</div>
	</td>
	</tr>
	<tr>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_tracker_as_calendar48x48.png" alt="{tr}Tracker as Calendar{/tr}" /></div>
	<b>{tr}Tracker as Calendar{/tr}</b>  (<a href="tiki-admin.php?profile=Tracker_as_Calendar_10&show_details_for=Tracker_as_Calendar_10&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile creates a tracker with some demo data and wiki interface that will be used to display and manage a Calendar of events in a fancy visual way.{/tr} 
	<a href="http://doc.tiki.org/PluginTrackerCalendar" target="tikihelp" class="tikihelp" title="{tr}Tracker as Calendar{/tr}: 
	<ul>
		<li>{tr}Advanced use of Plugin TrackerList{/tr}</li>
		<li>{tr}Working example of Plugin TrackerCalendar{/tr}</li>
	    <li>{tr}Drag & Drop to resize or move events{/tr}</li>
	    <li>{tr}Several display modes, useful for Project & Resource Management{/tr}</li>
	</ul>">
		<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
	</a>
	<div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
		<a href="http://doc.tiki.org/display722" class="internal" rel="box" title="{tr}Click to expand{/tr}">
			<img src="http://doc.tiki.org/display722"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
		</a>
		<div class="mini" style="width:100px;">
			<div class="thumbcaption">{tr}Click to expand{/tr}</div>
		</div>
	</div>
	</td>
	<td style="width:4%"> 
	&nbsp;
	</td>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_voting_system48x48.png" alt="{tr}Voting System{/tr}" /></div>
	<b>{tr}Voting System{/tr}</b> (<a href="tiki-admin.php?profile=Voting_System&show_details_for=Voting_System&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile sets up a Voting system in which only members of a group will be able to vote. It creates a tracker, 2 groups of users, one user in each group and a custom wiki page as interface to vote{/tr}. 
	<a href="http://doc.tiki.org/E-Democracy+system" target="tikihelp" class="tikihelp" title="{tr}Voting System{/tr}: 
	<ul>
	    <li>{tr}Group homepage set for the voting group{/tr}</li>
	    <li>{tr}Only one vote per member is allowed{/tr}</li> 
		<li>{tr}Results shown in real time (Plugin TrackerStat){/tr}</li>
	    <li>{tr}Other candidates can be voted beyond the proposed{/tr}</li>
	</ul>">
		<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
	</a>
	<div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
		<a href="http://tiki.org/display522" class="internal" rel="box" title="{tr}Click to expand{/tr}">
			<img src="http://tiki.org/display522"  width="100" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
		</a>
		<div class="mini" style="width:100px;">
			<div class="thumbcaption">{tr}Click to expand{/tr}</div>
		</div>
	</div>
	</td>
	</tr>
	</table>
</fieldset>
<br>
</div>

