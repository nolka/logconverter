<?php
/*
������ ������ ��������� ��� ����, ����� �� ��������� ���� �������� ������ �� CDR ������,
� ������� ������������ ���������� ������ ����� ���������. ��������� ������ ����� ���������������
*/

function filterNumbers($data)
{
    $line = $data['src'];
    $gibs = $data['gibs'];
    
    if(!preg_match("/25[27]9[02]/", $line))
        return false;
    
    return $data;
}

RegisterHandler('filterNumbers');
?>
