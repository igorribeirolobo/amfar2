<?
	include("global.php");
?>

<html>
   <head>
	  <meta http-equiv="Content-Language" content="pt-br">
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	  <META NAME="robots" CONTENT="All">
	  <title>AMF ASSOCIAÇÃO MINEIRA DE FARMACÊUTICOS</title>
	  <link rel="stylesheet" href="css/style.css" type="text/css">
	  <script type="text/javascript" src="http://www.amfar.com.br/js/ajax.js"></script>
	   <script type="text/javascript" src="js/jquery-1.3.2.js"></script>
   </head>

   <body topmargin="4" leftmargin="0" style="background: url('http://www.amfar.com.br/css/background.png')">
      <div id="dvTudo">
         <div id="dvTopo"><? include("topo.html") ?></div>
         <div id="dvMenu" style="margin:2px 0">
            <script type='text/javascript' src='js/menuProg.js'></script>
            <script type='text/javascript' src='js/menuTree.js'></script>					 
			</div><?
			if ($pg!=51) { ?>
         <div id="dvMain" style='height:438px; margin-top:0px'><? include($pages[$pg]) ?></div>            
  			<div id="dvPublic" style='background:none'>
  				<? include 'destaques.php'; ?>				
         </div>
         
			<?
			}
			else {
			 echo "<div id='dvMain' style='height:438px'>";
			 include($pages[$pg]);
			 echo"</div>";
			 }
			 if ($pg==10||!$_GET['lnk']) {?>
			 
				<table width=770 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<img src="images/homeBottom_01.jpg" width=266 height=485 alt=""></td>
						<td>
							<img src="images/homeBottom_02.jpg" width=258 height=485 alt=""></td>
						<td>
							<img src="images/homeBottom_03.jpg" width=246 height=485 alt=""></td>
					</tr>
				</table><?
				}
			 	?>
			 
			 <div id="dvRodape"><? include("includes/rodape.html") ?></div>         
		</div>
   </body>
</html>
<script>startclock()</script>
