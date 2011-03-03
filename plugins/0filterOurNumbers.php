<?php
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
