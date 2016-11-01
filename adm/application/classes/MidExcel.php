<?php
/*
Creditos:
- DzaiaCuck
- dzaiacuck@ig.com.br
- Desenvolvedor

Description: (lang = pt_br "Brasil")

Esta classe le(todos) os campos da uma *tabela gerando automaticamente
um arquivo excel com os dados da *mesma, alinhando/separando por colunas, mantendo
a integridade do banco/tabela, geralmente uso para BKPs e/ou simplesmente trabalhar os dados
no excel.


Importante lembrar que possui como base a classe "EXCEL GEN" que gera arquivos do excel
com base em dados fornecidos tabularmente


Licença:
Usa o bagulho...
Use the class without problem... is free




######### EXEMPLO/EXAMPLE (FILE exemplo.php content)
<?
# Brasil ***************************************

//params of the your server-dataBase
// parametros do seu servidor de banco
require("db_config.inc");

// classe (class)
require("mid_excel.class");

//Estanciar
$mid_excel = new MID_SQLPARAExel;

// data to the file(Dados para o arquivo)
$sql = "select * from alunos";

//ex.:
//$mid_excel->mid_sqlparaexcel("DataBaseName", "TABLEname", RECORDSET, "FILEname");
$mid_excel->mid_sqlparaexcel("ESCOLA", "alunos", $sql, "arquivo_alunos");
?>
*/


class MidExcel extends GeraExcel
{
	#Variaveis da classe GeraEcel
	var $armazena_dados; // Armazena dados
	var $filename; // Nome para o arquivo excel
	var $savefile = false; // Nome para o arquivo excel
	var $baseUrl;
	var $tempfile;

	function debug($var){
		echo"<pre>";
		print_r($var);
		echo"</pre>";
		}
	
	// define parametros(init)
	function mid_sqlparaexcel($baseUrl, $array, $filename, $save=false){	
		// define nome do arquivo
		$this->filename = sprintf("MID_%s_%s.xls", date("YmdHis"), trim($filename));
		$this->baseUrl = $baseUrl;
		$this->savefile = $save;
		//die("baseUrl=$baseUrl");		
		$col = 0;
		//$this->debug($array); die();
		foreach ($array[0] as $k => $v):
			$colname = strtolower($k);
		   //echo "$colname => $v.<br />";
		   $this->MontaConteudo(0, $col, $colname);
		   $col++;
		endforeach;
		// linha em branco
		//$this->MontaConteudo(1, 0, " ");
		//$this->MontaConteudo(1, 1, " ");		
	
		// quadro 1, primeira linha, da primeira coluna X/Y
		$row = 1;
		foreach ($array as $rs):
			$col = 0;
			foreach ($rs as $key => $val):
				$this->MontaConteudo($row, $col, $val); // write to the collumn
				$col++; // point to next collumn
			endforeach;
			
			$row++; // point to next row
		endforeach;
		
		$this->GeraArquivo();	// Cria arquivo	
		}// close function
	}// class end
	



	
	
// Gera EXCEL
class GeraExcel{	
	// define parametros(init)
	function GeraExcel(){	
		$this->armazena_dados = ""; // Armazena dados para imprimir(temporario)
		$this->filename = $filename; // Nome do arquivo excel
		$this->ExcelStart();
		}// fim constructor
	
	
	// Monta cabecario do arquivo(tipo xls)
	function ExcelStart(){	
		//inicio do cabecario do arquivo
		$this->armazena_dados = pack( "vvvvvv", 0x809, 0x08, 0x00,0x10, 0x0, 0x0 );
		}
	
	// Fim do arquivo excel
	function FechaArquivo(){
		$this->armazena_dados .= pack( "vv", 0x0A, 0x00);
		}
	
	
	// monta conteudo
	function MontaConteudo( $xlsLine, $xlsCol, $value){	
		$tamanho = strlen( $value );
		$this->armazena_dados .= pack( "v*", 0x0204, 8 + $tamanho, $xlsLine, $xlsCol, 0x00, $tamanho );
		$this->armazena_dados .= $value;
		}//Fim, monta Col/Lin
	
	// Gera arquivo(xls)
	function GeraArquivo(){	
		//Fecha arquivo(xls)
		$this->FechaArquivo();
		if(!$this->savefile){	
			header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT");
			header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
			header ( "Pragma: no-cache" );
			header ( "Content-type: application/octet-stream; name=$this->filename");
			header ( "Content-Disposition: attachment; filename=$this->filename");
			header ( "Content-Description: MID Gera excel" );
			print ( $this->armazena_dados);
			}
		else{
			$this->tempfile = sprintf("..%s/public/temp/%s", $this->baseUrl,  $this->filename); //die($tempfile);
			$fpLog = fopen($this->tempfile, "w");
			fwrite($fpLog, $this->armazena_dados);
			fclose($fpLog);
			//return $tempfile;		
			}	
		}// fecha funcao
	# Fim da classe que gera excel
	}
?>
