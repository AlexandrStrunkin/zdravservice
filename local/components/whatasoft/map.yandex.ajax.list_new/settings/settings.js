function OnWASYMapBDSettingsEdit(arParams){
	if(null != window.jsWASYMapBDOpener){
		try {window.jsWASYMapBDOpener.Close();}catch (e) {}
		window.jsWASYMapBDOpener = null;
	}

	window.jsWASYMapBDOpener = new JWASYMapBDOpener(arParams);
}

function JWASYMapBDOpener(arParams){
	this.arParams = arParams;
	this.jsOptions = this.arParams.data.split('||');

  var obButton = document.createElement('INPUT');
  obButton.type = "button";
  obButton.value = this.jsOptions[1];
  this.arParams.oCont.appendChild(obButton);

	obButton.onclick = BX.delegate(this.btnClick, this);
	this.saveData = BX.delegate(this.__saveData, this);
}

JWASYMapBDOpener.prototype.Close = function(e){
	if(false !== e){
		BX.util.PreventDefault(e);
	}

	if(null != window.jsPopup_was_yandex_map_bd){
		window.jsPopup_was_yandex_map_bd.Close();
	}
}

JWASYMapBDOpener.prototype.btnClick = function(){
	this.arElements = this.arParams.getElements();

	if(!this.arElements){
		return false;
	}

	var strUrl = '/bitrix/components/whatasoft/map.yandex.ajax.list/settings/settings.php'
    + '?lang=' + this.jsOptions[0]
    + '&MAP_SCALE=' + BX.util.urlencode(this.arElements.MAP_SCALE.value),
  strUrlPost = 'MAP_DATA=' + BX.util.urlencode(this.arParams.oInput.value);
  
	if(null == window.jsPopup_was_yandex_map_bd){
    window.jsPopup_was_yandex_map_bd = new BX.CDialog({
      'content_url': strUrl,
      'content_post': strUrlPost,
      'width':800, 'height':500,
      'resizable':false
    });
	}

	window.jsPopup_was_yandex_map_bd.Show();
	window.jsPopup_was_yandex_map_bd.PARAMS.content_url = '';

	return false;
}

JWASYMapBDOpener.prototype.__saveData = function(strData, scale){
	this.arParams.oInput.value = strData;
	if(null != this.arParams.oInput.onchange){
		this.arParams.oInput.onchange();
	}

	if(scale && this.arElements.MAP_SCALE){
		this.arElements.MAP_SCALE.value = scale;
		if(null != this.arElements.MAP_SCALE.onchange){
			this.arElements.MAP_SCALE.onchange();
		}
	}

	this.Close(false);
}