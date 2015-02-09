if (typeof(_OL) == 'undefined') {
	var _OL = {};
}
_OL.Util = new(function() {
	this.emptyFn = function() {};
	this.isFunction = function(wh) {
		if (!wh) {
			return false;
		}
		return (wh instanceof Function || typeof wh == "function");
	};
	this.goToHash = function(hash) {
		if (hash) {
			var hashTop = ($e('a[name=' + hash.replace(new RegExp('^#'), '') + ']').getXY() || [0, 0])[1];
			var top = Math.min(Math.max(0, hashTop), QZFL.dom.getScrollHeight());
			QZFL.dom.setScrollTop(top);
		}
	};
	this.htmlspecialchars = function(str) {
		str = str.replace(/&/g, '&amp;');
		str = str.replace(/</g, '&lt;');
		str = str.replace(/>/g, '&gt;');
		str = str.replace(/"/g, '&quot;');
		str = str.replace(/'/g, '&#039;');
		return str;
	};
})();
_OL.Util.Enum = function(obj) {
	this.length = 0;
	this._ = {};
	for (p in obj) {
		if (typeof obj[p] != 'function') {
			this._[p] = obj[p];
			this.length++;
		}
	}
	this.set = function(k, v, ncvr) {
		if ('function' === typeof v) return;
		if (this._[k] === undefined) {
			this.length++;
			this._[k] = v;
		} else {
			if (!ncvr) {
				this._[k] = v;
			}
		}
	};
	this.get = function(k) {
		return this._[k];
	};
	this.remove = function(k) {
		if ('function' === typeof this._[k]) return false;
		var r = this._[k];
		this._[k] = undefined;
		delete this._[k];
		this.length--;
		return r;
	};
	this.each = function(f) {
		for (p in this._) {
			f(p, this._[p]);
		}
	};
	this.filter = function(f) {
		for (p in this._) {
			var r = f(p, this._[p]);
			if ('function' !== typeof r) {
				if (undefined === r) {
					this._[p] = undefined;
					delete this._[p];
					this.length--;
				} else {
					this._[p] = r;
				}
			}
		}
	};
	this.clean = function() {
		this._ = {};
		this.length = 0;
	};
};
	var Haunt = {
		_type_: 'Haunt',
		savedItem: null,
		selectedItem: null,
		allItem: null,
		areas: null,
		elmMyHaunt: null,
		elmEditHaunt: null,
		elmSettingPanel: null,
		elmClose: null,
		elmSave: null,
		elmCancel: null,
		elmSelectedItemBox: null,
		elmAreaList: null,
		elmForSelectItemBox: null,
		init: function() {
			Haunt.savedItem = new _OL.Util.Enum();
			Haunt.selectedItem = new _OL.Util.Enum();
			Haunt.allItem = new _OL.Util.Enum();
			Haunt.areas = new _OL.Util.Enum();
			Haunt.elmHauntFilter = $('#haunt_filter');
			Haunt.elmMyHaunt = $('#sethauntbtn');
			Haunt.elmEditHaunt = $('#editFavArea');
			Haunt.elmSettingPanel = $('#haunt_sethauntpop');
			Haunt.elmClose = $('#haunt_closehauntpopbtn');
			Haunt.elmSave = $('#haunt_confirm');
			Haunt.elmCancel = $('#haunt_cancel');
			Haunt.elmSelectedItemBox = $('#haunt_haunts');
			Haunt.elmAreaList = $('#haunt_areas');
			Haunt.elmForSelectItemBox = $('#haunt_areasBusiness');
			if (!Haunt.elmHauntFilter || !Haunt.elmMyHaunt) {
				return;
			}
			var p;
			for (p in areasData) {
				Haunt.areas.set(p, areasData[p]['name']);
				Haunt.allItem.set(p, new _OL.Util.Enum());
				var pp;
				for (pp in areasData[p]['sub_district']) {
					Haunt.allItem._[p].set(pp, areasData[p]['sub_district'][pp]);
				}
			}
			areasData = null;
			Haunt.elmClose.click(function(){Haunt.hideSetting();});
			Haunt.elmSave.click(function(){Haunt.onSave();});
			Haunt.elmCancel.click(function(){Haunt.hideSetting();});
			Haunt.areas.each(function(k, v) {
				//Haunt.elmAreaList.add(new Option(v, k),undefined);
				Haunt.elmAreaList.append('<option value="'+k+'">'+v+'</option>');
			});
			Haunt.elmAreaList.change(function(){Haunt.showForSelectItem();});
			Haunt.elmMyHaunt.click(function() {
				$.ajax({
					url:"index.php?m=Goods&a=getareajson&r=" + (new Date()).getTime(),
					type:"GET",
					dataType:"html",
					cache : false,
					success : function(data){
						try {
							eval(data);
						} catch (e) {
							return;
						}
						if (RET_COMMON['retCode'] != 0) {
							Haunt.showSetting();
							Haunt.elmMyHaunt.click(function(){Haunt.showSetting();});
						} else {
							location.href = Haunt.elmMyHaunt.attr('_href');
						}
					}
				});
			});
			if (Haunt.elmEditHaunt) {
				Haunt.elmEditHaunt.click(function() {
				$.ajax({
					url:"index.php?m=Goods&a=getareajson&r=" + (new Date()).getTime(),
					type:"GET",
					dataType:"html",
					cache : false,
					success : function(data){
						try {
							eval(data);
						} catch (e) {
							return;
						}
						if (RET_COMMON['retCode'] != 0) {
							Haunt.elmMyHaunt.click(function(){Haunt.showSetting();});
						} else {
							var p;
							for (p in RET_COMMON['retData']) {
								Haunt.savedItem.set(p, RET_COMMON['retData'][p]);
							}
							Haunt.elmMyHaunt.click = null;
							Haunt.elmMyHaunt.href = Haunt.elmMyHaunt.attr('_href');
						}
						Haunt.showSetting();
						Haunt.elmEditHaunt.click(function(){Haunt.showSetting();});
					}
				});
				});
			}
		},
		hideSetting: function() {
			$("#haunt_sethauntpop").hide();
			$("#Mask").hide();
            $('html').removeClass('hash');
		},
		showSetting: function() {
			Haunt.selectedItem = new _OL.Util.Enum();
			Haunt.savedItem.each(function(k, v) {
				Haunt.selectedItem.set(k, v);
			});
			Haunt.showSelectedItem();
			Haunt.showForSelectItem();
			$("#Mask").show();
			$("#haunt_sethauntpop").show();
            $('html, body').animate({scrollTop:0}, 'slow');
            $('html').addClass('hash');
            $('.oftengo').show();
		},
		showSelectedItem: function() {
			Haunt.elmSelectedItemBox.html('');
			var frag = document.createDocumentFragment();
			Haunt.selectedItem.each(function(k, v) {
				var li = document.createElement('li');
				// li.className = 'selected';
				// li.innerHTML = '<a href="javascript:;">' + v['name'] + '</a>';
				var span = document.createElement('a');
				// span.style.display = 'none';
				span.innerHTML = v['name'];
                span.className = 'active';
				// li.onmouseover = function() {
					// span.style.display = 'inline'
				// };
				// li.onmouseout = function() {
					// span.style.display = 'none'
				// };
				span.onclick = function() {
					Haunt.selectedItem.remove(k);
					Haunt.showSelectedItem();
					Haunt.showForSelectItem();
				};
				li.appendChild(span);
				frag.appendChild(li);
			});
			Haunt.elmSelectedItemBox.append(frag);
			frag = null;
		},
		showForSelectItem: function() {
			Haunt.elmForSelectItemBox.html('');
			var frag = document.createDocumentFragment();
			if (Haunt.allItem.get(Haunt.elmAreaList.val())) {
				Haunt.allItem.get(Haunt.elmAreaList.val()).each(function(k, v) {
					if (v['name'] == '其他商圈') {
						return;
					}
					var li = document.createElement('li');
					li.innerHTML = '<a href="javascript:;">' + v['name'] + '</a>';
					if (Haunt.selectedItem.get(k)) {
						li.className = 'disabled';
					} else {
						li.onclick = function() {
							if (Haunt.selectedItem.length > 5) {
								return;
							}
							li.className = 'disabled';
							Haunt.selectedItem.set(k, {
								'name': v['name'],
								'areaId': v['areaId']
							});
							Haunt.showSelectedItem();
						};
					}
					frag.appendChild(li);
				});
			}
			Haunt.elmForSelectItemBox.append(frag);
			frag = null;
		},
		onSave: function() {
			var dataJson = '';
			dataJson += '{';
			Haunt.selectedItem.each(function(k, v) {
				dataJson += '"' + k + '":{"name":"' + v['name'] + '","areaId":' + v['areaId'] + '},';
			});
			dataJson += '}';
			dataJson = dataJson.replace(/,}/g, '}');
			$.ajax({
				url:"index.php?m=Goods&a=getareajson&r=" + (new Date()).getTime() + "&area_list=" + encodeURIComponent(dataJson),
				type:"POST",
				dataType:"html",
				cache : false,
				success : function(data){
					try {
						eval(data);
					} catch (e) {
						return;
					}
					if (RET_COMMON['retCode'] != 0) {} else {
						location.href = Haunt.elmMyHaunt.attr('_href');
					}
				}
			});
			Haunt.hideSetting();
		}
	};
