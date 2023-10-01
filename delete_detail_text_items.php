<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/site';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Подключение модуля Информационных блоков
CModule::IncludeModule("iblock");

// ID инфоблока с товарами
$iblockId = 15; // Замените на ID своего инфоблока

// Получение всех товаров инфоблока по фильтру
$arFilter = array(
    "IBLOCK_ID" => 15,
//    "ACTIVE" => "Y",
    "!DETAIL_TEXT" => false,
//    "CATALOG_AVAILABLE" => "Y",
    //свойство артикул в массиве, можно указать сколько надо.
    "PROPERTY_CML2_ARTICLE" => array(
        "1313131313",     
    )
);

$arSelect = array("ID", "IBLOCK_ID", "NAME", "DETAIL_TEXT");

//ntopcount количество товаров для проверки
$res = CIBlockElement::GetList(array(), $arFilter, false, array("nTopCount" => 1000), $arSelect);

//перебираем товары по фильтру и если есть DETAIL_TEXT удаляем этот текст.
while($ob = $res->GetNextElement()){
  $arFields = $ob->GetFields();
    print_r("<br>" . $arFields["ID"] . " " . $arFields["NAME"]);

    if (!empty($arFields['DETAIL_TEXT'])){
        print_r($arFields['NAME'] . ' - ' . $arFields['ID']);
        $el = new CIBlockElement;
        $arLoadProductArray = Array(
          "DETAIL_TEXT" => "",
        );
        $PRODUCT_ID = $arFields['ID'];
        $res1 = $el->Update($PRODUCT_ID, $arLoadProductArray);
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
