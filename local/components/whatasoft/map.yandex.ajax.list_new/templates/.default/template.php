<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
$this->setFrameMode(true);
?>

<div class="geo-map was-map-yandex-ajax" 
    <?if(strlen($arParams["MAP_ID"])){?>
    id="<?=$arParams["MAP_ID"]?>"
    <?}?>
    data-ajax_cache_id="<?=$arResult["AJAX_CACHE_ID"]?>"
    data-type="<?=$arParams["INIT_MAP_TYPE"]?>" 
    data-auto_scale_center="<?=($arParams["MAP_AUTO_SCALE_CENTER"] == "Y" ? '1' : '0')?>" 
    data-center="[<?=$arParams["MAP_CENTER"]?>]" 
    data-zoom="<?=$arParams["MAP_SCALE"]?>" 
    data-behaviors="<?=implode(",", $arParams["MAP_YMAP_BEHAVIORS"])?>" 
    data-controls="<?=implode(",", $arParams["MAP_YMAP_CONTROLS"])?>" 
    data-balloon_icon_content="<?=($arParams["USE_ICON_CONTENT"] == "Y" ? '1' : '0')?>" 
    data-balloon_color="<?=$arParams["MAP_COLOR"]?>" 
    <?if(count($arParams["MAP_ICON_DATA"])){?>
    data-balloon_icon="<?=$arParams["MAP_ICON_DATA"]["FILE"]["SRC"]?>" 
    data-balloon_icon_size="<?=$arParams["MAP_ICON_DATA"]["WIDTH"]?>,<?=$arParams["MAP_ICON_DATA"]["HEIGHT"]?>" 
    data-balloon_icon_offset="<?=$arParams["MAP_ICON_DATA"]["OFFSET"]?>" 
    data-balloon_icon_content_offset="<?=$arParams["MAP_ICON_DATA"]["CONTENT_OFFSET"]?>" 
    data-balloon_icon_content_size="<?=$arParams["MAP_ICON_DATA"]["CONTENT_SIZE"]?>" 
    <?}?>
    data-cluster="<?=($arParams["MAP_CLUSTER"] == "Y" ? '1' : '0')?>" 
    data-cluster_simple="<?=($arParams["MAP_CLUSTER_SIMPLE"] == "Y" ? '1' : '0')?>" 
    data-cluster_content_width="<?=intval($arParams["MAP_CLUSTER_CONTENT_WIDTH"])?>" 
    data-cluster_content_left_column_width="<?=intval($arParams["MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH"])?>" 
    style="width: <?=$arParams["MAP_WIDTH"]?>; height: <?=$arParams["MAP_HEIGHT"]?>">
    <div class="was-map-spinner"></div>
</div>