<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/site';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Подключение модуля Информационных блоков
CModule::IncludeModule("iblock");

// ID инфоблока с товарами
$iblockId = 10; // Замените на ID своего инфоблока

// Получение всех товаров инфоблока
$arFilter = array(
    "IBLOCK_ID" => $iblockId,
    "ACTIVE" => "Y",
);

$arSelect = array("ID", "IBLOCK_ID", "NAME");

//ntopcount количество коллекций для проверки, аккуратнее, если у вас много ID.
$res = CIBlockElement::GetList(array("RAND" => "ASC"), $arFilter, false, array("nTopCount" => 1000), $arSelect);

while($ob = $res->GetNextElement()){
	$arFields = $ob->GetFields();
    //print_r("<br>" . $arFields["ID"] . " " . $arFields["NAME"]);

    //делаем выборку по ID коллекции
    foreach($arFields as $colId){

        $colSelect = array("ID");
        $colFilter = array(
	    //инфоблок товаров, замените на свой
            "IBLOCK_ID" => 15,
            //свойство коллекции
            "PROPERTY_COLLECTION" => $arFields["ID"],
        );
        //получаем товары коллекции
        $col = CIBlockElement::GetList(array(), $colFilter, false, array(), $colSelect)->Fetch();

    }
    // если нет товаров у коллекции, удаляем ее
    if(empty($col)){
        print_r("<br>" . "!!! CATCH " . $arFields["ID"] . " " . $arFields["NAME"]);
        $DB->StartTransaction();
	    if(!CIBlockElement::Delete($arFields["ID"]))
	    {
	    	$strWarning .= 'Error!';
            print_r("<br>" . $strWarning . "не удален " . $arFields["ID"]);
	    	$DB->Rollback();
	    }
	    else
	    	$DB->Commit();
            print_r("Успешно удален элемент " . $arFields["ID"]);
    }
    else
        print_r("<br>Ничего не найдено!");
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
