/*
 * cookie.js
 * by Garrett Smith
 * Loosely based on:
 * Cookie API v1.0
 * http://www.dithered.com/javascript/cookies/index.html
 * maintained by Chris Nott (chris@NOSPAMdithered.com - remove NOSPAM)
 */

function setPageCookie(name, value) {
	document.cookie = name + "=" + escape(value) + "; path=" + getPath();
}

function getCookie(name) {
	var dc = document.cookie;

	var prefix = name + "=";
	var begin = dc.lastIndexOf(prefix);

	if (begin == -1)
		return null;

	var end = dc.indexOf(";", begin);

	if (end == -1)
		end = dc.length;

	return unescape(dc.substring(begin + prefix.length, end));
}

function deletePageCookie(name, path) {
	var value = getCookie(name);

	if (value != null)
		document.cookie = name + "=" + "; path=" + getPath() + "; expires=Thu, 01-Jan-70 00:00:01 GMT";

	return value;
}

function getFilename() {
	var href = window.location.href;

	var file = href.substring(href.lastIndexOf("/") + 1);
	return file;
}

function getPath() {
	var href = window.location.href;

	var path = href.substring(href.indexOf("//") + 2);
	path = path.substring(path.indexOf("/"));
	path = path.substring(0, path.lastIndexOf("/") + 1);
	return path;
}