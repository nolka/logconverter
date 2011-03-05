<?php
/*
������, ��������������� ��� ���������� ������� � ��������� E164
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
    // ���� ����� ������ ������ 6, ������, ��� ���������� �����, � �� ����� �� ���������,
    // ����� ��������� ��� ��������������.
    if(strlen($number) > 6)
    {
        // ��������� ������
        if(strlen($number) == 7)
        {
            // ��� ������ ������ 391. �������� ���� ��� �� ��� ��� �������������
            $number = '7391'.$number;
        }
        // ������� ��������� ������ ���� �������
        if(strlen($number) == 8 || strlen($number) == 9)
        {
            writeln("wrong CDR number: ".$number);
            return false;
        }
        // ������ �� ��������� � ��, ������� ���� ������� ��� 8��
        if(strlen($number) == 10)
        {
            // ��������� 7 �������
            $number = '7'.$number;
        }
        // ������������� �����
        if(preg_match('/^810/', $number))
        {
            $number = preg_replace("/^810/", "", $number);
        }
        // ��������
        if(substr($number,0,1) == 8)
        {
             $number = '7'.substr($number,1); 
        }
        return $number;
    }
    else
    {
        // ����� ���������� false. 
        // � ���� ������, ������ CDR ������ �� ����� �������� �� ��������� ��������� ��������-������������
        return false;
    }
}

// ���������������� �������-����������
RegisterHandler('toE164');

?>
