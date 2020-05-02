<?php

	if ( !isset($_FILES['docxFileInpt']['name']) ) header("location: ../index.php");
	session_start();
	echo"
		<script>
			var result = parent.window.document.getElementById('saved_form_result');
			var progressBar = result.querySelector('#progress-bar');
		</script>
	";
	$overalProcesses = 10;
	
	if ( isset($_SESSION['fromWord_data']) ) {
		require('../../Glob_Controllers/functs.php');
		rrmdir('../../../'.$_SESSION['fromWord_data']['tempDirName']);
		unset($_SESSION['fromWord_data']); //удаляем инфу из ворд файла и сами файлы
	}

$dir = _rootDIR_ . 'docx_temp';
$tempDirName = time()."";
mkdir($dir.'/'.$tempDirName, 0777, true);

$_SESSION['fromWord_data']['tempDirName'] = "docx_temp/$tempDirName";

$fileName = basename($_FILES['docxFileInpt']['name'], ".docx");

$_SESSION['fromWord_data']['number3D'] = $fileName;

$fileName .= ".zip";
move_uploaded_file($_FILES['docxFileInpt']['tmp_name'], "$dir/$tempDirName/$fileName");

$zip = new ZipArchive();
$res = $zip->open("$dir/$tempDirName/$fileName");
$zip->extractTo("$dir/$tempDirName/");

$filesImg = scandir("$dir/$tempDirName/word/media");

foreach ($filesImg as $key => $value ) {
	
	if ( $value == '.' || $value == '..' ) {
		continue;
	}
	$_SESSION['fromWord_data']['filesImg'][] = $value;
}
$_SESSION['fromWord_data']['pathToimg'] = "$tempDirName/word/media";
print_r($_SESSION['fromWord_data']['filesImg']);


function parseWord($userDoc) {
    $fileHandle = fopen($userDoc, "r");
    $word_text = @fread($fileHandle, filesize($userDoc));
    $line = "";
    $tam = filesize($userDoc);
    $nulos = 0;
    $caracteres = 0;
    for($i=1536; $i<$tam; $i++)
    {
        $line .= $word_text[$i];

        if( $word_text[$i] == 0)
        {
            $nulos++;
        }
        else
        {
            $nulos=0;
            $caracteres++;
        }

        if( $nulos>1996)
        {   
            break;  
        }
    }
	$progressCounter++; // 9 добавляем элемент когда задача выполнена 
	$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
	echo"
		<script>
			progressBar.style.width = $overalProgress + \"%\";
			progressBar.innerHTML = $overalProgress + \"%\";
		</script>
	";
    //echo $caracteres;

    $lines = explode(chr(0x0D),$line);
    //$outtext = "<pre>";

    $outtext = "";
    foreach($lines as $thisline)
    {
        $tam = strlen($thisline);
        if( !$tam )
        {
            continue;
        }

        $new_line = ""; 
        for($i=0; $i<$tam; $i++)
        {
            $onechar = $thisline[$i];
            if( $onechar > chr(240) )
            {
                continue;
            }

            if( $onechar >= chr(0x20) )
            {
                $caracteres++;
                $new_line .= $onechar;
            }

            if( $onechar == chr(0x14) )
            {
                $new_line .= "</a>";
            }

            if( $onechar == chr(0x07) )
            {
                $new_line .= "\t";
                if( isset($thisline[$i+1]) )
                {
                    if( $thisline[$i+1] == chr(0x07) )
                    {
                        $new_line .= "\n";
                    }
                }
            }
        }
        //troca por hiperlink
        $new_line = str_replace("HYPERLINK" ,"<a href=",$new_line); 
        $new_line = str_replace("\o" ,">",$new_line); 
        $new_line .= "\n";

        //link de imagens
        $new_line = str_replace("INCLUDEPICTURE" ,"<br><img src=",$new_line); 
        $new_line = str_replace("\*" ,"><br>",$new_line); 
        $new_line = str_replace("MERGEFORMATINET" ,"",$new_line); 

        $outtext .= nl2br($new_line);
		
		$progressCounter++; // 10 добавляем элемент когда задача выполнена 
		$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
		echo"
			<script>
				progressBar.style.width = $overalProgress + \"%\";
				progressBar.innerHTML = $overalProgress + \"%\";
			</script>
		";
    }

 return $outtext;
}
$progressCounter++; // 1 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";
$userDoc = "$dir/$tempDirName/word/document.xml";
$text = parseWord($userDoc);
$text = strip_tags( trim($text) );
//echo $text;

// №3д

/*
$pos = stripos($text, '000');
$number3D = substr($text, $pos, 8);

$_SESSION['fromWord_data']['number3D'] = $number3D;
*/

//echo '<pre>';
//echo ' Номер 3Д = '.$_SESSION['fromWord_data']['number3D'].'<br/>';

// 3д моделлер автор
$pos1 = stripos($text, 'Опер.оборуд');
$pos2 = stripos($text, 'Примечания');
$pos1 += 21; // Опер.оборуд длинна
$len = $pos2 - $pos1;
$author3Dmod = substr($text, $pos1, $len);
$author = substr($author3Dmod, 0, 17);
$mod3D = substr($author3Dmod, 17);

$_SESSION['fromWord_data']['author'] = trim($author);
$_SESSION['fromWord_data']['mod3D'] = trim($mod3D);

$progressCounter++; // 2 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

//echo ' Автор = '.$author.'<br/>';
//echo ' 3D моделлер = '.$mod3D.'<br/>';

// примечания
$pos1 = stripos($text, 'Примечания');
$pos2 = stripos($text, 'Название');
$pos1 += 20; // Примечания длинна
$len = $pos2 - $pos1;
$descr = substr($text, $pos1, $len);

//echo ' примечания = '.$descr.'<br/>';

$_SESSION['fromWord_data']['descr'] = trim($descr);
// END примечания

$progressCounter++; // 3 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

// камни
$pos1 = stripos($text, 'доработка');
$pos2 = stripos($text, 'Золото');
$pos1 += 18;
$len = $pos2 - $pos1;
$stones = substr($text, $pos1, $len);

$_SESSION['fromWord_data']['stones'] = $stones;

//echo ' камни = '.$stones.'<br/>';

$st_arr = explode('Ø', $stones);
/*
//echo '<pre>';
print_r($st_arr);
//echo '</pre>';
*/

$progressCounter++; // 4 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

// ссылки на доп артикулы
$pos1 = stripos($text, '003d модель');
$pos2 = stripos($text, 'Стрелками');
$pos1 += 17;
$len = $pos2 - $pos1;
$vcDop = substr($text, $pos1, $len);

$_SESSION['fromWord_data']['vcDop'] = $vcDop;
//echo ' ссылки на доп артикулы = '.$vcDop.'<br/>';

// коллекция
$pos1 = stripos($text, 'прочерк)');
$pos2 = stripos($text, 'Ссылки на');
$pos1 += 15;
$pos2 -= 14;
$len = $pos2 - $pos1;
$collection = substr($text, $pos1, $len);
$_SESSION['fromWord_data']['collection'] = $collection;
//echo ' коллекция = '.$collection.'<br/>';

$progressCounter++; // 5 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

// тип модели
$pos1 = stripos($text, 'Дата');
$pos2 = stripos($text, 'Участок');
$pos1 += 8;
$len = $pos2 - $pos1;
$modType = substr($text, $pos1, $len);
$modType = str_replace(",", ".", $modType);

$progressCounter++; // 6 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

// вес
$weights = preg_split('//u',$modType,-1,PREG_SPLIT_NO_EMPTY);
$weight = array();
$modTypes = array();
for( $i=0; $i<count($weights); $i++ ) {
		
	if ( $weights[$i] === '0' || $weights[$i] === '.' ) {
		$weight[] = $weights[$i];
		continue;
	}
	
	$nnn = intval($weights[$i]);
	if ( $nnn !== 0 ) {
		$weight[] = $nnn;
	} else {
		$modTypes[] = $weights[$i];
	}
}
	function ceiling($number, $significance = 1)
	{
		return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
	}

$weight = floatval(implode($weight));
$_SESSION['fromWord_data']['weight'] = ceiling($weight, 0.05);
//echo ' вес = '.ceiling($weight, 0.05).'<br/>';

$progressCounter++; // 7 добавляем элемент когда задача выполнена 
$overalProgress =  ceil( ( $progressCounter * 100 ) / $overalProcesses );
echo"
	<script>
		progressBar.style.width = $overalProgress + \"%\";
		progressBar.innerHTML = $overalProgress + \"%\";
	</script>
";

$modType = implode($modTypes);
$pos1 = stripos($modType, 'гр');

if ( $pos1 ) $modType = substr($modType, 0, $pos1);
$modType = trim($modType);
//echo ' тип модели = '.$modType.'<br/>';
$_SESSION['fromWord_data']['modType'] = $modType;

// 8 добавляем элемент когда задача выполнена 
echo"
	<script>
		progressBar.style.width = 100 + \"%\";
		progressBar.innerHTML = 100 + \"%\";
		setTimeout(function(){
			parent.window.location.reload(true);
		},500);
	</script>
";

//header("location: add_form_adm.php?new=1");

?>