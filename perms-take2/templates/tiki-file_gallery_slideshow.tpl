<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
{* Template for JQuery based slideshow *}
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$title} - Slideshow</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	{*<link rel="stylesheet" href="lib/slideshow/css/slideshow.css" type="text/css" media="screen" />*}
	<style type="text/css">
{literal}
	html, body { height: 100%; width: 100%; }
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
	/* new for jquery */
	.slideshow_container {
		position: absolute;
		left: 0;
		top: 0;
		right: 0;
	}
	.slideshow_container img {
		position: absolute;
		left: 0;
		top: 0;
	}
	.slideshow_thumbs {
		position: fixed;
		bottom: 0;
		height: 100px;
		overflow: hidden;
		z-index: 100;
		background: url(lib/shadowbox/images/overlay-85.png);
	}
	.slideshow_thumbs img {
		position: relative;
		left: 0;
		top: 0;
		margin: 0.2em 1em;
		vertical-align: middle;
	}
	.slideshow_controls {
		position: fixed;
		top: .5em;
		right: 1em;
		z-index: 100;
	}
{/literal}
	</style>
	{* don't include file='header_jquery.tpl' - too bulky, just what we need copied from there *}
	<!--  start jquery-tiki mini set-up -->
	<script type="text/javascript" src="lib/jquery/jquery.min.js"></script>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
var $jq = jQuery.noConflict();
	//--><!]]>
	</script>
	<script type="text/javascript" src="lib/jquery/malsup-cycle/jquery.cycle.all.min.js"></script>
	<!--  end jquery-tiki mini set-up -->

	{jq}

setupSlideShow = function () {
	// vars
	var imgs = $jq('.slideshow_container img');
	var cont = $jq('.slideshow_container');
	// resize container first
	cont.cycle('stop');
	$jq.removeData('.slideshow_container')
	imgs.hide();
	cont.width( $jq('body').width());
	cont.height($jq('body').height() - $jq('.slideshow_thumbs').height());
	// resize images that need it (too big)
	imgs.each( function() {	// resize and centre images
		var jthis = $jq(this);
		//alert((jthis.width()) + 'px  x  ' + (jthis.height()) + 'px\n' + jthis.position().left + " " + jthis.position().top);
		if (jthis.css('naturalWidth'))  { jthis.width( jthis.css('naturalWidth')); }	// reset to original size
		if (jthis.css('naturalHeight')) { jthis.height(jthis.css('naturalHeight'));}
		// wrap in a div so positioning works
		//if (jthis.parent()[0] == cont[0]) {
		//	var d = document.createElement('div');
		//	$jq(d).width(cont.width()).height(cont.height()).css('left', 0).css('top', 0);
		//	jthis.wrap(d);
		//} else {
			jthis.parent().width(cont.width()).height(cont.height()).css('left', 0).css('top', 0);
		//}
		// resize
		var dx = 1, dy = 1, r = 1;
		if (jthis.width() > cont.width()) {
			dx = cont.width() / jthis.width();
		}
		if (jthis.height() > cont.height()) {
			dy = cont.height() / jthis.height();
		}
		// work out which ratio to use
		if (dx < 1) {
			if (dy < 1) {
				if (dx < dy) {
					r = dx;
				} else {
					r = dy;
				}
			} else {
				r = dx;
			}
		} else if (dy < 1) {
			r = dy;
		}
		if (r < 1) {
			jthis.width( jthis.width() * r);	// coo, does width and height, nice!
			//jthis.height(jthis.height() * r);
		}
		// reposition
		jthis.css('left', parseInt((cont.width() - jthis.width()) / 2) + 'px');
		jthis.css('top',  parseInt((cont.height() - jthis.height()) / 2) + 'px');
	});
	imgs.show();
	// make slideshow
	cont.cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});
};	// end setupSlideShow

$jq(window).resize(function(){
	setupSlideShow();
});

setupSlideShow();

	{/jq}
	{if $headerlib}{$headerlib->output_headers()}{/if}
</head>
<body class="tiki_file_galleries slideshow">
	<div class="slideshow_controls">{button href='#' _onclick='javascript:window.close();' _text="{tr}Close{/tr}"}</div>
	<div id="show" class="slideshow_container">{strip}
		{foreach from=$file key=i item=f name=files}
			<div><img src='{if $tikiroot neq ""}{$tikiroot}{else}/{/if}tiki-download_file.php?preview&fileId={$f.id}'  title='{$f.name|escape}' /></div>
		{/foreach}
	{/strip}</div>
	<div class="slideshow_thumbs">
		{foreach from=$file key=i item=f name=files}
			<img src='{if $tikiroot neq ""}{$tikiroot}{else}/{/if}tiki-download_file.php?thumbnail&fileId={$f.id}'  title='{$f.name|escape}' id='thumb-{$f.id}' />
		{/foreach}
	</div>
</body>
</html>
