	
	Number.prototype.number_format = function (decimals, dec_point, thousands_sep) {
		if ( decimals==undefined ) {
			decimals = 0;
		}
		else if ( isNaN(decimals=parseInt(decimals)) ) {
			throw new Error("Неверный формат параметра \"decimals\"!")
		}
		if ( dec_point==undefined ) {
			dec_point = ".";
			thousands_sep = ",";
		}
		else if ( thousands_sep==undefined ) {
			throw new Error("Параметр \"thousands_sep\" является обязательным при задании параметра \"dec_point\"!");
		}
		else {
			dec_point = dec_point.toString();
			thousands_sep = thousands_sep.toString();
		}
		
		var str_parts = this.toFixed(decimals).split(".");
		var first_thousand = str_parts[0].length % 3 || 3;
		return str_parts[0].substr(0, first_thousand)
			+ str_parts[0].substr(first_thousand).replace(/\d{3}/g, thousands_sep.replace(/\$/g, "$$$$")+"$&")
			+ (str_parts[1]!=undefined ? dec_point+str_parts[1] : "");
	}
	
	var set_shop_cart_params = function () {
		var all_num = 0;
		var all_sum = 0.0;
		$("span.num_param").each(function() {
			all_num += parseInt($(this).text());
		});
		$("span.sum_param").each(function() {
			all_sum += parseFloat($(this).text().replace(/\s/g, ""));
		});
		if ( all_num > 0 ) {
			$("#sc_num").text(" (" + all_num + ")");
			$("#num_param").text(all_num.number_format(0));
			$("#sum_param").html(all_sum.number_format(2, ".", "&nbsp;"));
		}
		else {
			$("div.all_cart_act").remove();
			$("#sc_num").text("");
			$("#info_param").text("Корзина пуста");
		}
	}
	
	var increment_shop_cart_menu = function (num) {
		num = parseInt(num);
		if ( isNaN(num) ) return;
		
		var old_num = parseInt($("#sc_num").text().replace(/[()]/g, ""));
		if ( num > 0 ) $("#sc_num").text(" (" + (isNaN(old_num) ? num : num + old_num) + ")");
	}
	
	var change_full_item_image_click = function(event) {
		var filename = $(this).attr("src").split("/");
		filename = filename[filename.length-1];
		var full_item_image = $("#full_item_image");
		var img_loader = $("#img_loader");
		if (event.data.add_str < 1000) filename += "?"+event.data.add_str;
		img_loader.css({
			"display": "block",
			"left": (parseInt(full_item_image.css("width"))/2)-(parseInt(img_loader.css("width"))/2),
			"top": (parseInt(full_item_image.css("height"))/2)-(parseInt(img_loader.css("height"))/2)
		});
		full_item_image.attr("src", "/images/shop_items/full/"+filename);
	}
	
	var full_item_image_load = function(event) {
		$("#img_loader").css("display", "none");
	}
	
	var form_add_from_list_submit = function(event) {
		event.preventDefault();
		var this_form = $(this);
		var num = parseInt($("input[name='num']", this_form).val());
		if ( isNaN(num) ) {
			alert("Некорректно введено количество!");
			return;
		}
		var form_button = $("button", this_form.parent("div"))
		form_button.attr("disabled", true);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/",
			data: {
				"_ajax": 1,
				"_action": "manage_cart",
				"increment": 1,
				"uin": $("input[name='uin']", this_form).val(),
				"size": $("select[name='size']", this_form).val(),
				"num": num
			},
			success: function(data) {
				increment_shop_cart_menu(num);
				form_button.attr("disabled", false);
				form_button.replaceWith($("<span>").css({"font-weight": "bold", "color": "green"})
					.text("Товар добавлен в корзину!").delay(300).fadeOut(300, function() {
						$(this).replaceWith(form_button);
						$(this).fadeIn(300);
					}));
			}
		});
	}
	
	var form_add_item_submit = function(event) {
		event.preventDefault();
		var this_form = $(this);
		var num = parseInt($("input[name='num']", this_form).val());
		if ( isNaN(num) ) {
			alert("Некорректно введено количество!");
			return;
		}
		var form_button = $("button", this_form);
		form_button.attr("disabled", true);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/",
			data: {
				"_ajax": 1,
				"_action": "manage_cart",
				"increment": 1,
				"uin": $("input[name='uin']", this_form).val(),
				"size": $("select[name='size']", this_form).val(),
				"num": num
			},
			success: function(data) {
				increment_shop_cart_menu(num);
				form_button.attr("disabled", false);
				form_button.replaceWith($("<span>").css({"font-weight": "bold", "color": "green"})
					.text("Товар добавлен в корзину!").delay(300).fadeOut(300, function() {
						$(this).replaceWith(form_button);
						$(this).fadeIn(300);
					}));
			}
		});
	}
	
	var form_cart_item_plus_submit = function(event) {
		event.preventDefault();
		var this_form = $(this);
		var new_num = parseInt($("input[name='num']", this_form).val());
		$("button", this_form.parents("div.shop_cart_item")).attr("disabled", true);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/",
			data: {
				"_ajax": 1,
				"_action": "manage_cart",
				"uin": $("input[name='uin']", this_form).val(),
				"size": $("input[name='size']", this_form).val(),
				"num": new_num
			},
			success: function(data) {
				$("span.num_param", this_form.parents("div.shop_cart_item")).text(new_num.number_format(0));
				$("span.sum_param", this_form.parents("div.shop_cart_item")).text(
					($("span.price_param", this_form.parents("div.shop_cart_item")).text().replace(/\s/g, "")*new_num).number_format(2, ".", " ")
				);
				$("input[name='num']", this_form).val(new_num+1);
				$("input[name='num']", $("form[name^='fm']", this_form.parents("div.shop_cart_item"))).val(new_num-1);
				set_shop_cart_params();
				$("button", this_form.parents("div.shop_cart_item")).attr("disabled", false);
			}
		});
	}
	
	var form_cart_item_minus_submit = function(event) {
		event.preventDefault();
		var this_form = $(this);
		var new_num = parseInt($("input[name='num']", this_form).val());
		if ( new_num < 1 ) return;
		$("button", this_form.parents("div.shop_cart_item")).attr("disabled", true);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/",
			data: {
				"_ajax": 1,
				"_action": "manage_cart",
				"uin": $("input[name='uin']", this_form).val(),
				"size": $("input[name='size']", this_form).val(),
				"num": new_num
			},
			success: function(data) {
				$("span.num_param", this_form.parents("div.shop_cart_item")).text(new_num.number_format(0));
				$("span.sum_param", this_form.parents("div.shop_cart_item")).text(
					($("span.price_param", this_form.parents("div.shop_cart_item")).text().replace(/\s/g, "")*new_num).number_format(2, ".", " ")
				);
				$("input[name='num']", this_form).val(new_num-1);
				$("input[name='num']", $("form[name^='fp']", this_form.parents("div.shop_cart_item"))).val(new_num+1);
				set_shop_cart_params();
				$((new_num>1 ? "button" : "button:not([name^='sbm'])"), this_form.parents("div.shop_cart_item")).attr("disabled", false);
			}
		});
	}
	
	var form_cart_delete_item_submit = function(event) {
		event.preventDefault();
		if (!confirm("Удалить \"" + $("span.name_art_param", $(this).parents("div.shop_cart_item")).text() + "\" из корзины?")) return;
		var this_form = $(this);
		$("button", this_form.parents("div.shop_cart_item")).attr("disabled", true);
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "/",
			data: {
				"_ajax": 1,
				"_action": "delete_from_cart",
				"uin": $("input[name='uin']", this_form).val(),
				"size": $("input[name='size']", this_form).val()
			},
			success: function(data) {
				this_form.parents("div.shop_cart_item").slideUp(300, function() {
					$(this).remove();
					set_shop_cart_params();
				});
			}
		});
	}
	
	var form_clear_cart_submit = function(event) {
		if (confirm("Корзина будет полностью очищена.\nПродолжить?")) {
			$("button", $(this)).attr("disabled", true);
		}
		else {
			event.preventDefault();
		}
	}
	
	var captcha_refresh_click = function(event) {
		event.preventDefault();
		$("#captcha").attr("src", "/kcaptcha/index.php?"+Math.floor(Math.random()*1000));
	}
	
	$(function(){
		$(".change_full_item_image").css("cursor", "pointer");
		$(".change_full_item_image:first").click({"add_str": 1000}, change_full_item_image_click);
		$(".change_full_item_image:not(:first)").click({"add_str": Math.floor(Math.random()*1000)}, change_full_item_image_click);
		$("#full_item_image").load(full_item_image_load);
		$("form[name^='fal']").submit(form_add_from_list_submit);
		$("form[name='fai']").submit(form_add_item_submit);
		$("form[name^='fp']").submit(form_cart_item_plus_submit);
		$("form[name^='fm']").submit(form_cart_item_minus_submit);
		$("form[name^='fd']").submit(form_cart_delete_item_submit);
		$("form[name='fc']").submit(form_clear_cart_submit);
		$("#captcha_refresh").attr("disabled", false);
		$("#captcha_refresh").click(captcha_refresh_click);
	});
	