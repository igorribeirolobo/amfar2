<?php
include '../global.php';
include '../includes/funcoesUteis.php';
$query=mssql_query("select * from cursos_2");
//$res=mssql_fetch_object($query);
//debug($res);
while ($res=mssql_fetch_object($query)) {
	$sql="insert into cursos(uid, inicio, final, titulo, cargaHoraria, local,
		realizacao, informacoes, email, url, valor, parcelas, dataMax, status) values(
		0, '$res->inicio', '$res->final', '$res->titulo',$res->cargaHoraria,
		'$res->local','$res->realizacao','$res->informacoes','$res->email','$res->url',
		$res->valor, $res->parcelas, '$res->dataMax',$res->status, $res->ativo)";
	echo $sql;
	mssql_query($sql);

	}
?>
