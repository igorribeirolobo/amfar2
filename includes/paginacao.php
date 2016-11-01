<?
include "topo.php";

//Limite por pagina
$limite = 3;

//Pagina, se nao tiver vindo nenhuma, pagina = 1
if(!isset($pagina))
{
    $pagina = 1;
}

//
$quantidade = $pagina*$limite-$limite;

//Busca
$sql = "SELECT TOP $limite * FROM HTLN_INTERACAO_ WHERE (cdINTERACAO NOT IN (SELECT TOP $quantidade cdINTERACAO FROM HTLN_INTERACAO_ ))";

$exe = mssql_query($sql);
    while($row = mssql_fetch_array($exe))
    {
        echo $row["RESPOSTA"];
        echo "<br>";
        echo "<br>";
    }
$next = $pagina + 1;
$prev = $pagina - 1;

//Proximas paginas e anteriores
echo "<a href=?pagina=$prev>prev</a>";    
echo " | ";
echo "<a href=?pagina=$next>next</a>";

?>