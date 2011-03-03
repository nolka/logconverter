<?

$fh = null;

// функция, открывающая грязный логфайл на чтение
function converterDoFile($filename)
{
    global $fh;
    $fh = fopen($filename, "r");
    if(false === $fh)
    {
        return false;
    }
    else
    {
        return true;
    }
        
}

// функция, возвращающая данные из грязного лога в виде ассоциативного массива,
// за дополнительной информацией см ats_grepko.php
// здесь осуществляется основная логика по преобразованию данных из грязного лога
// в формат, понятный БГБиллингу
function converterGetData()
{
    global $fh;
    
    // пытаемся получить строчку из грязного лога
    $line = fgets($fh);
    
    if($line === false)
    {
        // если не получили - говорим скрипту что плохи дела
        return false;
    }
    
    // если получили, готовим нашу структуру данных
    $la = explode("\t", $line);

    $res = array();
    // сюда кладем исходную строчку лога
    $res['src'] = $line;
    // сюда кладем массив
    $res['gibs'] = $la;
    // отдаем
    return $res;
}
?> 
