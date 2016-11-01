<?php
/*
 * e-Mailing - Sistema automatizado para criação e envio de emails por lote
 * Copyright (C) 2007 Lauro A. L. Brito
	 *
	 * == BEGIN LICENSE ==
	 *
	 * Licensed under the terms of any of the following licenses at your
	 * choice:
	 *
	 *  - GNU General Public License Version 2 or later (the "GPL")
	 *    http://www.gnu.org/licenses/gpl.html
	 *
	 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
	 *    http://www.gnu.org/licenses/lgpl.html
	 *
	 *  - Mozilla Public License Version 1.1 or later (the "MPL")
	 *    http://www.mozilla.org/MPL/MPL-1.1.html
	 *
	 * == END LICENSE ==
	 *
	 * Arquivo inicial
 */
 
	if (!file_exists('config/config.php')) {
		header("location:config.php");
		die();
		}
		
	include 'config/config.php';
?>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
	<meta name="ProgId" content="FrontPage.Editor.Document">
	<title>News Letters</title>

	<script language="javascript">			
		// Main editor scripts.
		var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
		document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
	</script>
	
	<script language="javascript" type="text/javascript" src="ajax.js"></script>
</head>

<body bgcolor="#C0C0C0" topmargin="50">

<div id="tudo" style="height:auto">
  <table border="0" cellpadding="4" width="100%" cellspacing="4">
    <tr>
      <td>

			<div align="center">
			  <table border="0" cellpadding="2" width="400">
				 <tr>
					<td width="70" rowspan="2" style="border:2px solid #c0c0c0">
					  &nbsp;<img border="0" src="images/logoTopo.gif">
					</td>
					<td align="center" style="border:2px solid #c0c0c0">
						<font face="Arial Black" size="5" color="#c0c0c0">E-Mailing System</font><b>
					</td>
				 </tr>
				 <tr>
					<td align="right" style="background:#e0e0e0">
					  <b><font face="Arial" size="2">vs: <?=$_vs?> - <?=$_data?></font></b>
					</td>
				 </tr>
			  </table>
			</div>

        <br>
        <p align="center">&nbsp;</p>
        <div align="center">
          <table border="0" cellpadding="4" cellspacing="4" width="80%" style="border:2px solid #c0c0c0">
            <tr>
              <td width="50%">
						<div id="dvSender"><script>ajaxLoader('userLogin.php','dvSender')</script></div>
              </td>
            </tr>
          </table>
        </div>
        <p>&nbsp;</p>
			<p style="text-align:right; padding:2px 4px; background:#60c659; color:#ffffef">					  
				<b>LAB Design © copyright 2007 - E-mail: <a href="mailto:lab.design@globo.com.br">webmaster</a></p>

      </td>
    </tr>
  </table>
  </center>
</div>

</body>

</html>
