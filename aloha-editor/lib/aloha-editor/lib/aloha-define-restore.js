// Because almond.js clobbers these global variables, we preserve them.
// Also see aloha-define-preserve.js
if (Aloha.hasOwnProperty('_defineReplacedByAloha')) {
	define = Aloha._defineReplacedByAloha;
	require = Aloha._requireReplacedByAloha;
	requirejs = Aloha._requirejsReplacedByAloha;
}
