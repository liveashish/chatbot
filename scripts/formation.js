/*
 * Formation - jQuery Plugin
 * version: 1.1.0 (01/12/2011)
 * Created by: Matt Null
 * Examples and documentation at: http://mattnull.com/formation
 * c) 2010 Matt Null - www.mattnull.com
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */

(function($) {
 
	/**** define defaults ****/
	var form_config = {
		className : "formation",
		id : "formation",
		method : "POST",
		action :"",
		autoComplete : "on"
	},
	input_config = {
		className : "formation_input",
		name : "formation_input",
		type : "text",
		id : "",
		labelClass : "",
		labelID : ""
	},
	radio_config = {
		className : "formation_radio",
		name : "formation_radio",
		type : "radio",
		id : "",
		labelClass : "",
		labelID : "",
		legend: "List of Radios",
		numRequired: 0
	},
	checkbox_config = {
		className : "formation_checkbox",
		name : "formation_checkbox",
		type : "checkbox",
		id : "",
		labelClass : "",
		labelID : "",
		legend: "List of Checkboxes"
	},
	select_config = {
		id : "",
		className : "formation_select",
		name : "formation_select",
		labelClass : "",
		labelID : "",
		multiple: false
	},
	textarea_config = {
		className : "formation_textarea",
		name : "formation_textarea",
		cols : "30",
		rows : "5",
		labelClass : "",
		labelID : ""
	},
	button_config = {
		name : "formation_button",
		id : "formation_button",
		buttonValue : "Submit",
		labelClass : "",
		labelID : "",
                type :"submit",
		className: ""
	},
	captcha_config = {
		captchaQuestions : {'2 + 2 = ?' : '4','2 x 2 = ?': '4','(2 x 2) + 5 = ?': '9', '(4 + 3) x 2 = ?': '14','What color is the sky?':'blue'},
		special_type : 'captcha',
		type : 'text',
		className: 'formation_captcha'
	},
	validation = {
		blank : "Please complete this field.",
		number : "Numbers only.",
		email : "Invalid e-mail.",
		phone : "Invalid phone number.",
		zip : "Invalid zip code.",
		url : "Invalid URL.",
		captcha: "Invalid Captcha answer."
	},
	regex = {
		//http://projects.scottsplayground.com/email_address_validation/
		email : /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
		//http://projects.scottsplayground.com/iri/
		url : /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
		phone : /^\([1-9]\d{2}\)\s?\d{3}\-\d{4}$/,
		zip: /(^\d{5}$)|(^\d{5}-\d{4}$)/,
		form_id : /form_gen/gi
	},
	prefix = "formgen",
	error_class ="error",
	item_li = $('<li></li>');

	/**** Helper Functions ****/

	function $form(attr) { 
		var id = attr.id ? ' id="' + attr.id + '"' : '',
		className = attr.className ? 'class="' + attr.className +'"' : '',
		action = attr.action ? 'action="' + attr.action +'"' : '',
		method = attr.method ? 'method="' + attr.method +'"' : '';
		autoComplete = attr.autoComplete ? 'autoComplete="' + attr.autoComplete + '"' : '';
		return $('<form' + id +' '+ className + ' '+ action + ' '+ method + ' ' + autoComplete + '><ul></ul></form>');
	}
	
	function $label(attr) { 

		var id = attr.labelID ? ' id="' + attr.labelID + '"' : '',
		className = attr.labelClass ? 'class="' + attr.labelClass +'"' : '',
		error_class = 'class="small"';
		
		if(attr.captchaQuestions){
			var questions = [],
				answers = [];
			//seperate questions and answers into seperate arrays
			for(var i in attr.captchaQuestions){
				questions.push(i);
				answers.push(attr.captchaQuestions[i]);
			}

			var question_count = questions.length,
			key = Math.floor(Math.random()*question_count),
			val = questions[key];
			
			this.captcha_answer_key = key;
			this.captcha_answers = answers;
		}
		else{
			if(attr.labelValue == '' || !attr.labelValue) return;
			var val = attr.labelValue;
		}
		return $('<label' + id +' '+ className + '>'+ val +'<span '+ error_class +'></span></label>');
	}
	function $select(attr) { 
		var id = attr.id ? ' id="' + attr.id + '"' : '',
		name = attr.name ? 'name="' + attr.name +'"' : '',
		className = attr.className ? 'class="' + attr.className +'"' : '',
		multiple = attr.multiple ? 'multiple="multiple"' : '';
		
		return $('<select' + id +' '+ name +' '+ className +' '+ multiple + '></select>');
	}
	
	function $input(attr) { 
		var id = attr.id ? ' id="' + attr.id + '"' : '',
		type = attr.type ? 'type="' + attr.type +'"' : '',
		is_captcha = (attr.special_type == 'captcha') ? 'is_captcha' : '',
		name = attr.name ? 'name="' + attr.name +'"' : '',
		className = attr.className ? 'class="' + attr.className +' ' +is_captcha+'"' : '',
		value = attr.defaultValue ? 'value="' + attr.defaultValue +'"' : '';
		
		return $('<input' + id +' '+ type +' '+ name +' '+ className +' '+ value +' />');
	} 
	
	function $option(values){
		var options = '';
		for(var key in values){
			options += "<option value="+key+">"+values[key]+"</option>";
		}
		return options;
	}
	
	function $radios(values,attr){
		var id = attr.id ? " id='" + attr.id + "'" : "",
		type = attr.type ? "type='" + attr.type +"'" : "",
		name = attr.name ? "name='" + attr.name +"'" : "",
		className = attr.className ? "class='" + attr.className +"'" : "",
		for_attr = attr.name ? "for='"+attr.name+"'" : "",
		legend = attr.legend ? attr.legend : '',
		required = (attr.required == true) ? ' required' : '',
		radios = '<fieldset class="radio'+required+'"><legend>'+legend+'</legend><ul>';
		
		for(var key in values){
			radios +="<li><label "+for_attr+">"+values[key]+"</label><input value='"+key+"' "+name+" "+type+" "+className+" "+id+" /></li>";
		}
		
		radios+='</ul></fieldset>';
		return radios;
	}
	
	function $checkboxes(values,attr){
		var id = attr.id ? " id='" + attr.id + "'" : "",
		type = attr.type ? "type='" + attr.type +"'" : "",
		className = attr.className ? "class='" + attr.className +"'" : "",
		for_attr = attr.name ? "for='"+attr.name+"'" : "",
		legend = attr.legend ? attr.legend : '',
		i=0,
		required = (attr.required == true) ? ' required' : '',
		checkboxes='<fieldset class="checkbox'+required+'"><legend>'+legend+'</legend><ul>',
		inc_name = name + '-',
		name='';

		for(var key in values){
			name = attr.name ? "name='" + attr.name +"[]'" : "";
			checkboxes +="<li><label "+for_attr+">"+values[key]+"</label><input value='"+key+"' "+name+" "+type+" "+className+" "+id+" /></li>";
		}
		
		checkboxes+='</ul></fieldset>';
		return checkboxes;
	}
	
	function $textarea(attr) { 
		var id = attr.id ? ' id="' + attr.id + '"' : '',
		name = attr.name ? 'name="' + attr.name +'"' : '',
		className = attr.className ? 'class="' + attr.className +'"' : '',
		cols = attr.cols ? 'cols="'+ attr.cols +'"':'',
		rows = attr.rows ? 'rows="'+ attr.rows +'"':'';
		return $('<textarea' + id +' '+ name +' '+ className +' '+ rows + ' '+ cols + '></textarea>');
	}
	
	function $button(attr) { 
		var id = attr.id ? ' id="' + attr.id + '"' : '',
		name = attr.name ? 'name="' + attr.name +'"' : '',
		className = attr.className ? 'class="' + attr.className +'"' : 'class="button"',
		value = attr.value ? 'value="' + attr.value +'"' : '',
		type = ' type='+attr.type+'';
		return $('<input' + type +' '+ id +' '+ name +' '+ className +' '+value+' />');
	}
	
	function validate(form){

		var errors = [],
		context = this;

		form.children('ul').children('li').contents().each(function(){	
			var el = $(this),
				error_span = el.parent().children('label').children('span.small'),
				val = el.val(),
				error = false;
			
			//if the element is not a form element then return
			if(!el.is('fieldset') && !el.is('textarea') && !el.is('input') && !el.is('select')) return;
	
			//is radio or checkbox group question
			if(el.is('fieldset.radio') || el.is('fieldset.checkbox')){
				if(el.hasClass('required')){

					if(el.find('input').is(':checkbox') || el.find('input').is(':radio')){
						var checked = false,
						isGroup = true,
						items = el.find('input');	

						items.each(function(){
							if($(this).is(':checked')){
								checked = true;
							}
						});
						if(!checked){
							error_span.html(validation.blank);
							el.addClass('error');
							error = true;
						}
					}
				}
			}
			else if(val != ''){
				if(el.hasClass('is_captcha')){
					if(val.toLowerCase() != context.captcha_answers[context.captcha_answer_key].toLowerCase()){
						error_span.html(validation.captcha);
						error = true;
					}
				}
				
				if(el.hasClass('number')){
					var val = val.replace(/,/g,'');	
					if(isNaN(val)){
						error_span.html(validation.number);
						error = true;
					}
				}
				else if(el.hasClass('email')){ //e-mail validation
					if(!regex.email.test(val)){
						error_span.html(validation.email);
						error = true;
					}
				}
				else if(el.hasClass('url')){ //url validation
					if(!regex.url.test(val)){
						error_span.html(validation.url);
						error = true;
					}
				}
				else if(el.hasClass('phone')){ //phone number validation
					if(!regex.phone.test(val)){
						error_span.html(validation.phone);
						error = true;
					}
				}
				else if(el.hasClass('zip')){ //e-mail validation
					if(!regex.zip.test(val)){
						error_span.html(validation.zip);
						error = true;
					}
				}
			}	
							
			if(el.hasClass('error') && error == false){
				error_span.removeClass("error");
				error_span.html('');
				el.removeClass("error");
			}
						
			if(el.hasClass('required') && !isGroup){
				if(!val || val == ''){
					error_span.html(validation.blank);
					error = true;
				}
			}
			
			if(error){ //if there is an error add the appropriate classes
				el.addClass("error");		
				error_span.addClass("error");
				errors.push(error);
			}
		});
	
		if(errors.length > 0){
			form.addClass('hasErrors');
			return false;
		} 
		else if(form.hasClass('hasErrors')){
			form.removeClass('hasErrors');
		}
		
		return true;
	}
	
	/***** Public Methods ********************/
	/* format: $.fn.formation.addSelect();
	/*****************************************/
	
	publicMethod = $.fn.formation = $.formation = function(params) {
		
		params = params || {};

		if(params) var config = $.extend({},form_config, params);
		
		//check to see if there are any forms with the form_gen ID
		//if there is make the ID unique
					
		var forms = $('form').filter(function(){
        	return this.id.match(regex.form_id);
   		});

		if(forms.size() > 0 && !params.id){
			config.id = config.id +'-'+ (forms.size() + 1);
		}
		else if(params.id){
			if(params.id.match(regex.form_id)){
				config.id = config.id +'-'+ (forms.size() + 1);
			}
		}

		return publicMethod.init(this,config);
		
	};
	
	//creates the form and sets variables
	publicMethod.init = function(parent, params){
		params = params || {};
		var form = $form(params);
		this.form = form;
		form.submit(function(){if(!validate(form) || params.isAjax == true){return false;}});
		parent.append(form);
		
		this.parent = form.children('ul');
		
		return form;
	};
	
	publicMethod.addSelect = function(values,params){
		params = params || {};
		if(params) var config = $.extend({},select_config, params);
		
		var select = $select(config),
		form = this.parent,
		options = $option(values),
		li = $('<li></li>');

		if(config.required == true){
			select.addClass('required');
		}

		//append element
		if(config.after){
			var after_li = $(config.after).parent('li');
			after_li.after(li.append(select.append(options)));
			after_li.after(li.append($label(config)));
			after_li.after(li);
		}
		else{		
			//add labels
			li.append($label(config));
			li.append(select.append(options));				
			this.parent.append(li);
		}
		return select;
	};
	
	publicMethod.addButton = function(params){
		params = params || {};
		if(params) var config = $.extend({},button_config, params);
		var li = $('<li></li>'),
		button = $button(config);
		li.append(button);
		this.parent.append(li);
		
		return button;
	};
	
	publicMethod.addInput = function(params){
		params = params || {};
		if(params)var config = $.extend({},input_config, params);

		var input = $input(config),
		li = $('<li></li>');

		//add labels
		li.append($label(config));
		
		if(config.required == true){
			input.addClass('required');
		}
		if(config.validation){
			input.addClass(config.validation);
		}
		
		//append element
		li.append(input);
		this.parent.append(li);
		
		return input;
	};
	
	publicMethod.addTextarea = function(params){
		params = params || {};
		if(params) var config = $.extend({},textarea_config,params);

		var textarea = $textarea(config),
		li = $('<li></li>');
		
		//add labels
		li.append($label(config));
		
		if(config.required == true){
			textarea.addClass('required');
		}

		li.append(textarea);
		this.parent.append(li);
		
		return textarea;
	};
	
	publicMethod.addRadios = function(values,params){
		params = params || {};
		if(params) var config = $.extend({},radio_config,params);
		
		var radios = $radios(values,config),
		li = $('<li></li>');
		
		//add labels
		li.append($label(config));

		li.append(radios);
		this.parent.append(li);
	};
	
	publicMethod.addCaptcha = function(params){
		params = params || {};
		if(params)var config = $.extend({},captcha_config, params);

		var input = $input(config),
		li = $('<li></li>');

		//add labels
		li.append($label(config));
		
		if(config.required == true){
			input.addClass('required');
		}
		if(config.validation){
			input.addClass(config.validation);
		}
		
		//append element
		li.append(input);
		this.parent.append(li);
	};
	
	publicMethod.addCheckboxes = function(values,params){
		params = params || {};
		if(params) var config = $.extend({},checkbox_config,params);
		
		var checkboxes = $checkboxes(values,config),
		li = $('<li></li>');
		//add labels
		li.append($label(config));

		li.append(checkboxes);
		this.parent.append(li);
	};
	
	publicMethod.setErrorMessages = function(params){
		$.extend(validation,params);
	};
	
})(jQuery);

