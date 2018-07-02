<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
$APPLICATION->AddHeadScript("https://api-maps.yandex.ru/2.1/?load=package.full&mode=release&lang=ru-RU&wizard=bitrix&ns=whatasoftMaps");

?>
<?if($arResult["ID"]):?>
	<?if($arParams["USE_REVIEW"] == "Y" && IsModuleInstalled("forum")):?>
		<div id="reviews_content">
			<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("area");?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:forum.topic.reviews",
					"main",
					Array(
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
						"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
						"FORUM_ID" => $arParams["FORUM_ID"],
						"ELEMENT_ID" => $arResult["ID"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
						"SHOW_RATING" => "N",
						"SHOW_MINIMIZED" => "Y",
						"SECTION_REVIEW" => "Y",
						"POST_FIRST_MESSAGE" => "Y",
						"MINIMIZED_MINIMIZE_TEXT" => GetMessage("HIDE_FORM"),
						"MINIMIZED_EXPAND_TEXT" => GetMessage("ADD_REVIEW"),
						"SHOW_AVATAR" => "N",
						"SHOW_LINK_TO_FORUM" => "N",
						"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
					),	false
				);?>
			<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("area", "");?>
		</div>
	<?endif;?>
	<?if(($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))):?>
		<div id="ask_block_content" class="hidden">
			<?$APPLICATION->IncludeComponent(
				"bitrix:form.result.new",
				"inline",
				Array(
					"WEB_FORM_ID" => $arParams["ASK_FORM_ID"],
					"IGNORE_CUSTOM_TEMPLATE" => "N",
					"USE_EXTENDED_ERRORS" => "N",
					"SEF_MODE" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "3600000",
					"LIST_URL" => "",
					"EDIT_URL" => "",
					"SUCCESS_URL" => "?send=ok",
					"CHAIN_ITEM_TEXT" => "",
					"CHAIN_ITEM_LINK" => "",
					"VARIABLE_ALIASES" => Array("WEB_FORM_ID" => "WEB_FORM_ID", "RESULT_ID" => "RESULT_ID"),
					"AJAX_MODE" => "Y",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"SHOW_LICENCE" => CNext::GetFrontParametrValue('SHOW_LICENCE'),
				)
			);?>
		</div>
	<?endif;?>
	<script type="text/javascript">
		if($("#ask_block_content").length && $("#ask_block").length){
			$("#ask_block_content").appendTo($("#ask_block"));
			$("#ask_block_content").removeClass("hidden");
		}
		if($(".gifts").length && $("#reviews_content").length){
			$(".gifts").insertAfter($("#reviews_content"));
		}
		if($("#reviews_content").length && (!$(".tabs .tab-content .active").length) || $('.product_reviews_tab.active').length){
			$(".shadow.common").hide();
			$("#reviews_content").show();
		}
		if(!$(".stores_tab").length){
			$('.item-stock .store_view').removeClass('store_view');
		}
		viewItemCounter('<?=$arResult["ID"];?>','<?=current($arParams["PRICE_CODE"]);?>');
	</script>
<?endif;?>
<?if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])){
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency){?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
		</script>
	<?}
}?>
<script type="text/javascript">
	var viewedCounter = {
		path: '/bitrix/components/bitrix/catalog.element/ajax.php',
		params: {
			AJAX: 'Y',
			SITE_ID: "<?= SITE_ID ?>",
			PRODUCT_ID: "<?= $arResult['ID'] ?>",
			PARENT_ID: "<?= $arResult['ID'] ?>"
		}
	};
	BX.ready(
		BX.defer(function(){
			$('body').addClass('detail_page');
			<?//if(!isset($templateData['JS_OBJ'])){?>
				BX.ajax.post(
					viewedCounter.path,
					viewedCounter.params
				);
			<?//}?>
			if( $('.stores_tab').length ){
				var objUrl = parseUrlQuery(),
				add_url = '';
				if('clear_cache' in objUrl)
				{
					if(objUrl.clear_cache == 'Y')
						add_url = '?clear_cache=Y';
				}
				$.ajax({
					type:"POST",
					url:arNextOptions['SITE_DIR']+"ajax/productStoreAmount.php"+add_url,
					data:<?=CUtil::PhpToJSObject($templateData["STORES"], false, true, true)?>,
					success: function(data){
						var arSearch=parseUrlQuery();
						$('.tab-content .tab-pane .stores_wrapp').html(data);
						if("oid" in arSearch)
							$('.stores_tab .sku_stores_'+arSearch.oid).show();
						else
							$('.stores_tab .stores_wrapp > div:first').show();

					}
				});
			}
		})

	);
</script>
<?if($_REQUEST && isset($_REQUEST['formresult'])):?>
	<script>
	$(document).ready(function() {
		if($('#ask_block .form_result').length){
			$('.product_ask_tab').trigger('click');
		}
	});
	</script>
<?endif;?>
<?if(isset($_GET["RID"])){?>
	<?if($_GET["RID"]){?>
		<script>
			$(document).ready(function() {
				$("<div class='rid_item' data-rid='<?=htmlspecialcharsbx($_GET["RID"]);?>'></div>").appendTo($('body'));
			});
		</script>
	<?}?>
<?}?>

<?

global $arrMapFilter;

$arrMapFilter = ["ID" => $arParams['STORES']];

?>



<style media="screen">

.modal_popup_window.popup{
	box-sizing: border-box;
	display: none;
	position: fixed;
	top: 17%;
	left: 10%;
	width: 80%;
	min-width: 80%;
    max-width: 80%;
	height: 50%;
	background-color: #fff;
	color: #333;
	border-radius: 0;
    -moz-radius: 0;
    -webkit-border-radius: 0;
	padding: 0;
	z-index: 4002;
}

.modal_popup_window .popup_head h2{
	margin: 0;
}
.modal_popup_window .popup_head{
	position: relative;
	box-sizing: border-box;
	height: 20%;
	border-bottom: 2px solid #008c61;
	padding: 34px 75px 35px 35px;
	background-color: #fff;
}

.modal_popup_window.overlay  {
	display: none;
	position:fixed;
	top: 0;
	left: 0;
	/* overflow: hidden; */
	min-width: 100%;
    max-width: 100%;
	width: 100%;
	height: 100%;
	background: rgba(0,0,0,.5);
	z-index: 4001;
}
/* .jqmWindowContent{
	background-color: #EEE;
}

.jqmOverlay { background-color: #000; }*/


* html .modal_popup_window {
	position: absolute;
	top: expression((document.documentElement.scrollTop || document.body.scrollTop) + Math.round(23 * (document.documentElement.offsetHeight || document.body.clientHeight) / 100) + 'px');
}
</style>

<script type="text/javascript">
$().ready(function() {
	$("#callstoresmap").on('click', function(e){
		e.preventDefault();
		console.log("click");

		$.post("<?= /*$this->__template->GetFolder().*/"/local/templates/aspro_next/components/bitrix/catalog.element/main/ajax_map.php"; ?>", {id: <?=$arResult["ID"]?>, stores: <?=\Bitrix\Main\Web\Json::encode($arrMapFilter);?>},
			function(data){
// console.log(data);
			$( "body" ).append( data );
			$(".modal_popup_window.overlay").show(300);
			$("#stores_map").show(300);
			$( "body" ).css('overflow','hidden');

			waitScriptLoad();

			$("#stores_map.modal_popup_window a.close.modal_popup_window_close, .modal_popup_window.overlay").on('click', function(e){
				e.preventDefault();
				console.log("close1");
				$("#stores_map").hide(300).remove();
				$(".modal_popup_window.overlay").hide(300).remove();
				$( "body" ).css('overflow','scroll');
			});

		});

    });

});


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
</script>

<script type="text/javascript">
// (function(){
//   function waitScriptLoad(){
//     if(!window.whatasoftMaps){
//       setTimeout(waitScriptLoad, 50);
//     }else{
//       whatasoftMaps.ready(function(){
//         $(".was-map-yandex-ajax").each(function(i, el){
//           wmap = new WASAjaxYandexMapList();
//           wmap.Init($(el));
//         });
//       });
//     }
//   }
//
//   jQuery(function(){
//     $(document).ready(function(){
//       waitScriptLoad();
//     });
//   });
// }());


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

</script>
