# bitrix_scripts
Личные скрипты для bitrix


delete_collection_null.php - ищет в инфоблоке 10 элементы по arfilter, далее перебирает ID в другом инфоблоке (товары) и ищет заполнен ли данный ID и если нет, удаляет запись.
Полезно, когда у вас разные поставщики и они создают мусорные записи.


delete_detail_text_items.php - ищет товары в инфоблоке 15 с заполненным DETAIL_TEXT по артикулам. Далее, если DETAIL_TEXT есть, удаляем его у товара.
