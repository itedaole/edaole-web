var Common = {};
Common.trim = function(s){return s.replace(/(^\s*)|(\s*$)/g, "");};
var validator={
	errinput : 'errinput',
	errmsg : 'errmsg',
	errcls : 'no',
	yescls : 'yes',
	errorTip : 'errorTip',
	errorInput : 'errorInput',
	validTip   : 'validTip',
	require : /[^(^\s*)|(\s*$)]/,	
	email : /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
	domain: /^\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
	phone : /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/,
	mobile : /^1[345]\d{9}$|^18\d{9}$|^0\d{9,10}$/,
	url : /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/,
	idcard : "this.isIdCard(value)",
	money: /^\d+(\.\d+)?$/,
	number : /^\d+$/,
	zip : /^\d{6}$/,
	ip  : /^[\d\.]{7,15}$/,
	qq : /^[1-9]\d{4,9}$/,
	integer : /^[-\+]?\d+$/,
	double : /^[-\+]?\d+(\.\d+)?$/,
	english : /^[A-Za-z]+$/,
	chinese : /^[\u0391-\uFFE5]+$/,
	enandcn : /^[\w\u0391-\uFFE5][\w\u0391-\uFFE5\-\.]+$/,
	username : /^[\w]+[\-\.\w]{2,}$/i,
	unsafe : /[<>\?\#\$\*\&;\\\/\[\]\{\}=\(\)\.\^%,]/,
	safestring : "this.isSafe(value)",
	filter : "this.doFilter(value)",
	limit : "this.checkLimit(value.length)",
	limitb : "this.checkLimit(this.LenB(value))",
	limitc : "this.checkLimit(value.length)",
	date : "this.isDate(value)",
	repeat : "this.checkRepeat(value)",
	range : "this.checkRange(value)",
	compare : "this.checkCompare(value)",
	custom : "this.Exec(value)",
	group : "this.mustChecked()",
	ajax: "this.doajax(errindex)",

	isIdCard : function(number){
	var date, Ai;
	var verify = "10x98765432";
	var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
	var area = ['','','','','','','','','','','','北京','天津','河北','山西','内蒙古','','','','','','辽宁','吉林','黑龙江','','','','','','','','上海','江苏','浙江','安微','福建','江西','山东','','','','河南','湖北','湖南','广东','广西','海南','','','','重庆','四川','贵州','云南','西藏','','','','','','','陕西','甘肃','青海','宁夏','新疆','','','','','','台湾','','','','','','','','','','香港','澳门','','','','','','','','','国外'];

	var re = number.match(/^(\d{2})\d{4}(((\d{2})(\d{2})(\d{2})(\d{3}))|((\d{4})(\d{2})(\d{2})(\d{3}[x\d])))$/i);
	if(re == null) return false;
	if(re[1] >= area.length || area[re[1]] == "") return false;
	if(re[2].length == 12){
		Ai = number.substr(0, 17);
		date = [re[9], re[10], re[11]].join("-");
	} else {
		Ai = number.substr(0, 6) + "19" + number.substr(6);
		date = ["19" + re[4], re[5], re[6]].join("-");
	}
	if(!this.isDate(date, "ymd")) return false;
	var sum = 0;
	for(var i = 0;i<=16;i++){
		sum += Ai.charAt(i) * Wi[i];
	}
	Ai += verify.charAt(sum%11);

	return (number.length ==15 || number.length == 18 && number == Ai);
	},

	isSafe : function(str){
		 return !this.unsafe.test(str);
	},

	isDate : function(op){
		var formatString = this['element'].attr('format');
		formatString = formatString || "ymd";
		var m, year, month, day;
		switch(formatString){
		case "ymd" :
			m = op.match(new RegExp("^((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})$"));
			if(m == null ) return false;
			day = m[6];
			month = m[5]*1;
			year = (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
		break;
		case "dmy" :
			m = op.match(new RegExp("^(\\d{1,2})([-./])(\\d{1,2})\\2((\\d{4})|(\\d{2}))$"));
			if(m == null ) return false;
			day = m[1];
			month = m[3]*1;
			year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
		break;
		default :
			break;
		}
		if(!parseInt(month)) return false;
		month = month==0 ?12:month;
		var date = new Date(year, month-1, day);
		return (typeof(date) == "object" && year == date.getFullYear() && month == (date.getMonth()+1) && day == date.getDate());
		function GetFullYear(y){
			return ((y<30 ? "20" : "19") + y)|0;
		}
	}, //end isDate
	doFilter : function(value){
		var filter =this['element'].attr('accept');
		return new RegExp("^.+\.(?=EXT)(EXT)$".replace(/EXT/g,filter.split(/\s*,\s*/).join("|")),"gi").test(value);
	},

	checkLimit:function(len){
		var minval=this['element'].attr('min') ||Number.MIN_VALUE;
		var maxval=this['element'].attr('max') ||Number.MAX_VALUE;
		return (minval<= len && len<=maxval);
	},

	LenB : function(str){
		return str.replace(/[^\x00-\xff]/g,"**").length;
	},

	checkRepeat:function(value){
		var to = this['element'].attr('to');
		return value==jQuery('input[name="'+to+'"]').eq(0).val();
	},

	checkRange : function(value){
		value = value|0;
		var minval=this['element'].attr('min') || Number.MIN_VALUE;
		var maxval=this['element'].attr('max') || Number.MAX_VALUE;
		return (minval<=value && value<=maxval);
	},

	checkCompare : function(value){
		var compareid=this['element'].attr('compare');
		if(!value) return false;
		return jQuery('#'+compareid).attr('value') == value;
	},

	Exec : function(value){
		var reg = this['element'].attr('regexp');
		return new RegExp(reg,"gi").test(value);
	},

	mustChecked : function(){
		var tagName=this['element'].attr('name');
		var f=this['element'].parents('form');
		var n=f.find('input[name="'+tagName+'"][checked]').length;
		var count = f.find('input[name="'+tagName+'"]').length;
		var minval=this['element'].attr('min') || 1;
		var maxval=this['element'].attr('max') || count;
		return (minval<=n && n<=maxval);
	},

	doajax : function(value) {	
		var element = this['element'];
		var errindex = this['errindex'];
		var url=this['element'].attr('url');
		var vname=this['element'].attr('vname');
		var msgid = jQuery('#'+element.attr('msgid'));
		var val = this['element'].val();
		var str_errmsg=this['element'].attr('msg');
		var arr_errmsg ;
		var errmsg ;
		if(str_errmsg.indexOf('|')>-1) {
      		arr_errmsg= str_errmsg.split('|') ;
      		errmsg = arr_errmsg[errindex] ;
		} else {
      		errmsg='';
		}
		var type=this['element'].attr('type');
		var Charset = jQuery.browser.msie ? document.charset : document.characterSet;
		var methodtype = (Charset.toLowerCase() == 'utf-8') ? 'post' : 'get';
		var method=this['element'].attr('method') || methodtype;
		var name = this['element'].attr('name');
		if(url=="" || url==undefined) {
			alert('Please specify url');
			return false ;
		} else if (vname=="" || vname==undefined) {
			alert('Please specify vname');
			return false ;
		}
		if(url.indexOf('?')>-1){
			url = url+"&n="+vname+"&v="+escape(val);
		} else {
			url = url+'?n='+vname+"&v="+escape(val);
		}
		var s = $.ajax({
			type: method,
			url: url,
			data: {},
			cache: false,
			async: false,
			success: function(data){
					data = data.replace(/(^\s*)|(\s*$)/g, "");
					data = eval('('+data+')');
					if(data.error != 0){
						errmsg = errmsg=="" ? data.data : errmsg;
						(type!='checkbox' && type!='radio' && element.addClass(validator.errorInput));
						if (!errmsg) return false;
						if(msgid.length>0){ msgid.removeClass(validator.validTip).addClass(validator.errorTip).html(errmsg); } else{ jQuery("<span class='"+validator.errorTip+"'></span>").html(errmsg).insertAfter(element); }
						return false;
					} else if(data.error == 0) {
						if (!errmsg) return true;
						if(msgid.length>0){ msgid.removeClass(validator.errorTip).addClass(validator.validTip).html(''); } else { jQuery("<span class='"+validator.validTip+"'></span>").insertAfter(element); }
						return true;
				   }
			   }
		 }).responseText;
		 return (eval('('+s.replace(/(^\s*)|(\s*$)/g,"")+')').error == 0);
	}
};

// element 
validator.showErr=function (element, errindex){
	var str_errmsg=element.attr('msg') ||'';
	var arr_errmsg = str_errmsg.split('|');
	var errmsg = arr_errmsg[errindex]?arr_errmsg[errindex]:arr_errmsg[0];
	var msgid= jQuery('#'+element.attr('msgid'));
	var type=element.attr('type');
	(type!='checkbox' && type!='radio' && element.addClass(this['errorinput']));
	if (!errmsg) return false;
	if(msgid.length>0) { msgid.removeClass(this['validTip']).addClass(this['errorTip']).html(errmsg); } else { element.parent('*').find('.'+this['errorTip']).remove(); jQuery("<span class='"+this['errorTip']+"'></span>").html(errmsg).insertAfter(element); }
	return false ;
};

validator.removeErr =  function(element){
	element.removeClass(this['errorInput']);
	var msgid = jQuery('#'+element.attr('msgid'));
	if (msgid.length==0) {
		element.parent('*').find('span .'+this['errorTip']).remove();
		element.parent('*').find('span .'+this['validTip']).remove();
	}
};

validator.checkajax = function(element, datatype, errindex) {
	var value=jQuery.trim(element.val());
	this['element'] = element;
	this['errindex'] = errindex;
	validator.removeErr(element);
	return eval(this[datatype]);
};

validator.checkDatatype = function(element,datatype){
	var value=jQuery.trim(element.val());
	this['element'] = element;
	validator.removeErr(element);
	switch(datatype){
		case "idcard" :
		case "date" :
		case "repeat" :
		case "range" :
		case "compare" :
		case "custom" :
		case "group" :
		case "limit" :
		case "limitb" :
		case "limitc" :
		case "safestring" :
		case "filter" :
		return eval(this[datatype]);
		break;

		default:
			return this[datatype].test(value);
			break;
		}
};

validator.check=function(obj, submit){
	var datatype = obj.attr('datatype');
	var lastvalue = obj.attr('lastvalue');
	var value = jQuery.trim(obj.val());
	
	if(typeof(datatype) == "undefined") return true;
	if(obj.attr('require')!="true" && value=="") {
		obj.removeClass(validator.errorInput);
		return true;
	}
	var isValid = true;
	var datatypes = datatype.split('|');
	/* add for not check ajax every blur */
	if( ($.inArray('repeat',datatypes) == -1) 
			&& ($.inArray('ajax',datatypes) != -1)
			&& (submit==true || (lastvalue && lastvalue==value)) ) {
		var e = obj.parent('*').find('.'+validator.errorTip);
		var v = obj.parent('*').find('.'+validator.validTip);
		if ( e.length>0 || v.length>0 ) {
			return (v.length > 0);
		}
	}

	jQuery.each(datatypes,function(index,type){
		if(typeof(validator[type]) == "undefined") {
			isValid = false;
			return  false;
		}
		//ajax validate 
		if(type=='ajax')
			return isValid = validator.checkajax(obj, type, index);

		if(validator.checkDatatype(obj,type)==false){ 
			obj.addClass(validator.errorInput);
			validator.showErr(obj, index);
			return isValid=false;
		} else { // validate success
			validator.showErr(obj, index);
       		obj.removeClass(validator.errorInput);
       		var msgid = jQuery('#'+obj.attr('msgid'));
			if(msgid.length>0) { msgid.removeClass(validator.errorTip).addClass(validator.validTip).html(''); } else { obj.parent('*').find('.'+validator.errorTip+',.'+validator.validTip).remove(); }				
		}
	});
	obj.attr('lastvalue', value);
	return isValid;
};
  
jQuery.fn.validateForm = function(no, form) {
	var isValid = true;
	var errIndex= new Array();
	var n=0;
	var emsg = '';
	var elements = jQuery(form).find(':input[require]');
	elements.each(function(i){
		if ( false==validator.check(jQuery(this),true) ) {
			var m = jQuery(this).parent('*').find('.'+validator.errorTip).html();
			if (m) { emsg += (emsg=='') ? m : '\n'+m; }
			isValid  = false;
			errIndex[n++]=i;
		};
	});

	if(isValid==false){
		elements.eq(errIndex[0]).focus().select();
		return false;
	}
	return true;
};

jQuery.fn.checkForm = function(jform){
	var jform=jQuery(jform);
	var elements = jform.find(':input[require]');
	elements.blur(function(){
		return validator.check(jQuery(this));
	});
	jform.submit(function(){ return jQuery.fn.validateForm('',jform);});
};
