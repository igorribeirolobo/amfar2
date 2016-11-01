<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>openWYSIWYG | Select Color</title>
</head>

<script language="JavaScript" type="text/javascript">
	var qsParm = new Array();
	
	
	/* ---------------------------------------------------------------------- *\
	Function    : retrieveWYSIWYG()
	Description : Retrieves the textarea ID for which the image will be inserted into.
	\* ---------------------------------------------------------------------- */
	function retrieveWYSIWYG() {
		var query = window.location.search.substring(1);
		var parms = query.split('&');
		for (var i=0; i<parms.length; i++) {
			var pos = parms[i].indexOf('=');
			if (pos > 0) {
				 var key = parms[i].substring(0,pos);
				 var val = parms[i].substring(pos+1);
				 qsParm[key] = val;
				}
			}
		}
	
	
	/* ---------------------------------------------------------------------- *\
	Function    : insertImage()
	Description : Inserts image into the WYSIWYG.
	\* ---------------------------------------------------------------------- */
	function insertImage() {
		//var url = location.search.split("/");
		var urlBase = "http://www.sbrafh.org.br/novosite/public/news/";
		var image = '<img src="' + urlBase + document.getElementById('imageurl').value + '" alt="' + document.getElementById('alt').value + '" alignment="' + document.getElementById('alignment').value + '" border="' + document.getElementById('borderThickness').value + '" hspace="' + document.getElementById('horizontal').value + '" vspace="' + document.getElementById('vertical').value + '">';
		window.opener.insertHTML(image, qsParm['wysiwyg']);
		window.close();
		}

	function insertPath(id) {
		document.getElementById('imageurl').value = id;
		}
		


</script>

<script type="text/javascript" src="http://www.sbrafh.org.br/jsLibrary/jquery-1.3.2.js"></script>
<script>
	var aberto = false;
	$(document).ready(function(){
		$('#showAll').click(function(){
			if(aberto){
				$('#outras').css('display', 'none');
				aberto = false;
				}
			else{
				$('#outras').css('display', 'block');
				aberto = true;
				}
			})
		})
</script>



<body bgcolor="#EEEEEE" marginwidth="0" marginheight="0" topmargin="0" leftmargin="0" onLoad="retrieveWYSIWYG();">

<div id="images" style="width:100%;margin-top:4px">
	<?
	function debug($var){
		echo"<pre>";
		print_r($var);
		echo"</pre>";	
		}
	
	$_folder  = "../../news";
	$dh = dir ("$_folder");
	$idx=0;
	//$_images = array();
	while($entry = $dh->read()) :					
		$t = explode('.', $entry);
		$ext = strtolower($t[count($t)-1]);							
		if ($ext=="jpg"||$ext=="jpeg"||$ext=="gif"||$ext=="png"){
			$_images[$idx]['data'] = date("Y.m.d H:i:s", filemtime("$_folder/$entry"));
			$_images[$idx]['url'] = $entry;
			$idx++;
			}					
	endwhile;
	
	//debug($_images);
	rsort($_images);
	//debug($_images);

	echo"<img src='mini.php?img=$_folder/{$_images[0]['url']}&wh=75' border='0' alt='{$_images[0]['url']}' title='{$_images[0]['url']}' id='{$_images[0]['url']}'
				onclick='insertPath(this.id)'' style='cursor:pointer;margin-right:30px' align='absMiddle'/><a href='#' id='showAll'>Ver Todas</a>";
	
	echo "<div id='outras' style='display:none'>";
	$idx=1;
	while($idx < count($_images)) :	?>				
		<img src="mini.php?img=<?=$_folder.'/'.$_images[$idx]['url']?>&wh=75" border="0" alt="<?=$_images[$idx]['url']?>" 
			title="<?=$_images[$idx]['url']?>" id="<?=$_images[$idx]['url']?>"
			onclick="insertPath(this.id)"" style="cursor:pointer"/><?
		$idx++;			
	endwhile;
	echo "</div>"; ?>
</div>

<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F7F7; border: 2px solid #FFFFFF; padding: 10px;">
	<tr>
		<td>
			<span style="font-family: arial, verdana, helvetica; font-size: 11px; font-weight: bold;">Insert Image:</span>
			<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F7F7; border: 2px solid #FFFFFF; padding: 5px;">
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px">Image URL:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;"><input type="text" name="imageurl" id="imageurl" value=""  style="font-size: 10px; width: 100%;"></td>
				</tr>
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px;">Alternate Text:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;"><input type="text" name="alt" id="alt" value=""  style="font-size: 10px; width: 100%;"></td>
				</tr>
			</table>
		</td>
		
		<td width="10">&nbsp;</td>
		
		<td>
			<span style="font-family: arial, verdana, helvetica; font-size: 11px; font-weight: bold;">Layout:</span>
			<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F7F7; border: 2px solid #FFFFFF; padding: 5px;">
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px;" width="100">Alignment:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;" width="85">
						<select name="alignment" id="alignment" style="font-family: arial, verdana, helvetica; font-size: 11px; width: 100%;">
							<option value="">Not Set</option>
							<option value="left">Left</option>
							<option value="right">Right</option>
							<option value="texttop">Texttop</option>
							<option value="absmiddle">Absmiddle</option>
							<option value="baseline">Baseline</option>
							<option value="absbottom">Absbottom</option>
							<option value="bottom">Bottom</option>
							<option value="middle">Middle</option>
							<option value="top">Top</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px;">Border Thickness:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;"><input type="text" name="borderThickness" id="borderThickness" value=""  style="font-size: 10px; width: 100%;"></td>
				</tr>
			</table>	
		</td>
		
		<td width="10">&nbsp;</td>
		
		<td>		
			<span style="font-family: arial, verdana, helvetica; font-size: 11px; font-weight: bold;">Spacing:</span>
			<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F7F7; border: 2px solid #FFFFFF; padding: 5px;">
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px;" width="80">Horizontal:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;" width="105"><input type="text" name="horizontal" id="horizontal" value=""  style="font-size: 10px; width: 100%;"></td>
				</tr>
				<tr>
					<td style="padding-bottom: 2px; padding-top: 0px; font-family: arial, verdana, helvetica; font-size: 11px;">Vertical:</td>
					<td style="padding-bottom: 2px; padding-top: 0px;"><input type="text" name="vertical" id="vertical" value=""  style="font-size: 10px; width: 100%;"></td>
				</tr>
			</table>		
		</td>

		<td width="10">&nbsp;</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" style="background-color: #F7F7F7; border: 2px solid #FFFFFF; padding: 5px;">
				<tr>
					<td><input type="submit" value="  Submit  " onClick="insertImage();" style="font-size: 12px;" ></td>
				</tr>
				<tr>
					<td><input type="submit" value="  Cancel  " onClick="window.close();" style="font-size: 12px;" ></td>
				</tr>
			</table>
		</td
	</tr>	
</table>

</body>
</html>
