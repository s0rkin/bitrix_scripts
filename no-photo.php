<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$quantity = 0;
$iblock_id = 10; #ID инфоблока с товарами
$site = "https://site.ru" #URL сайта

$arSelect = Array("IBLOCK_ID","SECTION_ACTIVE", "ID");
$arFilter = Array("IBLOCK_ID"=>$iblock_id,"SECTION_ACTIVE" => "Y", "PREVIEW_PICTURE" => false, "DETAIL_PICTURE" => false);
$res = CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, false, false, $arSelect);
while($ob = $res->Fetch())
    {
        $quantity++;
        $res_id = CIBlockElement::GetByID($ob['ID']);
      if($ar_res = $res_id->GetNext()) {
      $external_id["ID"] = $ar_res["ID"];
	  $external_id["NAME"] = $ar_res["NAME"];
	  $external_id["ACTIVE"] = $ar_res["ACTIVE"];
	  $external_id["URL"] = $ar_res["DETAIL_PAGE_URL"];
      }
   $arExternal_ID[] = $external_id;
   }

?>
<html>
<head>
	<meta name="robots" content="noindex, nofollow"/>
</head>
<body>
<div>
	<?if(!empty($arExternal_ID)):?>
	<title><? echo 'Всего элементов без фото в каталоге - '.$quantity;?></title>
	<h1><? echo 'Всего элементов без фото в каталоге - '.$quantity;?></h1>
	<ol>
	<?foreach ($arExternal_ID as $it => $arTab):?>
		<li><pre><a href="<?=$arTab['URL']?>" target="blank"><?=$arTab['NAME']?></a><text> - ID Товара: <?=$arTab['ID']?>. Товар активен? - <?=$arTab["ACTIVE"]?> ***<a href=""$site . "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $iblock_id . "&type=catalog&lang=ru&ID=<?=$arTab['ID']?>" target="blank">Ссылка в админку</a></text></pre></li>
	<?endforeach;?>
	</ol>
	<?else:?>
	<text>brrrr....</text>
	<?endif?>
</div>
</body>
</html>
