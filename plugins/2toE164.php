<?php
/*
Плагин, предназначенный для привидения номеров к стандарту E164
*/

function toE164($data)
{
    $line = $data['src'];
    $gibs = $data['gibs'];
    $gibs[3] = convert($gibs[3]);
    $gibs[5] = convert($gibs[5]);
    $line = implode("\t", $gibs);
    return array('src'=> $line, 'gibs' => $gibs);
}

function convert($number)
{
    // Если длина номера больше 6, значит, это нормальный номер, а не какой то служебный,
    // Будем пробовать его тарифицировать.
    if(strlen($number) > 6)
    {
        // Городские номера
        if(strlen($number) == 7)
        {
            // Код нашего города 391. Замените этот код на Ваш при необходимости
            $number = '7391'.$number;
        }
        // Неверно набранные номера тоже удаляем
        if(strlen($number) == 8 || strlen($number) == 9)
        {
            writeln("wrong CDR number: ".$number);
            return false;
        }
        // Номера на мобильные и МГ, которые были набраны без 8ки
        if(strlen($number) == 10)
        {
            // Добавляем 7 вначале
            $number = '7'.$number;
        }
        // Международная связь
        if(preg_match('/^810/', $number))
        {
            $number = preg_replace("/^810/", "", $number);
        }
        // Межгород
        if(substr($number,0,1) == 8)
        {
             $number = '7'.substr($number,1); 
        }
        return $number;
    }
    else
    {
        // иначе возвращаем false. 
        // В этом случае, данная CDR запись не будет передана на обработку остальным плагинам-обработчикам
        return false;
    }
}

// Зарегистрировали функцию-обработчик
RegisterHandler('toE164');

?>
