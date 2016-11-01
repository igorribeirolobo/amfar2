<?
function gerasenha($digito)	{

	$id=md5(uniqid(microtime(), 1)) . getmypid();
	$senha .= $digito + $id; 
	return substr($senha, 0, 6);
	}	// function


include '../global.php';
$sql="select uid, cpf from dbo_cadastro2 where uid <> 810 and uid <> 812";
$query=mssql_query($sql);
?>

<html>
	<head>
		<title>Eventos</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../style.css" rel="stylesheet" type="text/css">
	</head>
	
	<body topmargin="0">
	
	<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td valign="top">
			<?
				while ($res = mssql_fetch_object($query))	{
					$uid= $res->uid;
					$codigo=$uid + 140907;
					$pwd=gerasenha($codigo);
					$cpf = ereg_replace("[' '-./ \t]",'',$res->cpf);
					mssql_query("update cadastro set cpf='$cpf' where uid=$uid");
					echo "<br>senha=$pwd";
					}		// while		
				?>
			</td>
		</tr>
	</table>
	</body>
</html>
