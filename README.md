# bitrix scripts
Личные "полезные" скрипты для bitrix, которые могут быть полезны всем.


delete_collection_null.php - 
<code>
ищет в инфоблоке 10 элементы по arfilter, далее перебирает ID в другом инфоблоке (товары) и ищет заполнен ли данный ID и если нет, удаляет запись в инфоблоке 10.
Полезно, когда у вас разные поставщики и они создают мусорные записи.
Заготовка подходит для разных свойств и тд.
</code>

delete_detail_text_items.php - 
<code>
ищет товары в инфоблоке 15 с заполненным DETAIL_TEXT по артикулам. Далее, если DETAIL_TEXT есть, удаляем его у товара.
Заготовка подходит для разных свойств и тд.
</code>

items_detail_text_chatgpt.php - 
<code>
получает товар и всего его заполненные хар-ки. Формирует текст запроса и передаем его в ChatGPT, далее получает ответ от GPT и записывает его в товар. Важно понимать, что это лишь пример.
</code>
![image](https://github.com/s0rkin/bitrix_scripts/assets/12657938/54e08268-175f-46d4-ac97-497fdba4ab26)

no-photo.php - 
<code>
  Получает в инфоблоке товары без детальной фотографии, можно сделать для любого свойства. Полезно для контенщиков, сразу ссылка на товар публичная и ссылка в админку отдельно, для редактирования.
</code>

![image](https://github.com/s0rkin/bitrix_scripts/assets/12657938/7dd52807-1559-4b0d-b364-820a4366e6ee)

