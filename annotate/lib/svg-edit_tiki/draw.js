$(function() {
	svgEditor.setConfig({
		extPath: 'lib/svg-edit/extensions/',
		jGraduatePath: 'lib/svg-edit/jgraduate/images/',
		curConfig: {
			imgPath: "lib/svg-edit/images/"
		}
	});
	svgEditor.curConfig.imgPath = 'lib/svg-edit/images/';
	svgEditor.curConfig.langPath = 'lib/svg-edit/locale/';
	svgEditor.curConfig.extPath = 'lib/svg-edit/extensions/';
	svgEditor.curConfig.jGraduatePat = 'lib/svg-edit/jgraduate/images/';	
	$(svgEditor.init);
});
