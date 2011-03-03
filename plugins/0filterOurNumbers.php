<?php
/*
ƒанный плагин необходим дл€ того, чтобы из огромного лога вырезать только те CDR записи,
в которых присутствуют телефонные номера наших абонентов. ќстальные номера будут проигнорированы
*/

function filterNumbers($data)
{
    $line = $data['src'];
    $gibs = $data['gibs'];
    
    if(!preg_match("/25[27]90/", $line))
        return false;
    
    return $data;
}

RegisterHandler('filterNumbers');
?>
