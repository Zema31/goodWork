Необходимо создать миграции в Laravel с такими таблицами:

<p>Пользователи:</p>
<ul>
  <li>Емайл</li>
  <li>Пароль</li>
  <li>Имя</li>
</ul>

<p>Кампании:</p>
<ul>
  <li>Название кампании</li>
  <li>Статус кампании (Один из - Активен, В ожидании, В архиве)</li>
  <li>Создатель</li>
</ul>

<p>Объявления:</p>
<ul>
  <li>Тайтл объявления (Строка)</li>
  <li>Текст объявления</li>
  <li>Статус (Один из - Активен, В ожидании, В архиве)</li>
    <li>URL</li>
    <li>Количество показов</li>
    <li>CPM</li>
    <li>Бюджет объявления</li>
    <li>Текст кнопки объявления (Один из - Смотреть, Оставить заявку, Скачать, Подробнее)
</li>
</ul>

Описание: 
Один пользователь может создавать несколько кампаний и внутри этих кампаний может создавать множество объявлений. 
Объявления закрепляются за кампаниями, а кампании за пользователями, которые их создали. 

<p>Реализовать:</p>
<ul>
  <li>Список</li>
  <li>Создание</li>
  <li>Удаление</li>
</ul>
 Без редактирования и просмотра конкретного элемента.

 <p>Нюансы:</p>
<ul>
  <li>Если хотя бы одно объявление кампании в статусе “Активен”, кампания должна иметь такой же статус.</li>
  <li>Когда редактируем статус кампании на “В ожидании” - все объявления внутри кампании должны так же переходить на “В ожидании”. Со статусом в архиве то же самое. </li>
  <li>При изменении текста объявления статус должен на 3 минуты переходить “В ожидании”.</li>
     <li>При удалении пользователя - удаляются созданные ими кампании.</li>
     <li>Бюджет не может быть меньше 0. Если он упал до 0, то переводить объявление в статус “В ожидании”.</li>
     <li>При изменении бюджета на сумму выше 0, например если было 0 и мы поставили 5, то статус объявления из “В ожидании” должен переходить в “Активен”.</li>
</ul>
