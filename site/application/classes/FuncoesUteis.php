<?php
define("STR_REDUCE_LEFT", 1);
define("STR_REDUCE_RIGHT", 2);
define("STR_REDUCE_CENTER", 4);


class FuncoesUteis {
	
	public function debug($var){
		echo"<pre>";
		print_r($var);
		echo"</pre>";
		}


	public function mime_content_type($f){		
		return trim (exec('file -bi'. escapeshellarg($f)));
		}


	public function mailAttachment($mail, $uri){
		if(!$mail || !$uri) return;
		$type = $this->mime_content_type($uri);		
		$attachment = $mail->getFile($uri);
		$mail->addAttachment($attachment, basename($uri), $type);	
		}



	public function normaliza($string){
		$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
		, "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
		$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
		, "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
		return str_replace( $array1, $array2, $string );
		}


	public function caseto($string, $upper=true){
		$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
		$array2 = array("Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
		if($upper)
			return str_replace($array1, $array2, $string);
		else
			return str_replace($array2, $array1, $string);
		}



	public function checkEmail($email) {
		$email = trim($email);
		if(preg_match("/^([a-zA-z0-9_.-]){3,}@([a-z0-9.-]{3,})(.[a-z]{2,3})(.[a-z]{2})?$/", $email)) {
			return true;
			}
		else {
			return false;
			}
		} 	// function


	public function checkCPF($cpf){
		// Verifiva se o número digitado contém todos os digitos
		$cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
	
		// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
		if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || 
			$cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || 
			$cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999'){
			return false;
			}
		else{
			// Calcula os números para verificar se o CPF é verdadeiro
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf{$c} * (($t + 1) - $c);
					}			
				$d = ((10 * $d) % 11) % 10;			
				if ($cpf{$c} != $d) {
					return false;
					}
				}			
			return true;
			}
		}





	public function dbReader($db, $sql, $all=false){	
		try{
			$query = $db->fetchAll($sql);
			return(($all)?$query : $query[0]);
			}
		catch(Exception $ex){
			echo $ex->getMessage();
			}	
		}


	
	public function msql2dmY($data, $full=false){
		$data = str_replace('  ',' ', $data);
		$meses = array('Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$temp = explode(' ', $data);
		$hora = explode(':', $temp[3]);
		$x = 0; $mes=0;
		//echo "data=$data";
		//$this->debug($temp);
		while($x < count($meses) && $mes==0){
			$mes = ($temp[0] ==  $meses[$x]) ? sprintf('%02d', $x+1) : 0;
			$x++;		
			}
		
		if($full)
			return(sprintf('%02d/%02d/%s %s:%s:%s', $temp[1], $mes, $temp[2], $hora[0], $hora[1], $hora[2]));
		else
			return(sprintf('%02d/%02d/%s', $temp[1], $mes, $temp[2]));
		}



	function diffDate($d1, $d2, $type='', $sep='-'){
		 $d1 = explode($sep, $d1);
		 $d2 = explode($sep, $d2);
		 switch ($type){
		 	case 'A':
		 		$X = 31536000;
		 		break;
		 	case 'M':
		 		$X = 2592000;
		 		break;
		 	case 'D':
		 		$X = 86400;
		 		break;
		 	case 'H':
		 		$X = 3600;
		 		break;
		 	case 'MI':
		 		$X = 60;
		 		break;
		 	default:
		 		$X = 1;
		 		break;
		 	}
		$diff = floor(((mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) - mktime(0, 0, 0, $d1[1], '01', $d1[0]))/$X));
		return $diff;
		}




	public function date2str($data=null, $upper=true, $short=true){
		if(!$data) $data = date('d/m/Y');	
		$_weekday[] = 'Domingo';
		$_weekday[] = 'Segunda';
		$_weekday[] = 'Terça';
		$_weekday[] = 'Quarta';
		$_weekday[] = 'Quinta';
		$_weekday[] = 'Sexta';
		$_weekday[] = 'Sábado';
		
		$_mon[] = null;
		$_mon[] = 'Janeiro';
		$_mon[] = 'Fevereiro';
		$_mon[] = 'Março';
		$_mon[] = 'Abril';
		$_mon[] = 'Maio';
		$_mon[] = 'Junho';
		$_mon[] = 'Julho';
		$_mon[] = 'Agosto';
		$_mon[] = 'Setembro';
		$_mon[] = 'Outubro';
		$_mon[] = 'Novembro';
		$_mon[] = 'Dezembro';
		
		
		$temp = explode('/', $data);
		$w = date("w", mktime(0, 0, 0, $temp[1], $temp[0], $temp[2]));
		$stream = sprintf('%s, %s de %s de %s', 
			$_weekday[$w], $temp[0], $_mon[(int)$temp[1]], $temp[2]);
		return($stream);	
		}



	public function mont2str($data){
		echo "data=$data";
		if(!$data) $mes = (int)date('m');
		elseif(stristr($data, '-')){
			$data = str_replace('-','/',str_replace('.','/',$data));
			$t = explode('/', $data);
			$mes = (int)$t[1];
			}
		
		$_mon[] = null;
		$_mon[] = 'Janeiro';
		$_mon[] = 'Fevereiro';
		$_mon[] = 'Março';
		$_mon[] = 'Abril';
		$_mon[] = 'Maio';
		$_mon[] = 'Junho';
		$_mon[] = 'Julho';
		$_mon[] = 'Agosto';
		$_mon[] = 'Setembro';
		$_mon[] = 'Outubro';
		$_mon[] = 'Novembro';
		$_mon[] = 'Dezembro';

		return($_mon[$mes]);	
		}


	

	public function formataTelefone($source){
		$s = ereg_replace("[' '-()./ \t]",'', $source);
		while($s[0]=='0'){
			$s = substr($s, 1, strlen($s)-1);
			}
		if(strlen($s)==8)
			return(sprintf('%s-%s', substr($s, 0, 4), substr($s, -4))); 
		elseif(strlen($s)==10)
			return(sprintf('%s %s-%s', substr($s, 0, 2), substr($s, 2, 4), substr($s, -4)));
		elseif($s[0]=='-')
			return(null);
		else
			return($s);
		}


	public function dmY2msSql($ddmmYYYY){
		// se existe ' ' é formato data hora
		$t = explode('/', $ddmmYYYY);
		return(sprintf("%s.%s.%s", $t[1], $t[0], $t[2]));		
		}



	public function Ymd2dmY($source, $full=false){
		// se existe ' ' é formato data hora
		$t = explode(' ', $source);
		$data = implode('/', array_reverse(explode("-", $t[0])));
		$hora = $t[1];
		if(count($t)==1)
			return($data);
				
		return($full)? sprintf("%s %s", $data, $hora) : $data;		
		}

	public function dmY2Ymd($source, $full=false){
		// se existe ' ' é formato data hora
		//die("source = $source");
		$t = explode(' ', $source);
		$data = implode('-', array_reverse(explode("/", $t[0])));
		$hora = $t[1];
		$t2 = explode('/', $t[0]);			
		if(!checkdate($t2[1],$t2[0],$t2[2]))
			return '0000-00-00';
		
		if(count($t)==1)
			return($data);
				
		return($full)? sprintf("%s %s", $data, $hora) : $data;		
		}


	public function dmy2time($ddmmyyyy){
		$t=explode('/',$ddmmyyyy);
		return (strtotime("$t[1]/$t[0]/$t[2]"));
		}
			

	public function formatCEP($source){
		$s = sprintf('%08d', ereg_replace("([^0-9])",'',$source));		
		return(substr($s, 0, 5).'-'.substr($s, -3));
		}


	public function formatCNPJ($source){
		$temp=null;
		$s = ereg_replace("([^0-9])",'',$source);
		if (strlen($s)==11){	// o numero e CPF
			$temp = $this->formatCPF($source);
			}	
		elseif(strlen($s)==14){	
			$temp = substr($s,0,2) . '.';
			$temp .= substr($s,2,3) . '.';
			$temp .= substr($s,5,3) . '/';
			$temp .= substr($s,8,4) . '-';
			$temp .= substr($s,-2);
			}		
		return($temp);
		}


	public function formatCPF($source){
		$temp=null;
		$s = ereg_replace("([^0-9])",'',$source);
		if (strlen($s)==11){	// o numero e CPF
			$temp = substr($s,0,3) . '.';
			$temp .= substr($s,3,3) . '.';
			$temp .= substr($s,6,3) . '-';
			$temp .= substr($s,-2);
			}	
		elseif (strlen($s)==14){	
			$temp = $this->formatCNPJ($source);
			}	
		return($temp);
		}
		
		
	public function maxDay($m){
		$_im = (int)$m;
		$_y = date('Y');
		$maxday = 31;
		if($_im==4||$_im==6||$_im==9||$_im==11)
			$maxday = 30;
		elseif($_im==2){
			$maxday = ($_y % 4 == 0) ? 29 : 28;
			}
		return($maxday);		
		}



	
	/*
	*    function str_reduce (str $str, int $max_length [, str $append [, int $position [, bool $remove_extra_spaces ]]])
	*
	*    @return string
	*
	*    Reduz uma string sem cortar palavras ao meio. Pode-se reduzir a string pela
	*    extremidade direita (padrão da função), esquerda, ambas ou pelo centro. Por
	*    padrão, serão adicionados três pontos (...) à parte reduzida da string, mas
	*    pode-se configurar isto através do parâmetro $append.
	*    Mantenha os créditos da função.
	*
	*    @autor: Carlos Reche
	*    @data:  Jan 21, 2005
	*/
	public function str_reduce($str, $max_length, $append = NULL, $position = STR_REDUCE_RIGHT, $remove_extra_spaces = true){
	    if (!is_string($str)){
	       echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 1 to be string.";
	        return false;
	    }
	    else if (!is_int($max_length))
	    {
	        echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects parameter 2 to be integer.";
	        return false;
	    }
	    else if (!is_string($append)  &&  $append !== NULL)
	    {
	        echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 3 to be string.";
	        return false;
	    }
	    else if (!is_int($position))
	    {
	        echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "() expects optional parameter 4 to be integer.";
	        return false;
	    }
	    else if (($position != STR_REDUCE_LEFT)  &&  ($position != STR_REDUCE_RIGHT)  &&
	             ($position != STR_REDUCE_CENTER)  &&  ($position != (STR_REDUCE_LEFT | STR_REDUCE_RIGHT)))
	    {
	        echo "<br /><strong>Warning</strong>: " . __FUNCTION__ . "(): The specified parameter '" . $position . "' is invalid.";
	        return false;
	    }
	
	
	    if ($append === NULL)
	    {
	        $append = "...";
	    }
	
	
	    $str = html_entity_decode($str);
	
	
	    if ((bool)$remove_extra_spaces)
	    {
	        $str = preg_replace("/\s+/s", " ", trim($str));
	    }
	
	
	    if (strlen($str) <= $max_length)
	    {
	        return htmlentities($str);
	    }
	
	
	    if ($position == STR_REDUCE_LEFT)
	    {
	        $str_reduced = preg_replace("/^.*?(\s.{0," . $max_length . "})$/s", "\\1", $str);
	
	        while ((strlen($str_reduced) + strlen($append)) > $max_length)
	        {
	            $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
	        }
	
	        $str_reduced = $append . $str_reduced;
	    }
	
	
	    else if ($position == STR_REDUCE_RIGHT)
	    {
	        $str_reduced = preg_replace("/^(.{0," . $max_length . "}\s).*?$/s", "\\1", $str);
	
	        while ((strlen($str_reduced) + strlen($append)) > $max_length)
	        {
	            $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);
	        }
	
	        $str_reduced .= $append;
	    }
	
	
	    else if ($position == (STR_REDUCE_LEFT | STR_REDUCE_RIGHT))
	    {
	        $offset = ceil((strlen($str) - $max_length) / 2);
	
	        $str_reduced = preg_replace("/^.{0," . $offset . "}|.{0," . $offset . "}$/s", "", $str);
	        $str_reduced = preg_replace("/^[^\s]+|[^\s]+$/s", "", $str_reduced);
	
	        while ((strlen($str_reduced) + (2 * strlen($append))) > $max_length)
	        {
	            $str_reduced = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $str_reduced);
	
	            if ((strlen($str_reduced) + (2 * strlen($append))) > $max_length)
	            {
	                $str_reduced = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $str_reduced);
	            }
	        }
	
	        $str_reduced = $append . $str_reduced . $append;
	    }
	
	
	    else if ($position == STR_REDUCE_CENTER)
	    {
	        $pattern = "/^(.{0," . floor($max_length / 2) . "}\s)|(\s.{0," . floor($max_length / 2) . "})$/s";
	
	        preg_match_all($pattern, $str, $matches);
	
	        $begin_chunk = $matches[0][0];
	        $end_chunk   = $matches[0][1];
	
	        while ((strlen($begin_chunk) + strlen($append) + strlen($end_chunk)) > $max_length)
	        {
	            $end_chunk = preg_replace("/^\s?[^\s]+(\s.*)$/s", "\\1", $end_chunk);
	
	            if ((strlen($begin_chunk) + strlen($append) + strlen($end_chunk)) > $max_length)
	            {
	                $begin_chunk = preg_replace("/^(.*?\s)[^\s]+\s?$/s", "\\1", $begin_chunk);
	            }
	        }
	
	        $str_reduced = $begin_chunk . $append . $end_chunk;
	    }
	
	
	    return htmlentities($str_reduced);
	}
	
	
	public function ufWords($string) {
	
		$uSpecialChars = array('Á','Ã','À','É','Ê','Í','Ç','Ó','Ô','Õ','Ú',' De ',' A ', ' E ', 'R$');
		$lSpecialChars = array('á','ã','à','é','ê','í','ç','ó','ô','õ','ú',' de ',' a ', ' e ', 'R$');
	
		$nString = ucwords(mb_strtolower($string));
		$nString = str_replace($uSpecialChars, $lSpecialChars, $nString);
		return $nString;
		}
	
	public function ucString($string) {
		$uSpecialChars = array('Á','Ã','À','É','Ê','Í','Ç','Ó','Ô','Õ','Ú',' De ',' A ', ' E ', 'R$');
		$lSpecialChars = array('á','ã','à','é','ê','í','ç','ó','ô','õ','ú',' de ',' a ', ' e ', 'R$');	
		$uPreposicoes = array(' De ', ' Da ', ' Do ', ' Na ', ' No ', ' À ', ' A ', ' E ', ' Em ', ' Ii ',' Iii ', ' Iv ', ' Vii ');
		$lPreposicoes = array(' de ', ' da ', ' do ', ' na ', ' no ', ' à ', ' a ', ' e ', ' em ', ' II', ' III ', ' IV ', ' VII ');
		
		$nString = strtolower($string);
		$nString = str_replace($uSpecialChars, $lSpecialChars, $nString);
		$nString = ucWords($nString);
		$nString = str_replace($uPreposicoes, $lPreposicoes, $nString);	
		return $nString;
		}



	
	private function checkgd(){ 
		$gd2=""; 
		ob_start(); 
		phpinfo(8);
		$phpinfo=ob_get_contents(); 
		ob_end_clean(); 
		$phpinfo=strip_tags($phpinfo); 
		$phpinfo=stristr($phpinfo,"gd version"); 
		$phpinfo=stristr($phpinfo,"version"); 
		preg_match('/\d/',$phpinfo,$gd); 
		if ($gd[0]=='2') {
			$gd2="yes";
			} 
		return $gd2; 
		}
	
	public function createthumb($name, $new_w, $new_h, $path) {
		
		ini_set("allow_url_fopen", 1);
		$gd2 = $this->checkgd();
		$system=explode(".",basename($name));
		if (preg_match("/jpg|jpeg/",$system[1])) {
			$src_img=imagecreatefromjpeg($name); } 
			
		if (preg_match("/png/",$system[1])) {
			$src_img=imagecreatefrompng($name); } 
			
		if (preg_match("/gif/",$system[1])) { 
			$src_img=imagecreatefromgif($name); }
	
		$old_x = imageSX($src_img); 
		$old_y = imageSY($src_img);
		
		//echo "name=$name, new_w=$new_w, new_h=$new_h, path=$path old_x=$old_x old_y=$old_y ";
	  
		if ($old_x > $old_y){ 
			$thumb_w = $new_w; 
			$thumb_h = round($old_y * ($new_w/$old_x));
			}	 
	 	elseif ($old_x < $old_y){ 
			$thumb_h = $new_h; 
			$thumb_w = round($old_x * ($new_h/$old_y)); 
	 		}	 
		else{ 
			$thumb_w = $new_w; 
			$thumb_h = $new_h;
			}
		//echo "name=$name, new_w=$new_w, new_h=$new_h, path=$path [old_x=$old_x old_y=$old_y] [thumb_w=$thumb_w thumb_h=$thumb_h]";	
			
			
		// verifica se largura maior que solicitada
		if ($thumb_w > $new_w){			 
			$thumb_h = round($thumb_h * ($new_w/$thumb_w));
			$thumb_w = $new_w;
			} 		
		if ($thumb_h > $new_h){			 
			$thumb_w = round($thumb_w * ($new_h/$thumb_h));
			$thumb_h = $new_h;
			} 	

		//echo "thumb_w = $thumb_w thumb_h=$thumb_h ";	 
	 
		if ($gd2=="") {
			$dst_img=ImageCreate($thumb_w,$thumb_h); 
			imagecopyresized($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
			}
		else { 
			$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h); 
			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
			} 
	 
		header('Content-type: image/jpeg');
		if (preg_match("/png/",$system[1]))
			imagepng($dst_img, $path); 
		elseif (preg_match("/gif/",$system[1]))
			imageGif($dst_img, $path); 
		else
			imagejpeg($dst_img);
	 
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
		}
	
	
	public function valorPorExtenso($valor=0) {
		$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
		$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
	"quatrilhões");
	
		$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
	"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
	"sessenta", "setenta", "oitenta", "noventa");
		$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
	"dezesseis", "dezesete", "dezoito", "dezenove");
		$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
	"sete", "oito", "nove");
	
		$z=0;
	
		$valor = number_format($valor, 2, ".", ".");
		$inteiro = explode(".", $valor);
		for($i=0;$i<count($inteiro);$i++)
			for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
				$inteiro[$i] = "0".$inteiro[$i];
	
		// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
		$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
		for ($i=0;$i<count($inteiro);$i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
		
			$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
			$t = count($inteiro)-1-$i;
			$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
			if ($valor == "000")$z++; elseif ($z > 0) $z--;
			if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
			if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? " e " : ", ") : " ") . $r;
		}
	
		return($rt ? $rt : "zero");
	}
	
	
	
	public function exParcelas($i=1) {
		$p[1] = "uma";
		$p[2] = "duas";
		$p[3] = "tres";
		$p[4] = "quatro";
		$p[5] = "cinco";
		$p[6] = "seis";
		$p[7] = "sete";
		$p[8] = "oito";
		$p[9] = "nove";
		$p[10] = "dez";
		$p[11] = "onze";
		$p[12] = "doze";
		$p[13] = "treze";
		$p[14] = "quatorze";
		$p[15] = "quinze";
		$p[16] = "dezesseis";
		$p[17] = "dezessete";
		$p[18] = "dezoito";
		$p[19] = "dezenove";
		$p[20] = "vinte";
		return($p[(int)$i]);
	}

	}
?>
