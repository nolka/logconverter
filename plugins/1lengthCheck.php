<?
/*
Плагин, предназначенный для проверки телефонных номеров на правильность.
Под правильностью понимается длина телефонного номера. 
Если телефонный номер  короче 7 символов - он неправильный, и его 
не требуется обрабатывать.
*/

function checkNumbersLen($data)
{
    $line = $data['src'];
    $gibs = $data['gibs'];
    if(strlen($gibs[2])<7)
        return false;
    if(strlen($gibs[3])<7)
        return false;
    if(strlen($gibs[4])<7)
        return false;
    if(strlen($gibs[5])<7)
        return false;
    
    return $data;
}

RegisterHandler('checkNumbersLen');
?>