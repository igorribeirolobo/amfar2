<?
include 'global.php';
// cria a conexao com a base de dados
$conn = mysql_connect($host,$userDB,$pwdDB) or die("Falha de Conexão - Por favor tente mais tarde !");
// se houver falha na conexao ao servidor de database, encerra.

mysql_select_db($dataBase) or die("Falha na Base de Dados - Por favor, informe ao <a href=mailto:$webmaster>webmaster</a> !");
// se houver falha na selecao da database, encerra.

$counter=0;
$query=mysql_query("select * from sbrafh_associados where email <> ''", $conn);
while ($res=mysql_fetch_array($query))	{

	$sql="insert into mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
	
echo "inserido $counter de Tab. Associados<br>";
$counter=0;
$query=mysql_query("select * from sbrafh_contato", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
echo "inserido $counter de Tab. contatos<br>";

$counter=0;
$query=mysql_query("select * from sbrafh_forum where email <> ''", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
echo "inserido $counter de Tab. forum<br>";

$counter=0;
$query=mysql_query("select * from sbrafh_guestbook where email <> ''", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
echo "inserido $counter de Tab. GuestBook<br>";

$counter=0;
$query=mysql_query("select * from sbrafh_insCongresso", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
echo "inserido $counter de Tab.  Congresso<br>";

$counter=0;
$query=mysql_query("select * from sbrafh_inscr_simposio", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";

	}
echo "inserido $counter de Tab. Simposio<br>";

$counter=0;
$query=mysql_query("select * from sbrafh_inscricoes", $conn);
while ($res=mysql_fetch_array($query))	{	
	$sql="insert into sbrafh_mailing(nome, email, dataReg) values('{$res['nome']}', '{$res['email']}', now())";	
	
	$query2=mysql_query($sql, $conn);
	if (!mysql_error()) {
		$counter++;
		echo "$sql<br>";
		}
	else echo mysql_error() . "<br>";
	}
echo "inserido $counter de Tab. Inscricoes<br>";

	@mysql_free_result($query);
	mysql_close($conn);
?>