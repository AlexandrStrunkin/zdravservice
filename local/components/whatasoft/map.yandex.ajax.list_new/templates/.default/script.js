(function(){
  function waitScriptLoad(){
    if(!window.whatasoftMaps){
      setTimeout(waitScriptLoad, 50);
    }else{
      whatasoftMaps.ready(function(){
        $(".was-map-yandex-ajax").each(function(i, el){
          wmap = new WASAjaxYandexMapList();
          wmap.Init($(el));
        });
      });
    }
  }

  jQuery(function(){
    $(document).ready(function(){
      waitScriptLoad();
    });
  });
}());


function WASAjaxYandexMapList(){
  this.def_options = {
      center: [0, 0],
      zoom: 14,
      auto_scale_center: 0,
      type: 'yandex#map',
      controls: ['default'],
      behaviors: ['scrollZoom','dblClickZoom','drag'],
      balloon_icon_content: 0,
      balloon_color: "9b7fc4",
      balloon_icon: "",
      balloon_icon_size: [20, 20],
      balloon_icon_offset: [0, 0],
      balloon_icon_content_offset: [0, 0],
      balloon_icon_content_size: [10, 10],
      cluster: 0,
      cluster_simple: 0,
      cluster_content_width: 350,
      cluster_content_left_column_width: 120
  };
  this.options = {};
  this.map_options = {};
  this.map_block = null;
  this.map = null;
  this.objectManager = null;
}

WASAjaxYandexMapList.prototype.BuildOptions = function(){
  this.options = $.extend({}, this.def_options, {
      ajax_cache_id: this.map_block.data('ajax_cache_id'),
      ajax_session_id: BX.bitrix_sessid,
      center: this.map_block.data('center'),
      zoom: this.map_block.data('zoom'),
      auto_scale_center: this.map_block.data('auto_scale_center'),
      type: "yandex#"+ this.map_block.data('type'),
      controls: this.map_block.data('controls') && this.map_block.data('controls').split(','),
      behaviors: this.map_block.data('behaviors') && this.map_block.data('behaviors').split(','),
      balloon_icon_content: this.map_block.data('balloon_icon_content'),
      balloon_color: String(this.map_block.data('balloon_color')),
      balloon_icon: this.map_block.data('balloon_icon'),
      balloon_icon_size: this.map_block.data('balloon_icon_size'),
      balloon_icon_offset: this.map_block.data('balloon_icon_offset'),
      balloon_icon_content_offset: this.map_block.data('balloon_icon_content_offset'),
      balloon_icon_content_size: this.map_block.data('balloon_icon_content_size'),
      cluster: this.map_block.data('cluster'),
      cluster_simple: this.map_block.data('cluster_simple'),
      cluster_content_width: this.map_block.data('cluster_content_width'),
      cluster_content_left_column_width: this.map_block.data('cluster_content_left_column_width')
  });

  this.map_options = {
      controls: this.options.controls,
      type: this.options.type,
      center: this.options.center,
      zoom: this.options.zoom
  };
}

WASAjaxYandexMapList.prototype.InitControls = function(){
  this.map.controls.remove('geolocationControl');
  this.map.controls.remove('searchControl');
  this.map.controls.remove('routeEditor');
  this.map.controls.remove('trafficControl');
  this.map.controls.remove('typeSelector');
  this.map.controls.remove('fullscreenControl');
  this.map.controls.remove('zoomControl');
  this.map.controls.remove('rulerControl');

  var map = this.map;
  $.each(this.options.controls, function(key, value){
    if(value != 'default'){
      map.controls.add(value);
    }
  });
}

WASAjaxYandexMapList.prototype.InitBehaviors = function(){
  this.map.behaviors.disable('scrollZoom');
  this.map.behaviors.disable('dblClickZoom');
  this.map.behaviors.disable('rightMouseButtonMagnifier');
  this.map.behaviors.disable('drag');

  var map = this.map;
  $.each(this.options.behaviors, function(key, value){
    map.behaviors.enable(value);
  });
}

WASAjaxYandexMapList.prototype.AddObjectManager = function(){
  var CustomItemContentLayout = whatasoftMaps.templateLayoutFactory.createClass(
      '<div class=was-map-balloon-header>{{ properties.balloonContentHeader|raw }}</div>' +
      '<div class=was-map-balloon-body>{{ properties.balloonContentBody|raw }}</div>' +
      '<div class=was-map-balloon-footer>{{ properties.balloonContentFooter|raw }}</div>'
  );
  var CustomIconContentLayout = whatasoftMaps.templateLayoutFactory.createClass(
      '<div class="was-icon-content $[properties.iconContentClass]">$[properties.iconContent]</div>'
  );
  if(this.options.cluster){
      if(this.options.cluster_simple){
          this.objectManager = new whatasoftMaps.ObjectManager({
            clusterize: true,
            clusterIconColor: "#"+ this.options.balloon_color
          });
      }else{
          this.objectManager = new whatasoftMaps.ObjectManager({
            clusterize: true,
            clusterDisableClickZoom: true,
            clusterOpenBalloonOnClick: true,
            clusterBalloonPanelMaxMapArea: 0,
            clusterBalloonItemContentLayout: CustomItemContentLayout,
            clusterBalloonContentLayoutWidth: this.options.cluster_content_width,
            clusterBalloonLeftColumnWidth: this.options.cluster_content_left_column_width,
            clusterIconColor: "#"+ this.options.balloon_color
          });
          //this.objectManager.objects.options.set('balloonMaxWidth', this.options.cluster_content_width);
      }
  }else{
      this.objectManager = new whatasoftMaps.ObjectManager();
  }

  if(this.options.balloon_color.length){ //for icon color from map
      this.objectManager.objects.options.set({
        iconColor: "#"+ this.options.balloon_color
      });
  }
  if(this.options.balloon_icon.length){ //for custom icon from map
      balloon_icon_size = $.map(this.options.balloon_icon_size.split(','), function(value){
          return parseInt(value, 10);
      });
      balloon_icon_offset = $.map(this.options.balloon_icon_offset.split(','), function(value){
          return parseInt(value, 10);
      });
      balloon_icon_content_offset = $.map(this.options.balloon_icon_content_offset.split(','), function(value){
          return parseInt(value, 10);
      });
      balloon_icon_content_size = $.map(this.options.balloon_icon_content_size.split(','), function(value){
          return parseInt(value, 10);
      });
      this.objectManager.objects.options.set({
        iconLayout: 'default#image',
        iconImageHref: this.options.balloon_icon,
        iconImageSize: balloon_icon_size,
        iconImageOffset: balloon_icon_offset
      });
      if(this.options.balloon_icon_content){
        this.objectManager.objects.options.set({
          iconLayout: 'default#imageWithContent',
          iconContentSize: balloon_icon_content_size,
          iconContentOffset: balloon_icon_content_offset,
          iconContentLayout: CustomIconContentLayout
        });
      }
  }else{
      this.objectManager.objects.options.set({
          preset: 'islands#blueDotIcon'
      });
  }

  this.map.geoObjects.add(this.objectManager);
}

WASAjaxYandexMapList.prototype.RequestData = function(_cache_id, _session_id, _page, _on_data_func){
  var request = $.ajax({
    url: "/local/components/whatasoft/map.yandex.ajax.list_new/ajax.php",
    type: "POST",
    data: {cache_id: _cache_id, session_id: _session_id, page: _page},
    dataType: "json",
    context: this
  });

  request.done(function(data){
    _on_data_func.call(this, data);
    if(data.status == "ok"){
      if(!data.last){
        this.RequestData(_cache_id, _session_id, data.page, _on_data_func);
      }
    }
  });

  request.fail(function(jqXHR, textStatus){
    console.log("Request failed: " + textStatus);
  });
}

WASAjaxYandexMapList.prototype.OnDataReceive = function(data){
  if(data.status == "ok"){
      this.objectManager.add(data.data);
      if(data.last && this.options.auto_scale_center){
        this.map.setBounds(this.map.geoObjects.getBounds(), {checkZoomRange: true, duration: 0});
      }
  }else{
      //errors
  }
}

WASAjaxYandexMapList.prototype.Init = function(map_block){
  if(typeof map_block.data("ymap") !== 'undefined'){
    return;
  }

  this.map_block = map_block;
  this.BuildOptions();
  this.map = new whatasoftMaps.Map(this.map_block.get(0), this.map_options);
  this.map_block.data("ymap", this.map);
  this.InitBehaviors();
  this.InitControls();
  this.AddObjectManager();
  this.RequestData(this.options.ajax_cache_id, this.options.ajax_session_id, 1, this.OnDataReceive);
}
