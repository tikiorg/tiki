<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$title} - Slideshow</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="lib/slideshow/css/slideshow.css" type="text/css" media="screen" />
	<style type="text/css">
{literal}
	html, body { background-color: black; height: 100%; width: 100%; }
	* { margin: 0; padding: 0; }
	.slideshow { right: 70px; }
	.slideshow-thumbnails a { padding: 0; }
	.slideshow-thumbnails a:hover { padding: 5px; }
	.slideshow, .slideshow-thumbnails { top: 30px; }
	.slideshow-thumbnails {
		height: 100%;
		left: auto;
		right: 10px;
		width: 130px;
		position: fixed;
	}
	.slideshow-thumbnails ul {
		height: 500px;
		width: 70px;
		bottom: 30px;
	}
	.button a {
		color: gray;
		font-weight: bold;
		text-decoration: none;
	}
	.button a:hover {
		color: white;
	}
	.slideshow-thumbnails .overlay {
		right: 10px;
		width: 130px;
		position: absolute;
		height: 60px;
		z-index: 10000;
	}
	.slideshow-thumbnails .overlay.a {
		background: transparent url(lib/slideshow_tiki/thumbnails-a.png) repeat scroll 0 0;
	}
	.slideshow-thumbnails .overlay.b {
		background: transparent url(lib/slideshow_tiki/thumbnails-b.png) repeat scroll 0 0;
		bottom: 30px;
	}
{/literal}
	</style>
	<script type="text/javascript" src="lib/mootools/mootools-1.2-core.js"></script>
	<script type="text/javascript" src="lib/mootools/mootools-1.2-more.js"></script>
	<script type="text/javascript" src="lib/slideshow/js/slideshow.js"></script>
	<script type="text/javascript" src="lib/slideshow_tiki/slideshow.fullsize.js"></script>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	var myShow;
	window.addEvent('domready', function(){ldelim}
		var data = {ldelim}
{foreach from=$file key=i item=f name=files}
{if $smarty.foreach.files.first}{assign var=first value=$f.id}{/if}
			'tiki-download_file.php?preview&fileId={$f.id}': {ldelim}
				caption: '{$f.name|escape:'javascript'}',
				href: 'tiki-download_file.php?fileId={$f.id}'
			{rdelim}{if !$smarty.foreach.files.last},
{/if}
{/foreach}
		{rdelim};
		myShow = new Slideshow.Fullsize('show', data, {ldelim}
			controller: true,
			hu: '{if $tikiroot neq ""}{$tikiroot}{else}/{/if}',
			thumbnails: true,
			replace: [/\?preview/, '?thumbnail'],
			overlap: false,
			delay: 2000,
			duration: 500,
			random: false,
			loop: true,
			linked: false,
			fast: false,
			captions: true,
			adjustheight: -60,
			adjustwidth: -180
		{rdelim});
	{rdelim});
	//--><!]]>
	</script>
</head>
<body>
<div style="position:fixed; top:5px; right:10px;">{button href='#' _onclick='javascript:window.close();' _text="{tr}Close{/tr}"}</div>
<div id="show" class="slideshow">
	<div id="images" class="slideshow-images">
		<img src="{if $tikiroot neq ""}{$tikiroot}{else}/{/if}tiki-download_file.php?preview&amp;fileId={$first}" />
	</div>
</div>
</body>
</html>
