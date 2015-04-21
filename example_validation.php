<?php

// Test of validation specially for UkrSibBank

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