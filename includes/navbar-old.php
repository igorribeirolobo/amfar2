<?php
/*
	A classe navbar de Copyright Joao Prado Maia (jpm@phpbrasil.com) e tradu��o de
	Thomas Gonzalez Miranda (thomasgm@hotmail.com) baixada do site www.phpbrasil.com
	em 06/05/2002 foi modificada para melhor entendimento do seu funcionamento e
	aperfei�oada deste que apareceram alguns "bugs", sendo transformada como classe
	Mult_Pag (Multiplas paginas).
	As informa��es acima foram retiradas da vers�o 1.3 da classe navbar do arquivo
	navbar.zip.
	Adapta��o realizada por Marco A. D. Freitas (madf@splicenet.com.br) entre
	06 e 09/05/2002.
	Altera��es realizadas por Gustavo Villa (php@sitework.com.br) entre
	em 30/01/2004, com a inser��o da funcionalidade de n�o exibir o menu de navega��o
	caso seja necess�ria apenas 1 p�gina e permitir o acesso � quantidade total de p�ginas
	
	**********************************************************************************************
	Altera��es abaixo realizadas por Lauro A L Brito (lab.design@globo.com) inserindo
	a funcionalidade de acesso no retorno porque: image ter 90 p�ginas de registros contendo cada
	p�gina 20 registros. O avan�o tudo bem, ele fazia com que o primeiro link fosse o �ltimo + 1
	mas o retorno ja complicava porque se tive na pagina 80 por exemplo, teria que clicar 79 vezes
	para voltar � primeira p�gina, portanto dessa forma, o link anterior retorna o n�mero de posi��es
	informadas no $maxLinks e portanto estando o usuario na pagina 80, e o $maxLinks fosse definido
	como 10, bastaria 8 clicks que j� estaria na primeira p�gina novamente.
	Claro que cada um tem uma vis�o e uma solu��o diferente.
	
	dessa forma a estrutura ficou assim:
	anterior 1 2 3 4 5 6 7 8 9 10 pr�xima
	onde: anterior n�o tem link e proxima aponta para a p�gina 11
	
	anterior 11 12 13 14 15 16 17 18 19 20 pr�xima
	onde: anterior aponta para pagina 10 e proxima aponta para 21
	
	Outro detalhe: Eu embuti algumas variaveis para facilitar o uso dessa class:
	$maxLinks que antes deveria ser passada da chamada onde foi incluida a class
	
	Tudo que precisa ser feito agora �:
	
	Na pagina de inclus�o:	
		$conn = mssql_connect($host,$userDB, $pwdDB);	// cria a conex�o com o mysql
		mssql_select_db($dataBase);							// seleciona a base de dados
		
	require 'navbar.php';										// inclui o class
	$mult_pag =new Mult_Pag();									// cria o objeto class	
	$mult_pag->pageSize=18;								// define o m�ximo de registros por pagina
	$mult_pag->maxLinks=10;									// define o m�ximo de links a mostrar na barra de navega��o
	
	// cria a sql para a pesquisa no banco de dados
	$sql="select distinct(cnpj), empresa, contato from pf_pedidos where dst={$_SESSION['dst']} order by empresa";
	
	// chama o class para acessar a base de dados
	$rsQuery=$mult_pag->executar($sql, $conn,"otimizada","mysql") or die("erro $sql" . mssql_error());
	
	// numRegs informa o total de registros lidos ou 0 se n�o achou
	$numRegs=mssql_numrows($rsQuery);
	
	if($numRegs==0)	{
		echo "<p align=center>Nenhum registro localizado !</p>";
			exit;
			}
	
	$counter = 0;
	while ($counter < $numRegs) {
		$result=mssql_fetch_object($rsQuery);
		$empresa=substr($result->empresa, 0, 50);
		// cria a linha para envio pra tela
		}	// fecha o loop while
		
	// acrescente essa linha no final da p�gina para mostrar a barra de navega��o
	<? $links = $mult_pag->Construir_Links(); ?>

	***********************************************************************************************

	Construi esta pequena classe para navega��o din�mica de links. Observe
	por favor a simplicidade deste c�digo. Este c�digo � livre em
	toda maneira que voc� puder imaginar. Se voc� o usar em seu
	pr�prio script, por favor deixe os cr�ditos como est�o. Tamb�m,
	envie-me um e-mail se voc� o fizer, isto me deixa feliz :-)
*/

// classe que multiplica paginas
class Mult_Pag {
	// Valores padr�o para a navega��o dos links
	var $filename;	// nome da url
	var $prev = "<img src='images/leftArrow.png' border='0' align='absMiddle'/>";
	var $next = "<img src='images/rightArrow.png' border='0' align='absMiddle'/>";

	var $numRows;	// total de registros na pesquisa	
	var $pageSize;	// registro por pagina
	var $numPages;	// total de paginas
	var $currPage;	// p�gina atual
	var $numreg;	// registros retornados
	var $showLinks;
	var $maxLinks;	// m�ximo de links para a barra de navega��o
	
	// Vari�veis usadas internamente

	/*
		Metodo construtor. Isto � somente usado para setar
		o n�mero atual de colunas e outros m�todos que
		podem ser re-usados mais tarde.
	*/
	function Mult_Pag ($maxRows, $maxLink) {
		global $pageNum;
		$this->currPage = $pageNum ? $pageNum : 1;
		$this->pageSize=$maxRows;
		$this->maxLinks=$maxLink;
		}




	/*
		O pr�ximo m�todo roda o que � necess�rio para as queries.
		� preciso rod�-lo para que ele pegue o total
		de colunas retornadas, e em segundo para pegar o total de
		links limitados.

		$sql par�metro:
		. o par�metro atual da query que ser� executada

		$conexao par�metro:
		. a liga��o da conex�o do banco de dados

		$tipo par�metro:
		. "mysql" - usa fun��es php mysql
		. "pgsql" - usa fun��es pgsql php
	*/	
	function Executar($sql, $conexao) {
		// variavel para o inicio das pesquisas
		
		if (isset($_GET['pageNum'])) $this->currPage=$_GET['pageNum'];
		
		$resultado = mssql_query($sql, $conexao);
		$this->numRows = mssql_num_rows($resultado);	// total de registros da pesquisa inteira

			
		$this->numPages=ceil($this->numRows/$this->pageSize);

		$offset=($this->currPage-1) * $this->pageSize; // offset para seek = pagina atual * pagesize
		
		if ($offset + $this->pageSize > $this->numRows)			
			$this->numreg=$this->numRows-$offset;
		else
			$this->numreg=$this->pageSize;
		
		if ($this->numRows > 0)
			mssql_data_seek($resultado, $offset);			
    	return ($resultado);    	
		}





	/*
		Este m�todo cria uma string que ir� ser adicionada �
		url dos links de navega��o. Isto � especialmente importante
		para criar links din�micos, ent�o se voc� quiser adicionar
		op��es adicionais � estas queries, a classe de navega��o
		ir� adicionar automaticamente aos links de navega��o
		din�micos.
	*/
	function Construir_Url() {
		global $REQUEST_URI, $REQUEST_METHOD, $HTTP_GET_VARS, $HTTP_POST_VARS;

		// separa o link em 2 strings
		@list($this->filename, $voided) = @explode("?", $REQUEST_URI);
		
		if ($REQUEST_METHOD == "GET")
			$cgi = $HTTP_GET_VARS;
		else
			$cgi = $HTTP_POST_VARS;

		reset($cgi); // posiciona no inicio do array

		// separa a coluna com o seu respectivo valor
		while (list($chave, $valor) = each($cgi))			
			if ($chave != "pageNum")
				$query_string .= "&" . $chave . "=" . $valor;
		return $query_string;
		}





	/*
		Este m�todo cria uma liga��o de todos os links da barra de
		navega��o. Isto � �til, pois � totalmente independete do layout
		ou design da p�gina. Este m�todo retorna a liga��o dos links
		chamados no script php, sendo assim, voc� pode criar links de
		navega��o com o conte�do atual da p�gina.

			$opcao par�metro:
			 . "todos" - retorna todos os links de navega��o
			 . "numeracao" - retorna apenas p�ginas com links numerados
			 . "strings" - retornar somente os links 'Pr�xima' e/ou 'Anterior'

			$mostra_string par�metro:
			 . "nao" - mostra 'Pr�xima' ou 'Anterior' apenas quando for necess�rios
			 . "sim" - mostra 'Pr�xima' ou 'Anterior' de qualqur maneira
	*/
  function Construir_Links() {
     	$extra_vars = $this->Construir_Url();
     	
     	$num_mult_pag = ceil($this->numRows / $this->pageSize); // numero de multiplas paginas
     	     	
     	$inicio = floor(($this->currPage-1) / $this->maxLinks) * $this->maxLinks;
     	
	
  		$lnk=$inicio - $this->maxLinks+1;	// deve apontar para o primeiro elemento no link menos total de links
  		
  		// link voltar pagina��o
  		if ($lnk > 0)
  			$array[0] = "<a href='$this->filename?pageNum=$lnk$extra_vars'/>";
  			
  		$array[0] .= "{$this->prev}</a>";
		
		
		if ($num_mult_pag <= $this->maxLinks) {
			$max=$num_mult_pag;			
			}
		elseif ($inicio + $this->maxLinks > $num_mult_pag) {
			$max=$num_mult_pag-$inicio;
			}
		else {
			$max=$this->maxLinks;			
			}
		
		//pagina��o central
     	for ($atual=1; $atual <= $max; $atual++) {
  			// escreve a numeracao (1 2 3 ...)  			
  			$lnk=$inicio + $atual;  			
  			if ($lnk!=$this->currPage)
  				$array[$atual]= "<a href='$this->filename?pageNum=$lnk$extra_vars'/>";
  			$array[$atual] .= "$lnk</a>";
  			}
  		
  	//	debug ($array);
  		// o valor de $atual deve apontar para a proxima posi��o ou seja maxLinks +1
  			
  		// pr�xima pagina��o
  		$lnk++;
  		if ($lnk < $num_mult_pag-1)
  			$array[$atual] = "<a href='$this->filename?pageNum=$lnk$extra_vars'/>{$this->next}</a>";
  		else
  			$array[$atual] = $this->next;
  		
  		$this->numPages = count($array);		//	Faz a contagem da quantidade de p�ginas que ser�o utilizadas
  		 		
  		if ($this->numPages != 3) {			//	Caso a quantidade de p�ginas seja 1, o menu de navega��o n�o � exibido
  			for ($n = 0; $n < $this->numPages; $n++) {			
				echo $array[$n] . "&nbsp;&nbsp;&nbsp;";
				}
			echo " [$num_mult_pag pgs.]";
  			}
  		else
  			$this->showLinks = false;
		}	
  
	}	// fim da class
?>
