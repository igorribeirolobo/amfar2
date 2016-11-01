<?php
/*
	A classe navbar de Copyright Joao Prado Maia (jpm@phpbrasil.com) e tradução de
	Thomas Gonzalez Miranda (thomasgm@hotmail.com) baixada do site www.phpbrasil.com
	em 06/05/2002 foi modificada para melhor entendimento do seu funcionamento e
	aperfeiçoada deste que apareceram alguns "bugs", sendo transformada como classe
	Mult_Pag (Multiplas paginas).
	As informações acima foram retiradas da versão 1.3 da classe navbar do arquivo
	navbar.zip.
	Adaptação realizada por Marco A. D. Freitas (madf@splicenet.com.br) entre
	06 e 09/05/2002.
	Alterações realizadas por Gustavo Villa (php@sitework.com.br) entre
	em 30/01/2004, com a inserção da funcionalidade de não exibir o menu de navegação
	caso seja necessária apenas 1 página e permitir o acesso à quantidade total de páginas
	
	**********************************************************************************************
	Alterações abaixo realizadas por Lauro A L Brito (lab.design@globo.com) inserindo
	a funcionalidade de acesso no retorno porque: image ter 90 páginas de registros contendo cada
	página 20 registros. O avanço tudo bem, ele fazia com que o primeiro link fosse o último + 1
	mas o retorno ja complicava porque se tive na pagina 80 por exemplo, teria que clicar 79 vezes
	para voltar à primeira página, portanto dessa forma, o link anterior retorna o número de posições
	informadas no $max_links e portanto estando o usuario na pagina 80, e o $max_links fosse definido
	como 10, bastaria 8 clicks que já estaria na primeira página novamente.
	Claro que cada um tem uma visão e uma solução diferente.
	
	dessa forma a estrutura ficou assim:
	anterior 1 2 3 4 5 6 7 8 9 10 próxima
	onde: anterior não tem link e proxima aponta para a página 11
	
	anterior 11 12 13 14 15 16 17 18 19 20 próxima
	onde: anterior aponta para pagina 10 e proxima aponta para 21
	
	Outro detalhe: Eu embuti algumas variaveis para facilitar o uso dessa class:
	$max_links que antes deveria ser passada da chamada onde foi incluida a class
	
	Tudo que precisa ser feito agora é:
	
	Na pagina de inclusão:	
		$conn = mysql_connect($host,$userDB, $pwdDB);	// cria a conexão com o mysql
		mysql_select_db($dataBase);							// seleciona a base de dados
		
	require 'navbar.php';										// inclui o class
	$mult_pag =new Mult_Pag();									// cria o objeto class	
	$mult_pag->num_pesq_pag=18;								// define o máximo de registros por pagina
	$mult_pag->max_links=10;									// define o máximo de links a mostrar na barra de navegação
	
	// cria a sql para a pesquisa no banco de dados
	$sql="select distinct(cnpj), empresa, contato from pf_pedidos where dst={$_SESSION['dst']} order by empresa";
	
	// chama o class para acessar a base de dados
	$rsQuery=$mult_pag->executar($sql, $conn,"otimizada","mysql") or die("erro $sql" . mysql_error());
	
	// numRegs informa o total de registros lidos ou 0 se não achou
	$numRegs=mysql_numrows($rsQuery);
	
	if($numRegs==0)	{
		echo "<p align=center>Nenhum registro localizado !</p>";
			exit;
			}
	
	$counter = 0;
	while ($counter < $numRegs) {
		$result=mysql_fetch_object($rsQuery);
		$empresa=substr($result->empresa, 0, 50);
		// cria a linha para envio pra tela
		}	// fecha o loop while
		
	// acrescente essa linha no final da página para mostrar a barra de navegação
	<? $links = $mult_pag->Construir_Links(); ?>

	***********************************************************************************************

	Construi esta pequena classe para navegação dinâmica de links. Observe
	por favor a simplicidade deste código. Este código é livre em
	toda maneira que você puder imaginar. Se você o usar em seu
	próprio script, por favor deixe os créditos como estão. Também,
	envie-me um e-mail se você o fizer, isto me deixa feliz :-)
*/

// classe que multiplica paginas
class Mult_Pag {
	// Valores padrão para a navegação dos links
	var $max_links;		// máximo de registro por pagina
	var $num_pesq_pag;	// máximo de registro por pagina
	var $str_anterior = "<<";
	var $str_proxima = ">>";
	// Variáveis usadas internamente
	var $nome_arq;			// nome da url
	var $total_reg;		// total de registros na pesquisa
	var $pagina;			// página atual
	var $qtd_paginas;	
	var $exibir_menu;




	/*
		Metodo construtor. Isto é somente usado para setar
		o número atual de colunas e outros métodos que
		podem ser re-usados mais tarde.
	*/
	function Mult_Pag () {
		global $pagina;
		$this->pagina = $pagina ? $pagina : 1;
		}




	/*
		O próximo método roda o que é necessário para as queries.
		É preciso rodá-lo para que ele pegue o total
		de colunas retornadas, e em segundo para pegar o total de
		links limitados.

		$sql parâmetro:
		. o parâmetro atual da query que será executada

		$conexao parâmetro:
		. a ligação da conexão do banco de dados

		$tipo parâmetro:
		. "mysql" - usa funções php mysql
		. "pgsql" - usa funções pgsql php
	*/	
	function Executar($sql, $conexao, $velocidade, $tipo) {
		// variavel para o inicio das pesquisas
		
		if ($this->pagina==0)
			$inicio_pesq = $this->pagina * $this->num_pesq_pag;
		else
			$inicio_pesq = ($this->pagina -1) * $this->num_pesq_pag;

		if ($velocidade == "otimizada") {
			$total_sql = preg_replace("/SELECT (.*?) FROM /sei", "'SELECT COUNT(*) FROM '", $sql);
			}
			
		else {
      	$total_sql = $sql;
			}
			
				// tipo da pesquisa
		if ($tipo == "mysql") {
			$resultado = mysql_query($total_sql, $conexao);
			$this->total_reg = mysql_result($resultado, 0, 0);	// total de registros da pesquisa inteira
			$sql .= " LIMIT $inicio_pesq , $this->num_pesq_pag";
			$resultado = mysql_query($sql, $conexao);				// pesquisa com limites por pagina			
			}
			
		elseif ($tipo == "pgsql") {
			$resultado = pg_Exec($conexao, $total_sql);
			$this->total_reg = pg_Result($resultado, 0, 0);		// total de registros da pesquisa inteira
			$sql .= " LIMIT $this->num_pesq_pag, $comeco";
			$resultado = pg_Exec($conexao, $sql);					// pesquisa com limites por pagina
    		}
    	return $resultado;
		}





	/*
		Este método cria uma string que irá ser adicionada à
		url dos links de navegação. Isto é especialmente importante
		para criar links dinâmicos, então se você quiser adicionar
		opções adicionais à estas queries, a classe de navegação
		irá adicionar automaticamente aos links de navegação
		dinâmicos.
	*/
	function Construir_Url() {
		global $REQUEST_URI, $REQUEST_METHOD, $HTTP_GET_VARS, $HTTP_POST_VARS;

		// separa o link em 2 strings
		@list($this->nome_arq, $voided) = @explode("?", $REQUEST_URI);

		if ($REQUEST_METHOD == "GET")
			$cgi = $HTTP_GET_VARS;
		else
			$cgi = $HTTP_POST_VARS;
			
		reset($cgi); // posiciona no inicio do array

		// separa a coluna com o seu respectivo valor
		while (list($chave, $valor) = each($cgi))
			if ($chave != "pagina")
				$query_string .= "&" . $chave . "=" . $valor;

		return $query_string;
		}





	/*
		Este método cria uma ligação de todos os links da barra de
		navegação. Isto é útil, pois é totalmente independete do layout
		ou design da página. Este método retorna a ligação dos links
		chamados no script php, sendo assim, você pode criar links de
		navegação com o conteúdo atual da página.

			$opcao parâmetro:
			 . "todos" - retorna todos os links de navegação
			 . "numeracao" - retorna apenas páginas com links numerados
			 . "strings" - retornar somente os links 'Próxima' e/ou 'Anterior'

			$mostra_string parâmetro:
			 . "nao" - mostra 'Próxima' ou 'Anterior' apenas quando for necessários
			 . "sim" - mostra 'Próxima' ou 'Anterior' de qualqur maneira
	*/
  function Construir_Links() {
     	$extra_vars = $this->Construir_Url();
     	$num_mult_pag = ceil($this->total_reg / $this->num_pesq_pag); // numero de multiplas paginas
     	
     	$inicio = floor(($this->pagina-1) / $this->max_links) * $this->max_links;
     	
	
  		$lnk=$inicio - $this->max_links+1;	// deve apontar para o primeiro elemento no link menos total de links
  		
  		// link voltar paginação
  		if ($lnk > 0)
  			$array[0] = "<a href='$this->nome_arq?pagina=$lnk$extra_vars'/>";
  			
  		$array[0] .= "{$this->str_anterior}</a>";
		
		
		if ($num_mult_pag <= $this->max_links) {
			$max=$num_mult_pag;			
			}
		elseif ($inicio + $this->max_links > $num_mult_pag) {
			$max=$num_mult_pag-$inicio;
			}
		else {
			$max=$this->max_links;			
			}
		
		//paginação central
     	for ($atual=1; $atual <= $max; $atual++) {
  			// escreve a numeracao (1 2 3 ...)  			
  			$lnk=$inicio + $atual;  			
  			if ($lnk!=$this->pagina)
  				$array[$atual]= "<a href='$this->nome_arq?pagina=$lnk$extra_vars'/>";
  			$array[$atual] .= "$lnk</a>";
  			}
  		
  		// o valor de $atual deve apontar para a proxima posição ou seja max_links +1
  			
  		// próxima paginação
  		$lnk++;
  		if ($lnk < $num_mult_pag-1)
  			$array[$atual] = "<a href='$this->nome_arq?pagina=$lnk$extra_vars'/>{$this->str_proxima}</a>";
  		else
  			$array[$atual] = $this->str_proxima;
  			
  					
  		$this->qtd_paginas = count($array);		//	Faz a contagem da quantidade de páginas que serão utilizadas
  		 		
  		if ($this->qtd_paginas != 3) {			//	Caso a quantidade de páginas seja 1, o menu de navegação não é exibido
  			for ($n = 0; $n < $this->qtd_paginas; $n++) {			
				echo $array[$n] . "&nbsp;&nbsp;&nbsp;";
				}
			echo " [$num_mult_pag pgs.]";
  			}
  		else
  			$this->exibir_menu = false;		
		}	
  
	}	// fim da class
?>
