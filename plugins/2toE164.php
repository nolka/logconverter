<?php

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
    if(strlen($number) > 6)
    {
        if(strlen($number) == 7)
        {
            $number = '7391'.$number;
        }
        if(preg_match('/^810/', $number))
        {
            $number = preg_replace("/^810/", "", $number);
        }
        if(substr($number,0,1) == 8)
        {
             $number = '7'.substr($number,1); 
        }
        return $number;
    }
    else
    {
        return false;
    }
}

RegisterHandler('toE164');

?>
