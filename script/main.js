	
	Number.prototype.toFixedFix = function(fractionDigits) {
		if ( isNaN(parseFloat(this)) ) throw new Error("Неверный объект");
		if ( fractionDigits == undefined ) {
			fractionDigits = 0;
		}
		else if ( isNaN(fractionDigits = Math.abs(parseInt(fractionDigits))) ) {
			throw new Error("Неверный формат параметра \"fractionDigits\"!");
		}
		var k = Math.pow(10, fractionDigits);
		var result = "" + (Math.round(this * k) / k);
		if ( fractionDigits > 0 ) {
			var dec_part = result.split(".")[1] || "";
			result += dec_part.length > 0 ? "" : ".";
			for ( var i = dec_part.length; i < fractionDigits; i++ ) result += "0";
		}
		return result;
	}
	
	Number.prototype.number_format = function(decimals, dec_point, thousands_sep) {
		if ( isNaN(parseFloat(this)) ) throw new Error("Неверный объект");
		if ( decimals == undefined ) {
			decimals = 0;
		}
		else if ( isNaN(decimals = Math.abs(parseInt(decimals))) ) {
			throw new Error("Неверный формат параметра \"decimals\"!");
		}
		if ( dec_point==undefined ) {
			dec_point = ".";
			thousands_sep = ",";
		}
		else if ( thousands_sep==undefined ) {
			throw new Error("Параметр \"thousands_sep\" является обязательным при задании параметра \"dec_point\"!");
		}
		else {
			dec_point = "" + dec_point;
			thousands_sep = "" + thousands_sep;
		}
		var str_parts = this.toFixedFix(decimals).split(".");
		return str_parts[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, thousands_sep.replace(/\$/g, "$$$$"))
			+ (decimals > 0 ? dec_point + str_parts[1] : "");
	}
	
	function set_shop_cart_params() {
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
			$("#num_param").text(all_num.number_format(0, ".", " "));
			$("#sum_param").text(all_sum.number_format(2, ".", " "));
		}
		else {
			$("div.all_cart_act").remove();
			$("#sc_num").text("");
			$("#info_param").text("Корзина пуста");
		}
	}
	
	function increment_shop_cart_menu(num) {
		if ( isNaN(num = parseInt(num)) ) return;
		var old_num = parseInt($("#sc_num").text().replace(/[()]/g, ""));
		if ( num > 0 ) $("#sc_num").text(" (" + (isNaN(old_num) ? num : num + old_num) + ")");
	}
	
	$(function(){
		$(".change_full_item_image").css("cursor", "pointer");
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
		$(".change_full_item_image:first").click({"add_str": 1000}, change_full_item_image_click);
		$(".change_full_item_image:not(:first)").click({"add_str": Math.floor(Math.random()*1000)}, change_full_item_image_click);
		
		$("#full_item_image").load(function(event) {
			$("#img_loader").css("display", "none");
		});
		
		$("form[name^='fal']").submit(function(event) {
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
		});
		
		$("form[name='fai']").submit(function(event) {
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
		});
		
		$("form[name^='fp']").submit(function(event) {
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
					$("span.num_param", this_form.parents("div.shop_cart_item")).text(new_num.number_format(0, ".", " "));
					$("span.sum_param", this_form.parents("div.shop_cart_item")).text(
						($("span.price_param", this_form.parents("div.shop_cart_item")).text().replace(/\s/g, "")*new_num).number_format(2, ".", " ")
					);
					$("input[name='num']", this_form).val(new_num+1);
					$("input[name='num']", $("form[name^='fm']", this_form.parents("div.shop_cart_item"))).val(new_num-1);
					set_shop_cart_params();
					$("button", this_form.parents("div.shop_cart_item")).attr("disabled", false);
				}
			});
		});
		
		$("form[name^='fm']").submit(function(event) {
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
					$("span.num_param", this_form.parents("div.shop_cart_item")).text(new_num.number_format(0, ".", " "));
					$("span.sum_param", this_form.parents("div.shop_cart_item")).text(
						($("span.price_param", this_form.parents("div.shop_cart_item")).text().replace(/\s/g, "")*new_num).number_format(2, ".", " ")
					);
					$("input[name='num']", this_form).val(new_num-1);
					$("input[name='num']", $("form[name^='fp']", this_form.parents("div.shop_cart_item"))).val(new_num+1);
					set_shop_cart_params();
					$((new_num>1 ? "button" : "button:not([name^='sbm'])"), this_form.parents("div.shop_cart_item")).attr("disabled", false);
				}
			});
		});
		
		$("form[name^='fd']").submit(function(event) {
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
		});
		
		$("form[name='fc']").submit(function(event) {
			if (confirm("Корзина будет полностью очищена.\nПродолжить?")) {
				$("button", $(this)).attr("disabled", true);
			}
			else {
				event.preventDefault();
			}
		});
		
		$("#captcha_refresh").attr("disabled", false);
		$("#captcha_refresh").click(function(event) {
			event.preventDefault();
			$("#captcha").attr("src", "/kcaptcha/index.php?"+Math.floor(Math.random()*1000));
		});
	});
	