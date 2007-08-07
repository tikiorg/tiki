{literal}
<script type="text/javascript" src="lib/mootools/mootools.js"></script>
<script type="text/javascript" src="lib/mootools/extensions/windoo/windoo.js"></script>
<script type="text/javascript">

tikimypagewin=[];
{/literal}{$mypagejswindows}{literal}

</script>
{/literal}
<div id="container" style="margin: auto; width: 1000px; display: block; height: 500px; margin-bottom: 0">
<div id='mypage_tools'>
 <a href='#'>New IFrame</a>
</div>

<div>
 <p>IFrame:</p>
 Title: <input id='mypage_newiframe_title' type='text' value='' /><br />
 URL:   <input id='mypage_newiframe_url' type='text' value='' /><br />
 <input id='mypage_newiframe_submit' type='button' value='Create'>
</div>

<div>
 <p>wiki:</p>
 Title: <input id='mypage_newwiki_pagename' type='text' value='' /><br />
 <input id='mypage_newwiki_submit' type='button' value='Create'>
</div>
</div>
{literal}
<script type="text/javascript">

$('mypage_newiframe_submit').addEvent('click', function() {
	var title=$('mypage_newiframe_title').value;
	var url=$('mypage_newiframe_url').value;

	xajax_mypage_win_create({/literal}{$id_mypage}{literal}, 'iframe', title, url);
});

$('mypage_newwiki_submit').addEvent('click', function() {
	var pagename=$('mypage_newwiki_pagename').value;

	xajax_mypage_win_create({/literal}{$id_mypage}{literal}, 'wiki', pagename, pagename);
});

</script>
{/literal}
