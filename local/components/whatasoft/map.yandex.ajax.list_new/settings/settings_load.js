(function($){
  var jsWASYMapBD = {
    map: null,
    arData: {},
    obForm: null,

    bPositionFixed: true,

    onInitCompleted: null,
    bInitCompleted: false,

    init: function(map){
      if(null != map){
        jsWASYMapBD.map = map; //GLOBAL_arMapObjects['was_ymap_bd_list'];
      }

      jsWASYMapBD.obForm = $("form[name=was_popup_form_ymap_bd]");
      jsWASYMapBD.obForm.on("submit", jsWASYMapBD.__saveChanges);

      jsWASYMapBD.map.events.add("boundschange", jsWASYMapBD.__getPositionValues);
      jsWASYMapBD.map.events.add("sizechange", jsWASYMapBD.__getPositionValues);

      if(!jsWASYMapBD.arData.yandex_lat || !jsWASYMapBD.arData.yandex_lon || !jsWASYMapBD.arData.yandex_scale){
        var obPos = jsWASYMapBD.map.getCenter();
        jsWASYMapBD.arData.yandex_lat = obPos[0];
        jsWASYMapBD.arData.yandex_lon = obPos[1];
        jsWASYMapBD.arData.yandex_scale = jsWASYMapBD.map.getZoom();
        jsWASYMapBD.bPositionFixed = false;
      }else{
        jsWASYMapBD.bPositionFixed = true;
      }

      jsWASYMapBD.setControlValue('yandex_lat', jsWASYMapBD.arData.yandex_lat);
      jsWASYMapBD.setControlValue('yandex_lon', jsWASYMapBD.arData.yandex_lon);
      jsWASYMapBD.setControlValue('yandex_scale', jsWASYMapBD.arData.yandex_scale);

      $('.was_restore_position', jsWASYMapBD.obForm).on("click", jsWASYMapBD.restorePositionValues);
      $('.was_yandex_position_fix', jsWASYMapBD.obForm).on("click", function(){jsWASYMapBD.setFixedFlag(this.checked)});

      jsWASYMapBD.setFixedFlag(true);

      jsWASYMapBD.bInitCompleted = true;
      jsWASYMapBD.checkInitCompleted();
    },

    checkInitCompleted: function(){
      if(jsWASYMapBD.bInitCompleted){
        if(jsWASYMapBD.onInitCompleted){
          jsWASYMapBD.onInitCompleted();
        }
        return true;
      }
      
      return false;
    },

    __getPositionValues: function(){
      if(jsWASYMapBD.bPositionFixed){
        return;
      }
      
      var obPos = jsWASYMapBD.map.getCenter();
      jsWASYMapBD.arData.yandex_lat = obPos[0];
      jsWASYMapBD.arData.yandex_lon = obPos[1];
      jsWASYMapBD.arData.yandex_scale = jsWASYMapBD.map.getZoom();
      
      jsWASYMapBD.setControlValue('yandex_lat', jsWASYMapBD.arData.yandex_lat);
      jsWASYMapBD.setControlValue('yandex_lon', jsWASYMapBD.arData.yandex_lon);
      jsWASYMapBD.setControlValue('yandex_scale', jsWASYMapBD.arData.yandex_scale);
    },
    
    restorePositionValues: function(e){
      jsWASYMapBD.map.setZoom(+jsWASYMapBD.arData.yandex_scale);
      jsWASYMapBD.map.panTo([+jsWASYMapBD.arData.yandex_lat, +jsWASYMapBD.arData.yandex_lon]);
      
      return false;
    },

    setFixedFlag: function(value){
      jsWASYMapBD.bPositionFixed = value;
      if(!value){
        jsWASYMapBD.__getPositionValues();
      }
    },
    
    setControlValue: function(control, value){
      var obControl = $('input[name=was_'+ control +']', jsWASYMapBD.obForm);
      if(obControl.length){
        obControl.val(value);
      }
      
      var obControlOut = $('.was_'+ control +'_value', jsWASYMapBD.obForm);
      if(obControlOut.length){
        obControlOut.html(value);
      }
    },

    __saveChanges: function(){
      if(!jsWASYMapBD.map){
        return false;
      }
      
      var centerCoords = jsWASYMapBD.arData.yandex_lat +","+ jsWASYMapBD.arData.yandex_lon;
      window.jsWASYMapBDOpener.saveData(centerCoords, jsWASYMapBD.arData.yandex_scale);
      
      return false;
    },
    
    clear: function(){
      jsWASYMapBD.bInitCompleted = false;
      jsWASYMapBD.map = null;
    }
  }
  window.jsWASYMapBD = jsWASYMapBD;

  var jsWASYMapBDSearch = {
    bInited: false,

    map: null,
    obInput: null,
    timerID: null,
    timerDelay: 1000,

    arSearchResults: [],

    obOut: null,

    __init: function(input){
      if (jsWASYMapBDSearch.bInited) return;

      jsWASYMapBDSearch.map = jsWASYMapBD.map;
      jsWASYMapBDSearch.obInput = input;

      input.form.onsubmit = function() {jsWASYMapBDSearch.doSearch(); return false;}

      input.onfocus = jsWASYMapBDSearch.showResults;
      input.onblur = jsWASYMapBDSearch.hideResults;

      jsWASYMapBDSearch.bInited = true;
    },

    setTypingStarted: function(input){
      if(!jsWASYMapBDSearch.bInited){
        jsWASYMapBDSearch.__init(input);
      }

      jsWASYMapBDSearch.hideResults();

      if(null != jsWASYMapBDSearch.timerID){
        clearTimeout(jsWASYMapBDSearch.timerID);
      }

      jsWASYMapBDSearch.timerID = setTimeout(jsWASYMapBDSearch.doSearch, jsWASYMapBDSearch.timerDelay);
    },

    doSearch: function(){
      var value = BX.util.trim(jsWASYMapBDSearch.obInput.value);
      if(value.length > 1){
        ymaps.geocode(value).then(
          jsWASYMapBDSearch.__searchResultsLoad,
          jsWASYMapBDSearch.handleError
        );
      }
    },

    handleError: function(error){
      alert(this.jsMess.mess_error + ': ' + error.message);
    },

    __generateOutput: function(){
      jsWASYMapBDSearch.obOut = document.body.appendChild(document.createElement('UL'));
      jsWASYMapBDSearch.obOut.className = 'bx-yandex-address-search-results';
      jsWASYMapBDSearch.obOut.style.display = 'none';
    },

    __searchResultsLoad: function(res){
      if(null == jsWASYMapBDSearch.obOut){
        jsWASYMapBDSearch.__generateOutput();
      }

      jsWASYMapBDSearch.obOut.innerHTML = '';
      jsWASYMapBDSearch.clearSearchResults();

      var len = res.geoObjects.getLength();
      if(len > 0){
        for(var i = 0; i < len; i++){
          jsWASYMapBDSearch.arSearchResults[i] = res.geoObjects.get(i);

          var obListElement = document.createElement('LI');

          if (i == 0)
            obListElement.className = 'bx-yandex-first';

          var obLink = document.createElement('A');
          obLink.href = "javascript:void(0)";
          var obText = obLink.appendChild(document.createElement('SPAN'));

          obText.appendChild(document.createTextNode(
            jsWASYMapBDSearch.arSearchResults[i].properties.get('metaDataProperty').GeocoderMetaData.text
          ));

          obLink.BXSearchIndex = i;
          obLink.onclick = jsWASYMapBDSearch.__showSearchResult;

          obListElement.appendChild(obLink);
          jsWASYMapBDSearch.obOut.appendChild(obListElement);
        }
      }else{
        jsWASYMapBDSearch.obOut.innerHTML = '<li class="bx-yandex-notfound">' + window.jsYandexMess.nothing_found + '</li>';
      }

      jsWASYMapBDSearch.showResults();
    },

    __showSearchResult: function(e){
      if(null !== this.BXSearchIndex){
        jsWASYMapBDSearch.map.panTo(jsWASYMapBDSearch.arSearchResults[this.BXSearchIndex].geometry.getCoordinates());
      }

      return BX.PreventDefault(e);
    },

    showResults: function(){
      var obPos = BX.pos(jsWASYMapBDSearch.obInput);
      jsWASYMapBDSearch.obOut.style.top = (obPos.bottom + 2) + 'px';
      jsWASYMapBDSearch.obOut.style.left = obPos.left + 'px';
      jsWASYMapBDSearch.obOut.style.zIndex = parseInt(BX.WindowManager.Get().zIndex) + 200;

      if(BX.findParent(jsWASYMapBDSearch.obInput, {"tag" : "div", "className" : "bx-core-window bx-core-adm-dialog"}).style.display == 'block'){
          if (null != jsWASYMapBDSearch.obOut)
              jsWASYMapBDSearch.obOut.style.display = 'block';
      }
    },

    hideResults: function(){
      if(null != jsWASYMapBDSearch.obOut){
        setTimeout("jsWASYMapBDSearch.obOut.style.display = 'none'", 300);
      }
    },

    clearSearchResults: function(){
      for(var i = 0; i < jsWASYMapBDSearch.arSearchResults.length; i++){
        delete jsWASYMapBDSearch.arSearchResults[i];
      }
      
      jsWASYMapBDSearch.arSearchResults = [];
    },

    clear: function(){
      if (!jsWASYMapBDSearch.bInited)
        return;

      jsWASYMapBDSearch.bInited = false;
      if(null != jsWASYMapBDSearch.obOut){
        jsWASYMapBDSearch.obOut.parentNode.removeChild(jsWASYMapBDSearch.obOut);
        jsWASYMapBDSearch.obOut = null;
      }

      jsWASYMapBDSearch.arSearchResults = [];
      jsWASYMapBDSearch.map = null;
      jsWASYMapBDSearch.obInput = null;
      jsWASYMapBDSearch.timerID = null;
    }
  }
  window.jsWASYMapBDSearch = jsWASYMapBDSearch;
})(jQuery);