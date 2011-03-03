<?

$fh = null;

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

function converterGetData()
{
    global $fh;
    
    $line = fgets($fh);
    if($line === false)
    {
        return false;
    }
    
    $la = preg_split("/(\s*\|\s*)/", $line);
    $date = substr($la[0],1);

    $d = new DateTime($date);
    $date = $d->Format('d.m.Y H:i:s');
    $day = $d->Format('d');
    $hour = $d->Format('H');

    $res = array();
    $res['src'] = $date."\t".$la[3]."\t".$la[1]."\t".($la[1])."\t".$la[2]."\t".($la[2])."\tA0\tB0\t0\t".$la[3]."\t0\n";
    $res['gibs'] = explode("\t", $res['src']);
    return $res;

}
?> 
