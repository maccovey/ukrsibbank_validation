<?php

/*
/*Класс для валидации данных специально для оформления рассрочки в УкрСибБанке


/* author: a.shraer@gmail.com
*/


define("DEBUG_MSG", false);

function console_log($msg){

    if (DEBUG_MSG){
        echo $msg.PHP_EOL;
     }
}

class customerPerson{

    var $surname      = '';
    var $first_name   = '';
    var $patronymic   = '';
    var $birthday     = '';
    var $passport_id  = '';
    var $inn          = '';
    var $mobile_phone = '';
    var $email        = '';

    var $error_msg    = '<ul>';

    var $result       = '';

    public function __construct(
                $surname,
				$first_name,
				$patronymic,
				$birthday,
				$inn,
				$passport_id,
				$mobile_phone,
				$email
				)
    {
        $this->surname      = $surname;
        $this->first_name   = $first_name;
        $this->patronymic   = $patronymic;
        $this->birthday     = $birthday;
        $this->inn     	    = $inn;
        $this->mobile_phone = $mobile_phone;
        $this->email        = $email;
    }


     private function decode_inn($inn){

        //$id must contain 10 digits
        if (empty($inn) || !preg_match('/^\d{10}$/',$inn)) return false;

        $result = array();
        $result['inn'] = $inn;
        $result['sex'] = (substr($inn, 8, 1) % 2) ? 'm' : 'f';
        $split = str_split($inn);
        $summ = $split[0]*(-1) + $split[1]*5 + $split[2]*7 + $split[3]*9 + $split[4]*4 + $split[5]*6 + $split[6]*10 + $split[7]*5 + $split[8]*7;
        $result['control'] = (int)($summ - (11 * (int)($summ/11)));
        $result['valid'] = ($result['control'] == (int)$split[9]) ? true : false;
        $inn = substr($inn, 0, 5);
        $normal_date = date('d.m.Y', strtotime('01/01/1900 + ' . $inn . ' days - 1 days'));
        list($result['day'], $result['month'], $result['year']) = explode('.', $normal_date);
        return $result;
    }

    /*проверка что стррка не пустая и с кирилическими символами и знаками - и ' */
    protected function is_str_valid($string){
        if(!empty($string)){
        mb_internal_encoding('UTF-8');
        $remaining=preg_replace('/[-йцукенгшщзхїфивапролджєґячсмітьбюыё\']/u','',mb_strtolower($string));

        if (empty($remaining)){
            console_log("string cyrillic");
            return true;
        }
        console_log("string NOT cyrillic");
        $this->error_msg.='<li>Строка может содержать только буквы кирилицы ('.$string.')</li>';
        }else{
            $this->error_msg.='<li>Строка не может быть пустой</li>';
            return false;
        }
    }

    // определяет правильность ИНН по дате рождения
    protected function is_inn_valid(){

       $parsed_inn = $this->decode_inn($this->inn);
       if ($parsed_inn["valid"]) {
           console_log("inn valid");

      //     var_dump(DateTime::createFromFormat('Y-m-d',$parsed_inn["year"].'-'.$parsed_inn["month"].'-'.$parsed_inn["day"]));
      //     DateTime::createFromFormat('Y-m-d', $this->birthday);

           if (DateTime::createFromFormat('Y-m-d',$parsed_inn["year"].'-'.$parsed_inn["month"].'-'.$parsed_inn["day"])
               == DateTime::createFromFormat('Y-m-d', $this->birthday)) {

           return true;
           }
           else {
               console_log("inn or birthday is INVALID");
               $this->error_msg.="<li>Проверьте правильность ИНН или даты рождения</li>";

           }
    }else{
        console_log("inn INVALID");
     $this->error_msg.="<li>Проверьте правильность ИНН</li>";
    return false;
       }
    }


    protected function age_in_range($age_from, $age_to){
        $age=$this->get_age();
        if ($age>=$age_from && $age<=$age_to){
            console_log('age_valid ='.$age);
        return true;
        }
        $this->error_msg.='<li>Возраст не подходит по правилам предоставления кредита ('.$age.' лет)</li>';
        console_log('age INVALID ='.$age);
        return false;
    }


    private function get_age(){
        date_default_timezone_set('Europe/Kiev');
        $d1 =  DateTime::createFromFormat( 'Y-m-d', $this->birthday );
        $d2 = new DateTime('NOW');
        $age = $d2->diff( $d1 );
        return $age->y;
    }


    private function is_passport_id_valid(){

        return true;
    }

    private function is_mobile_phone_valid(){

        $valid_codes = array ( '039', '050','063', '066', '067', '068', '091', '092', '093', '094', '096', '097', '098', '099',
            '031', '032', '033', '034', '035', '036', '037', '038', '041', '042', '043', '044', '045', '046', '047', '048', '049',
            '051', '052', '053', '054', '055', '056', '057', '058', '059', '061', '062', '063', '064', '065', '069'
                             );
        $blacklisted_phones = array ('0000000', '1111111', '2222222','3333333','4444444','5555555','6666666','7777777','8888888','9999999','1234567','5678912');

        $phone_number = str_replace(array(' ','-'), '', $this->mobile_phone);
        if (strlen($phone_number)==10){
        $phone_code=substr($phone_number,0,3);
        $phone_body=substr($phone_number,3,10);

        if (in_array($phone_code, $valid_codes) && (!in_array($phone_body,$blacklisted_phones))) {

            console_log('valid phone ='.$phone_code.$phone_body);
            return true;
        }
        $this->error_msg.='<li>Неверный номер телефона ('.$this->mobile_phone.')</li>';
        console_log('INVALID phone ='.$phone_code.$phone_body);
        }else {
            $this->error_msg.='<li>Неверный формат номера телефона ('.$this->mobile_phone.')</li>';
            return false;
        }


    }

    private function is_email_valid(){
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        console_log('email valid ='.$this->email);
        return true;
        }
        else {
        $this->error_msg.='<li>Адрес e-mail не правильный ('.$this->email.')</li>';
        console_log('email INVALID ='.$this->email);
        return false;
        }
    }


    public function is_all_data_valid(){
//    echo 'validating inn';
        $this->result = array (
          'surname'    =>  $this->is_str_valid($this->surname),
          'first_name' =>  $this->is_str_valid($this->first_name),
          'patronymic' =>  $this->is_str_valid($this->patronymic),
          'age_valid'  =>  $this->age_in_range(21,65),
          'inn_valid'  =>  $this->is_inn_valid(),
          'passport_id_valid'  => $this->is_passport_id_valid(),
          'mobile_phone_valid' => $this->is_mobile_phone_valid(),
          'email_valid' => $this->is_email_valid()
        );

    $check=0;
    foreach($this->result as $row) {
        $check |= !$row;
    }
     return !$check;
    }

    //private function 



}


?>