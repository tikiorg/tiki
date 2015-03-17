$.fn.extend({
	cartProductClassMissingForm: function(settings){
		settings = $.extend({
			informationForm: ''
		}, settings);
		
		$(this).each(function() {
			var formParent = $(this).submit(function(){
				if (formParent.attr('satisfied')) return true;
				formParent.addClass('hasMissingForm');
				
				var formDialog = window.formDialog = $('<div id="formDialog" />').load('tiki-index_raw.php?page=' + tiki_encodeURIComponent(settings.informationForm), function(){
					formDialog.dialog({
						title: settings.informationForm,
						modal: true,
						height: $(window).height() * 0.8,
						width: $(window).width() * 0.8
					});
					
					var loading = $('<div><span>Loading...</span><img src=\"img/loading.gif\" /></div>').hide().appendTo(formDialog);
					
					var forms = formDialog.find('form');
					forms.each(function(){
						var form = $(this).submit(function(){
						
							var satisfied = true;
							$('.mandatory_field').each(function(){
								var field = $(this).children().first();
								if (!field.val()) {
									$(this).addClass('ui-state-error');
									satisfied = false;
								}
							});
							if (!satisfied) 
								return false;
							
							$.post(form.attr('action'), form.serialize(), function(){
								form
									.slideUp(function(){
										formDialog.animate({
											scrollTop: form.next().offset().top
										});
									})
									.attr('satisfied', true);
								
								satisfied = true;
								
								forms.each(function(){
									if (!$(this).attr('satisfied')) {
										satisfied = false;
									}
								});
								
								if (satisfied) {
									loading.show()
										.prevAll()
										.hide();
									
									formParent
										.attr('satisfied', true)
										.submit();
								}
							});
							
							return false;
						});
					});
				});
				return false;
			});
		});
	},
	cartAjaxAdd: function() {

		$(this).each(function() {
			var form = $(this);
			form.submit(function() {
				if (form.hasClass('hasMissingForm') && !form.attr('satisfied')) return false;

				$("div.box-cart").tikiModal(" ");
				var itemData = {}, params = form.data("params");
				$.each(params, function (k, v) {
					itemData["params~" + k] = v;
				});

				$("input[type!=submit]", form).each( function (k, el) {
					itemData[$(el).attr("name")] = $(el).val();
				});

				$(document).trigger("cart.addtocart.start", [itemData]);

				$.post($.service("payment", "addtocart"), itemData, function(data) {
					if(data) {

						$("div.box-cart").each(function () {
							var $this = $(this);
							$.get($.service("module", "execute"), {
								module: "cart",
								moduleId: $(this).attr("id").replace("module_", "")
							}, function (html) {
								$this.tikiModal();
								$this.replaceWith(html);
								$(document).trigger("cart.addtocart.complete", [itemData]);
							});
						});
					} else {
						$("div.box-cart").tikiModal();
					}
					if (window.formDialog) {
						window.formDialog.dialog("destroy");
						window.formDialog = null;
					}
				}, "json").error(function (jqxhr, error, type) {
					$("div.box-cart").tikiModal();
					$(document).trigger("cart.addtocart.error", [itemData]);
				});
				
				return false;
			});
		});
	}
});

$(document).ready(function () {
	var cartSubmit = function () {
		var data = {
			module: "cart",
			moduleId: $(this).parents("div.box-cart").attr("id").replace("module_", "")
		};
		var $form = $(this);
		$form.tikiModal(" ");
		if (! $.trim(data.moduleId) || isNaN(data.moduleId)) {	// module in wikiplugin?
			delete data.moduleId;	// get the params from the data on the form
			var params = $(".mod-cart-checkout-form", $form.parent()).data("params");
			$.each(params, function (k, v) {
				data["params~" + k] = v;
			});
		}
		$("input", $form).each(function (k, v) {
			data[v['name']] = v['value'];
		});
		$.post($.service("module", "execute"), data, function (html) {
			$form.tikiModal();
			$form.parents("div.box-cart").replaceWith(html);
			$("form.mod-cart-item-form").submit(cartSubmit);
			$(document).trigger("cart.addtocart.complete");
		}, "html");

		return false;
	};
	$("form.mod-cart-item-form").on("submit", cartSubmit);
});