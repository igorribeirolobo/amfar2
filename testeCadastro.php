<?php
include 'global.php';
include 'includes/funcoesUteis.php';


$query=mssql_query("select * from cadastro");
$rsCadastro=mssql_fetch_object($query);
echo "<pre>";
print_r($rsCadastro);
echo "</pre>";

$query=mssql_query("select * from cadastro where email='lab.design@globo.com' and senha='britto'");
$rsCadastro=mssql_fetch_object($query);
echo "<pre>";
print_r($rsCadastro);
echo "</pre>";
?>
