## Валидация полей для интеграции с рассрочкой от УкрСибБанка

Этот репозиторий создан для тех, кто не хочет писать велосипед по проверке всех полей перед отправкой в банк (!)

- Проверяется, чтобы имя, фамилия и отчество содержало только кирилические символы
- Проверяется, чтобы возраст был от 21 до 65 лет
- Проверяется правильность ввода ИНН
- Проверяется соответствие даты рождения и ИНН
- Проверяется номер мобильно телефона по кодам операторов и запрещенных номеров
- Проверяется правильность e-mail


Для начала нужно создать экземпляр класса validateCustomer со всеми данными

```php
$customer = new validateCustomer(
    "Квітка-Основ'яненко",
    "Григорій",
    "Федорович",
    "1989-01-25",
    "3253218857",
    "ма1234567",
    "063 123-55-66",
    "aa@aa.ru"
);
```

Затем вызвать метод `is_all_data_valid()`, который возвращает `TRUE`, если все поля правильные и `FALSE` если в каком-то поле есть ошибка.

Также для вывода ошибок в форме, экземпляр имеет свойство `error_msg` – в котором содержатся тексты ошибок.

Для детальной информации в каких полях есть ошибка, а в каких нет, возвращается массив `result`.

Выглядит он так, где `1` (`TRUE`), поле правильное, где пусто (`FALSE`) поле не прошло проверку.

```php
Array
(
    [surname] => 1
    [first_name] => 
    [patronymic] => 1
    [age_valid] => 1
    [inn_valid] => 1
    [passport_id_valid] => 1
    [mobile_phone_valid] => 1
    [email_valid] => 1
)
```


Пример кода

```php
<?php

include_once("ValidateCustomer.php");
$customer = new validateCustomer(
    "Квітка-Основ'яненко",
    "Григорій",
    "Федорович",
    "1989-01-25",
    "3253218857",
    "ма1234567",
    "063 123-55-66",
    "aa@aa.ru"
);
if (!$customer->is_all_data_valid()){
  echo $customer->error_msg;
  print_r($customer->result);
}
else {echo "OK".PHP_EOL;
  print_r($customer->result);
};
?>
```
