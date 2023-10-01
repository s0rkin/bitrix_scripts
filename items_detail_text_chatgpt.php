<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/site';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

// Подключение модуля Информационных блоков
CModule::IncludeModule("iblock");

// ID инфоблока с товарами
$iblockId = 1; // Замените на ID своего инфоблока

// Получение всех товаров инфоблока
$arFilter = array(
    "IBLOCK_ID" => $iblockId,
    "ACTIVE" => "Y",
    "DETAIL_TEXT" => false,
    //только товары в наличие
    "=AVAILABLE" => "Y",
);

$arSelect = array("ID", "IBLOCK_ID", "NAME");

// получаем 1 товар рандом по фильтру.
$res = CIBlockElement::GetList(array("RAND" => "ASC"), $arFilter, false, array("nTopCount" => 1), $arSelect);

//создаем пустую переменную, потом наполняем ее и передаем в GPT.
$test = "";

while($ob = $res->GetNextElement()){
	$arFields = $ob->GetFields();
        print_r("\n" . "Дата: " . date('Y-m-d H:i:s'));
        print_r("\n" . "Начинаю процесс генерации описания для товара - " . $arFields["ID"] . " " . $arFields["NAME"]);
        print_r("\n" . "Битрикс отправил текст : ");
        //формируем запрос для gpt
        $test .= "Перепиши описание на русском языке для товара более развернуто, без выделения характеристик и добавь что знаешь - " . $arFields['NAME'] . " .\n";
        $arProps = $ob->GetProperties(array(), array("EMPTY" => "N"));
  
        foreach ($arProps as $arProp){
            $arPropName = $arProp['NAME'];
            $arPropValue = $arProp['VALUE'];

            //выборка хар-ик по полю SORT от 10 до 600.
            if($arProp['SORT'] > 10 and $arProp['SORT'] <= 600){

                if($arProp["MULTIPLE"] != "Y" and $arProp["PROPERTY_TYPE"] != "E"){
                    //добавляем к тексту хар-ки (заменяя символы)
                    $test .= (preg_replace('/[\[{\(].*?[\]}\)]/' , '', $arPropName) .  " : " . $arPropValue);
                }
                // Если это множественное значение у хар-ик
                if ($arProp["MULTIPLE"] == "Y") {
                     //добавляем к тексту хар-ки (заменяя символы)
                    $test .= (preg_replace('/[\[{\(].*?[\]}\)]/' , '', $arPropName) . " : ");
                    // Обработка множественных значений, в цикле
                    foreach ($arProp["VALUE"] as $value) {
                        //добавляем к тексту хар-ки
                        $test .= ($value . " ");
                    }
                }
                //проверка на хар-ки "E" из других инфоблоков
                if ($arProp["PROPERTY_TYPE"] == "E"){
                    $res = CIBlockElement::GetByID($arProp["VALUE"]);
                        if($ar_res = $res->GetNext())
                        //добавляем к тексту хар-ки (заменяя символы)
                        $test .= (preg_replace('/[\[{\(].*?[\]}\)]/' , '', $arPropName) . " : " . $ar_res['NAME']);
                }
            //добавляем к текстам запятую чтоб разбить текст для gpt
            $test .= " , ";
            }
        }
    }

print_r($test);

//Передаем в GPT готовый $test
//Выглядит это так - 
//Дата: 2023-10-01 06:26:03
//Начинаю процесс генерации описания для товара - 123131313
//Битрикс отправил текст : Перепиши описание на русском языке для товара более развернуто, без выделения характеристик и добавь что знаешь - Товар 123131313 .
//Высота, мм : 33 , Диаметр, мм : 111 , Бренд : Брено , Коллекция : Коллекция , Страна бренда : Россия , Стиль : Техно  , Гарантия : 12 мес

if(!empty($test)){
        // GPT URL НУЖНО ЗАПОЛНИТЬ!
        $api_url = '';
        // CHATGPT дата (сам запрос, как нибудь сами разберетесь.)
        //НУЖНО ЗАПОЛНИТЬ!
        $data = array(
        "messages" => array(
        //НУЖНО ЗАПОЛНИТЬ!
        ),
        //НУЖНО ЗАПОЛНИТЬ!
        "model" => "" 
        );

        $post_data = json_encode($data);
    
        $headers = array(
        //НУЖНО ЗАПОЛНИТЬ!
        //хедеры для запроса и авторизации.
        );

        $ch = curl_init($api_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error_response = curl_exec($ch); //сохраню отдельно error для вызова, если вдруг респонс корректный, а сам текст падает в битриксе.
        curl_close($ch);

        // $response содержит сгенерированное описание товара
        $test2 = json_decode($response, true);
        $description = $test2['choices'][0]['message']['content'];
       
        if(empty($description)){
            print_r("\n\n" . "CHATGPT ERROR! : " . $error_response);
        }

        if(!empty($description)){
            // Обновим описание товара в базе данных
            $el = new CIBlockElement;
            $arLoadProductArray = Array(
                "DETAIL_TEXT_TYPE" => "text",
                "DETAIL_TEXT" => html_entity_decode($description),
            );
            $res = $el->Update($arFields['ID'], $arLoadProductArray);

            print_r("\n\n" . "---------------------------------------------------------------------------------------");
            print_r("\n\n" . "CHATGPT вернул текст: " . $description);

            print_r("\n\n" . "---------------------------------------------------------------------------------------");
            print_r("\n\n" . "Данные успешно записаны в базу битрикс." . date('Y-m-d H:i:s'));
            print_r("\n\n" . "---------------------------------------------------------------------------------------");
        }
    }

if(empty($test)){
    print_r("\n\n" . "---------------------------------------------------------------------------------------");
    print_r("\n\n" . "Битрикс не вернул товары!!!");
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>

