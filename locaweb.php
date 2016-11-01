<?
        // idenficação da empresa
        $host='200.234.197.42'; // host ou ip do mssql

        $userDB='amfar_1';      // login do mssql

        $pwdDB='eofilho172298'; // senha do mssql

        $dataBase='amfar_1';    // base de dados

$link = mssql_connect($host,$userDB, $pwdDB);
mssql_select_db($dataBase, $link);
mssql_query("insert into locateste values('çaõãaáóí')", $link);
$sql = "Select * from locateste";
$rs = mssql_query($sql, $link);
for ($i = 0; $i < mssql_num_rows( $rs ); ++$i)
     {
         $line = mssql_fetch_row($rs);
         print( "<br>$line[0] - $line[1]\n");
     }

mssql_close($link);

?>

