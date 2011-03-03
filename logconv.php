#!/usr/bin/env php5
<?

global $options;
$options = array(
                "converter" => null,
                "file_mask" => "*.log",
                "out_dir"   => "./",
                "input_dir" => "./",
                "clean" => false,
                "ziponly" => false,
                "nozip" => false);

// массив, в котором будут храниться имена функций плагинов-обработчиков
$Proccessors = array();

// функция регистрации плагина-обработчика
function RegisterHandler($handler_name)
{
    global $Proccessors;
    // просто добавляем имя обработчика в массив
    $Proccessors[] = $handler_name;
}

// функция обработки
function CallHandlers($data)
{
    global $Proccessors;
    $result = null;
    foreach($Proccessors as $id => $handler)
    {
        $result = call_user_func($handler, (($result === null)?$data:$result));
        if($result === false)
        {
            return false;
        }
    }
    return $result;
}

// врителн)
function writeln($msg)
{
    echo str_replace("\n","",$msg)."\n";
}

// функция загрузки плагинов - обработчиков записей
function LoadPlugins()
{
    writeln('loading plugins...');
    $files = glob('plugins/*.php');
    foreach($files as $plugin)
    {
        writeln('   file: '.$plugin);
        require_once $plugin;
    }
}

function cleanDir($dir)
{
    foreach(glob($dir."/*") as $file)
    {
        unlink($file);
    }
}

function zipFiles($src, $dest)
{
    foreach(glob($src.'*') as $file)
    {
        $run = '7z a -tzip '.$dest.'/'.basename($file).'.zip '.$file;
        writeln("execing: ".$run);
        system($run);
    }
}

// формируем имя файлы, в который будет записана строка
function getFilename($gibs)
{
    preg_match("/([\d]+)\..*\s([\d]+)\:/", $gibs[0], $matches);
    return $matches[1]."_".$matches[2];
}

function parseFile($filename)
{
    global $options;
    writeln("** Parsing ".$filename);
    
    // если выбранный конвертер не сможет открыть файл с грязными логами
    // мы не сможем получить оттуда данные - выходим
    if(!converterDoFile($filename))
    {
        die('Cannot open file');
    }
    
    // читаем данные из грязного лога и возвращаем их в формате BGB, описанном
    // вот здесь: http://bgbilling.ru/v4.6/doc_op/billing.html#d0e19261
    // данные возвращаются в виде ассоциативного массива: 
    // array('src' => 'исходная строка и лога', 'gibs' => 'массив, получаемый функцией explode("\t", "исходная строка лога")')
    while($data = converterGetData())
    {
        // каждую строчку лога отдаем плагинам обработчикам.
        // эти плагины помогают определить, нужна ли текущая строчка лога,
        // если нужна, то могут над ней что-то сделать, например, привести номера в формат e164
        $line = CallHandlers($data);
        
        // если все обработчики завершились успешно, пишем эту строчку в логфайл
        if($line)
        {
            // getFilename помогает узнать, в какой файл нужно записать строчку.
            $h = fopen($options['out_dir']."/".getFilename($line['gibs']), "a");
            fwrite($h, $line['src']);
            fclose($h);
        }
        
    }
}

writeln("log grepper by xternalx. visit me at http://xternalx.com");

// обрабатываем параметры командной строки
for($i = 0; $i < $argc; $i++)
{
    switch($argv[$i])
    {
        // директория, в которой будем искать исходные логи
        case "-i": # input dir
        {
            $options['input_dir'] = $argv[$i+1];
            break;
        }
        
        // директория в которой будут сохраняться готовые чистые кусочки логов
        case "-o": # output dir
        {
            $options['out_dir'] = $argv[$i+1];
            break;
        }
        
        // конвертер, который будет использоваться для чтения исходного формата логов и приводить его в формат bgb
        case "-c": # output dir
        {
            $options['converter'] = $argv[$i+1];
            $conv = 'converters/'.$options['converter'].'.php';
            if(file_exists($conv))
            {
                // если указанный конвертер существует - инклудим его, и сообщаем об этом
                require_once $conv;
                writeln('using converter '.$options['converter']);
            }
            else
            {
                // если конвертера не будет, мы не сможем получить записи из существующих
                // логов, и обработать их, сообщаем об этом пользователю и выходим
                die('converter "'.$options['converter'].'" not found. exiting');
            }
            break;
        }
        
        // маска файлов "сырых" логов, которые используются как входные данные
        case "-m": # files input mask
        {
            $options['file_mask'] = $argv[$i+1];
            break;
        }
        
        case "-clean":
        {
            $options['clean'] = true;
            break;
        }
        
        case "-nozip":
        {
            $options['nozip'] = true;
            break;
        }
        
        case "-onlyzip": 
        {
            $options['onlyzip'] = true;
            break;
        }

        default: break;
    }
}

// покажем что у нас получилось
print_r($options);

// загржужаем плагины
LoadPlugins();

// если указан clean, значит, нам нужно почистить чистить директорию 
// с готовыми кусочками
if($options['clean'] === true)
{
    // очистим директорию назначения
    cleanDir($options['out_dir']);
}


// ищем все файлы в исходной директории
foreach(glob($options['input_dir']."/".$options['file_mask']) as $dirtyLog)
{
    // и обрабатываем их по очереди
    parseFile($dirtyLog);
}

writeln('done parsing and splitting files!');

// если не указан ключ nozip, значит нужно заархивировать логи по окончанию работы
// по разбивке сырого лога на кусочки
if($options['nozip'] == false)
{
    writeln("now, i'll try to compress your files:)");
    // в эту папку будем складывать зазипованные логи
    $zipdir = $options['out_dir']."zip";

    // если директори нет, создаем ее, если она есть - чистим
    if(!file_exists($zipdir))
    {
        if(mkdir($zipdir))
        {
            writeln("zip dir created");
        }
        else
        {
            die('zip dir not created - exiting');
        }
    }else
    {
        cleanDir($zipdir);
    }

    // архивируем кусочки логов
    zipFiles($options['out_dir'], $zipdir);
}

?>
