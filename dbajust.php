<?php
	function debug($var){
		echo"<pre>";
		print_r($var);
		echo"</pre>";	
		}
	
	$vcto = array();
	$ano = 2012;
	$mes = 2;
	for($x=1; $x < 20; $x++){
		$mes++;
		if ($mes == 13){
			$ano = 2013;
			$mes = 1;
			}
		$vcto[$x] = sprintf('%02d.15.%d', $mes, $ano);	
		}
		
	debug($vcto);
	include 'global.php';
	//die('cheguei');
	$query = mssql_query("SELECT idFinanceiro, idCadastro, idCurso, numdoc, CONVERT(CHAR(10), vcto, 102) AS dvcto , CONVERT(CHAR(10), pgto, 102) AS dpgto FROM
		financeiro WHERE (idCurso in(144,145,146)) ORDER BY idCadastro, vcto");
	while ($rs = mssql_fetch_object($query)){
		$parcela = (int)substr($rs->numdoc, -2);
		debug($rs);
		if($parcela > 1 || $rs->dpgto=='1900.01.01') {
			$sql = "UPDATE financeiro SET vcto = '{$vcto[$parcela]}' WHERE idFinanceiro=$rs->idFinanceiro";
			echo $sql;
			mssql_query($sql);
			}
		}
?>
