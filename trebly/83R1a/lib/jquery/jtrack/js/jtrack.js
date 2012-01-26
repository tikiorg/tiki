var jTask = {
	showArchived: false,
	showCompleted: false,
	intervals: [],
	timer: [],
	bind: function () {
		$(".jtrack-create").live("click", function (e) {
			e.preventDefault();
			$(".jtrack-form").hide();
			$("#jtrack-form-create").show();
		});
		$(".jtrack-update").live("click", function (e) {
			e.preventDefault();
			$(".jtrack-form").hide();
			var namespace = $(this).attr("rel");
			$("#jtrack-button-update").attr("rel", namespace);
			var est = $.DOMCached.get('estimate', namespace);
			if (est !== null) {
				$("#jtrack-form-update input[name='jtrack-task-name']").val(namespace);
				$("#jtrack-form-update input[name='jtrack-task-estimate']").val(est);
				$("#jtrack-form-update input[name='jtrack-task-completed']").attr("checked", $.DOMCached.get("completed", namespace));
				$("#jtrack-form-update input[name='jtrack-task-archived']").attr("checked", $.DOMCached.get("archived", namespace));								
				$("#jtrack-form-update span#created").text($.DOMCached.get('created', namespace));
			}
			$("#jtrack-form-update").show();
		});
		$(".jtrack-remove").live("click", function (e) {
			e.preventDefault();
			$(".jtrack-form").hide();
			$("#jtrack-button-remove").attr("rel", $(this).attr("rel"));
			$("#jtrack-remove-confirm").html("Are you sure you want to delete <strong>" + $(this).attr("rel") + "</strong>?");
			$("#jtrack-form-remove").show();
		});
		$(".jtrack-remove-all").live("click", function (e) {
			e.preventDefault();
			$(".jtrack-form").hide();
			$("#jtrack-form-remove-all").show();
		});
		$("#jtrack-show-archived").bind("click change", function () {
			jTask.showArchived = $(this).is(":checked");
			jTask.index();
		});
		$("#jtrack-show-completed").bind("click change", function () {
			jTask.showCompleted = $(this).is(":checked");
			jTask.index();
		});
		$(".jtrack-cancel").live("click", function (e) {
			e.preventDefault();
			$("#" + $(this).attr("rel")).hide().find("input:text").val("");
			$("#jtrack-form-list").show();
		});
		$(".jtrack-power").live("click", function (e) {
			e.preventDefault();
			jTask.toggleTimer($(this), $(this).attr("rel"));
		})
		$("#jtrack-button-remove").live("click", function () {
			$.DOMCached.deleteNamespace($(this).attr("rel"));
			$(this).attr("rel", "");
			$("#jtrack-form-remove").hide();
			jTask.index();		
		});		
		$("#jtrack-button-remove-all").live("click", function () {
			$.DOMCached.flush_all();
			$("#jtrack-form-remove-all").hide();
			jTask.index();		
		});
		$("#jtrack-button-create").live("click", function () {
			var namespace = $("#jtrack-form-create :input[name='jtrack-task-name']").val();		
			if ($.DOMCached.get('estimate', namespace) === null) {
				$.DOMCached.set('estimate', $("#jtrack-form-create :input[name='jtrack-task-estimate']").val(), false, namespace);
				$.DOMCached.set('timer', 0, false, namespace);
				$.DOMCached.set('started', false, false, namespace);
				$.DOMCached.set('completed', false, false, namespace);
				$.DOMCached.set('archived', false, false, namespace);
				var d = new Date();
				var created = [d.getDate(), d.getMonth() + 1, d.getFullYear()]; 
				$.DOMCached.set('created', created.join("."), false, namespace);
				$("#jtrack-create-status").hide().text("");
				$("#jtrack-form-create").hide().find("input:text").val("");
				jTask.index();
			} else {
				$("#jtrack-create-status").text("Task with the same name already exists.").show();
			}
		});
		
		$("#jtrack-button-update").live("click", function () {
			var ns = $(this).attr("rel");
			var namespace = $("#jtrack-form-update :input[name='jtrack-task-name']").val();
			
			if (ns === namespace) {
				// update
				$.DOMCached.set('estimate', $("#jtrack-form-update :input[name='jtrack-task-estimate']").val(), false, namespace);
				if ($("#jtrack-form-update input[name='jtrack-task-completed']").is(":checked")) {
					$.DOMCached.set("completed", true, false, namespace);
					$.DOMCached.set("started", false, false, namespace);
				} else {
					$.DOMCached.set("completed", false, false, namespace);
				}
				if ($("#jtrack-form-update input[name='jtrack-task-archived']").is(":checked")) {
					$.DOMCached.set("archived", true, false, namespace);
					$.DOMCached.set("started", false, false, namespace);
				} else {
					$.DOMCached.set("archived", false, false, namespace);
				}
			} else {
				// rename
				if ($.DOMCached.getNamespace(namespace) === null) {
					var timer = $.DOMCached.get('timer', ns),
						created = $.DOMCached.get('created', ns);
					$.DOMCached.set('estimate', $("#jtrack-form-update :input[name='jtrack-task-estimate']").val(), false, namespace);
					$.DOMCached.set('timer', timer, false, namespace);
					$.DOMCached.set('created', created, false, namespace);
					$.DOMCached.deleteNamespace(ns);
				} else {
					$("#jtrack-update-status").text("Task with the same name already exists.").show();
					return;
				}			
			}
			$(this).attr("rel", "");
			$("#jtrack-update-status").hide().text("");
			$("#jtrack-form-update").hide().find("input:text").val("");
			jTask.index();
		});
	},
	index: function () {
		var p = '',
			conditions = [],
			created,
			archived,
			completed,
			namespace,
			started = [],
			storage = $.DOMCached.getStorage();
		conditions.push('true');
		if (!this.showArchived) {
			conditions.push('!archived');
		}
		if (!this.showCompleted) {
			conditions.push('!completed');
		}
		for (namespace in storage) {
			archived = $.DOMCached.get("archived", namespace);
			completed = $.DOMCached.get("completed", namespace);			
			if (eval(conditions.join(' && '))) {
				created = $.DOMCached.get("created", namespace);							
				started[namespace] = $.DOMCached.get("started", namespace);
				jTask.timer[namespace] = $.DOMCached.get("timer", namespace);
				p += '<p class="jtrack-item' + (archived ? ' jtrack-archived' : '') + (completed ? ' jtrack-completed' : '') + '">' + created + '<label>' + namespace + '</label><a href="#" class="jtrack-update" rel="' + namespace + '">Edit</a> | <a href="#" class="jtrack-remove" rel="' + namespace + '">Delete</a><span class="jtrack-timer">' + this.hms(jTask.timer[namespace]) + '</span><a href="#" class="jtrack-power' + (started[namespace] ? ' jtrack-power-on' : '') + '" title="Timer on/off" rel="' + namespace + '"></a></p>';
				if (started[namespace]) {
					this.timerScheduler(namespace);
				}
			}
		}
		if (p === '') {
			p = '<p><label>No tasks</label></p>';
		}
		$("#jtrack-form-list").empty().append(p).show();
	},
	init: function () {
		this.bind();
		this.index();
	},
	timerScheduler: function (namespace) {
		clearInterval(this.intervals[namespace]);
		this.intervals[namespace] = setInterval(function () {
			if ($.DOMCached.get("started", namespace)) {
				jTask.timer[namespace]++;
				$.DOMCached.set("timer", jTask.timer[namespace], false, namespace);
				$(".jtrack-power[rel='" + namespace + "']").siblings(".jtrack-timer").eq(0).text(jTask.hms(jTask.timer[namespace]));
			}
		}, 1000);
	},
	toggleTimer: function (jQ, namespace) {
		if (!$.DOMCached.get("started", namespace)) {
			$.DOMCached.set("started", true, false, namespace);
			this.timer[namespace] = $.DOMCached.get("timer", namespace);
			this.timerScheduler(namespace);
			jQ.addClass("jtrack-power-on");
		} else {
			$.DOMCached.set("started", false, false, namespace);
			jQ.removeClass("jtrack-power-on");
		}
	},
	hms: function (secs) {
		secs = secs % 86400;
		var time = [0, 0, secs], i;
		for (i = 2; i > 0; i--) {
			time[i - 1] = Math.floor(time[i] / 60);
			time[i] = time[i] % 60;
			if (time[i] < 10) {
				time[i] = '0' + time[i];
			}
		}
		return time.join(':');
	}
};