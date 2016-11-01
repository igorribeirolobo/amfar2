<?php

/**
* Filename.......: class.html.mime.mail.inc
* Project........: HTML Mime mail class
* Last Modified..: $Date: 2002/07/24 13:14:10 $
* CVS Revision...: $Revision: 1.4 $
* Copyright......: 2001, 2002 Richard Heyes
*/


require_once(dirname(__FILE__) . '/mimePart.php');


class htmlMimeMail
{
	/**
	* The html part of the message
    * @var string
    */
	var $html;

	/**
	* The text part of the message(only used in TEXT only messages)
	* @var string
	*/
	var $text;

	/**
	* The main body of the message after building
	* @var string
	*/
	var $output;

	/**
	* The alternative text to the HTML part (only used in HTML messages)
	* @var string
	*/
	var $html_text;

	/**
	* An array of embedded images/objects
	* @var array
	*/
	var $html_images;

	/**
	* An array of recognised image types for the findHtmlImages() method
	* @var array
	*/
	var $image_types;

	/**
	* Parameters that affect the build process
	* @var array
	*/
	var $build_params;

	/**
	* Array of attachments
	* @var array
	*/
	var $attachments;

	/**
	* The main message headers
	* @var array
	*/
	var $headers;

	/**
	* Whether the message has been built or not
	* @var boolean
	*/
	var $is_built;
	
	/**
    * The return path address. If not set the From:
	* address is used instead
	* @var string
    */
	var $return_path;
	
	/**
    * Array of information needed for smtp sending
	* @var array
    */
	var $smtp_params;

/**
* Constructor function. Sets the headers
* if supplied.
*/

	function htmlMimeMail()
	{
		/**
        * Initialise some variables.
        */
		$this->html_images = array();
		$this->headers     = array();
		$this->is_built    = false;

		/**
        * If you want the auto load functionality
		* to find other image/file types, add the
		* extension and content type here.
        */
		$this->image_types = array(
									'gif'	=> 'image/gif',
									'jpg'	=> 'image/jpeg',
									'jpeg'	=> 'image/jpeg',
									'jpe'	=> 'image/jpeg',
									'bmp'	=> 'image/bmp',
									'png'	=> 'image/png',
									'tif'	=> 'image/tiff',
									'tiff'	=> 'image/tiff',
									'swf'	=> 'application/x-shockwave-flash'
								  );

		/**
        * Set these up
        */
		$this->build_params['html_encoding'] = 'quoted-printable';
		$this->build_params['text_encoding'] = '7bit';
		$this->build_params['html_charset']  = 'ISO-8859-1';
		$this->build_params['text_charset']  = 'ISO-8859-1';
		$this->build_params['head_charset']  = 'ISO-8859-1';
		$this->build_params['text_wrap']     = 998;

		/**
        * Defaults for smtp sending
        */
		if (!empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'])) {
			$helo = $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'];
		} elseif (!empty($GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'])) {
			$helo = $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'];
		} else {
			$helo = 'localhost';
		}

		$this->smtp_params['host'] = 'localhost';
		$this->smtp_params['port'] = 25;
		$this->smtp_params['helo'] = $helo;
		$this->smtp_params['auth'] = false;
		$this->smtp_params['user'] = '';
		$this->smtp_params['pass'] = '';

		/**
        * Make sure the MIME version header is first.
        */
		$this->headers['MIME-Version'] = '1.0';
	}

/**
* This function will read a file in
* from a supplied filename and return
* it. This can then be given as the first
* argument of the the functions
* add_html_image() or add_attachment().
*/
	function getFile($filename)
	{
		$return = '';
		if ($fp = fopen($filename, 'rb')) {
			while (!feof($fp)) {
				$return .= fread($fp, 1024);
			}
			fclose($fp);
			return $return;

		} else {
			return false;
		}
	}

/**
* Accessor to set the CRLF style
*/
	function setCrlf($crlf = "\n")
	{
		if (!defined('CRLF')) {
			define('CRLF', $crlf, true);
		}

		if (!defined('MAIL_MIMEPART_CRLF')) {
			define('MAIL_MIMEPART_CRLF', $crlf, true);
		}
	}

/**
* Accessor to set the SMTP parameters
*/
	function setSMTPParams($host = null, $port = null, $helo = null, $auth = null, $user = null, $pass = null)
	{
		if (!is_null($host)) $this->smtp_params['host'] = $host;
		if (!is_null($port)) $this->smtp_params['port'] = $port;
		if (!is_null($helo)) $this->smtp_params['helo'] = $helo;
		if (!is_null($auth)) $this->smtp_params['auth'] = $auth;
		if (!is_null($user)) $this->smtp_params['user'] = $user;
		if (!is_null($pass)) $this->smtp_params['pass'] = $pass;
	}

/**
* Accessor function to set the text encoding
*/
	function setTextEncoding($encoding = '7bit')
	{
		$this->build_params['text_encoding'] = $encoding;
	}

/**
* Accessor function to set the HTML encoding
*/
	function setHtmlEncoding($encoding = 'quoted-printable')
	{
		$this->build_params['html_encoding'] = $encoding;
	}

/**
* Accessor function to set the text charset
*/
	function setTextCharset($charset = 'ISO-8859-1')
	{
		$this->build_params['text_charset'] = $charset;
	}

/**
* Accessor function to set the HTML charset
*/
	function setHtmlCharset($charset = 'ISO-8859-1')
	{
		$this->build_params['html_charset'] = $charset;
	}

/**
* Accessor function to set the header encoding charset
*/
	function setHeadCharset($charset = 'ISO-8859-1')
	{
		$this->build_params['head_charset'] = $charset;
	}

/**
* Accessor function to set the text wrap count
*/
	function setTextWrap($count = 998)
	{
		$this->build_params['text_wrap'] = $count;
	}

/**
* Accessor to set a header
*/
	function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

/**
* Accessor to add a Subject: header
*/
	function setSubject($subject)
	{
		$this->headers['Subject'] = $subject;
	}
	

/**
* Accessor to add a From: header
*/
	function setFrom($from)
	{
		$this->headers['From'] = $from;
	}

/**
* Accessor to set the return path
*/
	function setReturnPath($return_path)
	{
		$this->return_path = $return_path;
	}

/**
* Accessor to add a Cc: header
*/
	function setCc($cc)
	{
		$this->headers['Cc'] = $cc;
	}

/**
* Accessor to add a Bcc: header
*/
	function setBcc($bcc)
	{
		$this->headers['Bcc'] = $bcc;
	}

	function setReplayTo($replayTo)
	{
		$this->headers['Reply-to'] = $replayTo;
	}


/**
* Adds plain text. Use this function
* when NOT sending html email
*/
	function setText($text = '')
	{
		$this->text = $text;
	}

/**
* Adds a html part to the mail.
* Also replaces image names with
* content-id's.
*/
	function setHtml($html, $text = null, $images_dir = null)
	{
		$this->html      = $html;
		$this->html_text = $text;

		if (isset($images_dir)) {
			$this->_findHtmlImages($images_dir);
		}
	}

/**
* Function for extracting images from
* html source. This function will look
* through the html code supplied by add_html()
* and find any file that ends in one of the
* extensions defined in $obj->image_types.
* If the file exists it will read it in and
* embed it, (not an attachment).
*
* @author Dan Allen
*/
	function _findHtmlImages($images_dir)
	{
		// Build the list of image extensions
		while (list($key,) = each($this->image_types)) {
			$extensions[] = $key;
		}

		preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/Ui', $this->html, $images);

		for ($i=0; $i<count($images[1]); $i++) {
			if (file_exists($images_dir . $images[1][$i])) {
				$html_images[] = $images[1][$i];
				$this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
			}
		}

		if (!empty($html_images)) {

			// If duplicate images are embedded, they may show up as attachments, so remove them.
			$html_images = array_unique($html_images);
			sort($html_images);
	
			for ($i=0; $i<count($html_images); $i++) {
				if ($image = $this->getFile($images_dir.$html_images[$i])) {
					$ext = substr($html_images[$i], strrpos($html_images[$i], '.') + 1);
					$content_type = $this->image_types[strtolower($ext)];
					$this->addHtmlImage($image, basename($html_images[$i]), $content_type);
				}
			}
		}
	}

/**
* Adds an image to the list of embedded
* images.
*/
	function addHtmlImage($file, $name = '', $c_type='application/octet-stream')
	{
		$this->html_images[] = array(
										'body'   => $file,
										'name'   => $name,
										'c_type' => $c_type,
										'cid'    => md5(uniqid(time()))
									);
	}


/**
* Adds a file to the list of attachments.
*/
	function addAttachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64')
	{
		$this->attachments[] = array(
									'body'		=> $file,
									'name'		=> $name,
									'c_type'	=> $c_type,
									'encoding'	=> $encoding
								  );
	}

/**
* Adds a text subpart to a mime_part object
*/
	function &_addTextPart(&$obj, $text)
	{
		$params['content_type'] = 'text/plain';
		$params['encoding']     = $this->build_params['text_encoding'];
		$params['charset']      = $this->build_params['text_charset'];
		if (is_object($obj)) {
			return $obj->addSubpart($text, $params);
		} else {
			return new Mail_mimePart($text, $params);
		}
	}

/**
* Adds a html subpart to a mime_part object
*/
	function &_addHtmlPart(&$obj)
	{
		$params['content_type'] = 'text/html';
		$params['encoding']     = $this->build_params['html_encoding'];
		$params['charset']      = $this->build_params['html_charset'];
		if (is_object($obj)) {
			return $obj->addSubpart($this->html, $params);
		} else {
			return new Mail_mimePart($this->html, $params);
		}
	}

/**
* Starts a message with a mixed part
*/
	function &_addMixedPart()
	{
		$params['content_type'] = 'multipart/mixed';
		return new Mail_mimePart('', $params);
	}

/**
* Adds an alternative part to a mime_part object
*/
	function &_addAlternativePart(&$obj)
	{
		$params['content_type'] = 'multipart/alternative';
		if (is_object($obj)) {
			return $obj->addSubpart('', $params);
		} else {
			return new Mail_mimePart('', $params);
		}
	}

/**
* Adds a html subpart to a mime_part object
*/
	function &_addRelatedPart(&$obj)
	{
		$params['content_type'] = 'multipart/related';
		if (is_object($obj)) {
			return $obj->addSubpart('', $params);
		} else {
			return new Mail_mimePart('', $params);
		}
	}

/**
* Adds an html image subpart to a mime_part object
*/
	function &_addHtmlImagePart(&$obj, $value)
	{
		$params['content_type'] = $value['c_type'];
		$params['encoding']     = 'base64';
		$params['disposition']  = 'inline';
		$params['dfilename']    = $value['name'];
		$params['cid']          = $value['cid'];
		$obj->addSubpart($value['body'], $params);
	}

/**
* Adds an attachment subpart to a mime_part object
*/
	function &_addAttachmentPart(&$obj, $value)
	{
		$params['content_type'] = $value['c_type'];
		$params['encoding']     = $value['encoding'];
		$params['disposition']  = 'attachment';
		$params['dfilename']    = $value['name'];
		$obj->addSubpart($value['body'], $params);
	}

/**
* Builds the multipart message from the
* list ($this->_parts). $params is an
* array of parameters that shape the building
* of the message. Currently supported are:
*
* $params['html_encoding'] - The type of encoding to use on html. Valid options are
*                            "7bit", "quoted-printable" or "base64" (all without quotes).
*                            7bit is EXPRESSLY NOT RECOMMENDED. Default is quoted-printable
* $params['text_encoding'] - The type of encoding to use on plain text Valid options are
*                            "7bit", "quoted-printable" or "base64" (all without quotes).
*                            Default is 7bit
* $params['text_wrap']     - The character count at which to wrap 7bit encoded data.
*                            Default this is 998.
* $params['html_charset']  - The character set to use for a html section.
*                            Default is ISO-8859-1
* $params['text_charset']  - The character set to use for a text section.
*                          - Default is ISO-8859-1
* $params['head_charset']  - The character set to use for header encoding should it be needed.
*                          - Default is ISO-8859-1
*/
	function buildMessage($params = array())
	{
		if (!empty($params)) {
			while (list($key, $value) = each($params)) {
				$this->build_params[$key] = $value;
			}
		}

		if (!empty($this->html_images)) {
			foreach ($this->html_images as $value) {
				$this->html = str_replace($value['name'], 'cid:'.$value['cid'], $this->html);
			}
		}

		$null        = null;
		$attachments = !empty($this->attachments) ? true : false;
		$html_images = !empty($this->html_images) ? true : false;
		$html        = !empty($this->html)        ? true : false;
		$text        = isset($this->text)         ? true : false;

		switch (true) {
			case $text AND !$attachments:
				$message = &$this->_addTextPart($null, $this->text);
				break;

			case !$text AND $attachments AND !$html:
				$message = &$this->_addMixedPart();

				for ($i=0; $i<count($this->attachments); $i++) {
					$this->_addAttachmentPart($message, $this->attachments[$i]);
				}
				break;

			case $text AND $attachments:
				$message = &$this->_addMixedPart();
				$this->_addTextPart($message, $this->text);

				for ($i=0; $i<count($this->attachments); $i++) {
					$this->_addAttachmentPart($message, $this->attachments[$i]);
				}
				break;

			case $html AND !$attachments AND !$html_images:
				if (!is_null($this->html_text)) {
					$message = &$this->_addAlternativePart($null);
					$this->_addTextPart($message, $this->html_text);
					$this->_addHtmlPart($message);
				} else {
					$message = &$this->_addHtmlPart($null);
				}
				break;

			case $html AND !$attachments AND $html_images:
				if (!is_null($this->html_text)) {
					$message = &$this->_addAlternativePart($null);
					$this->_addTextPart($message, $this->html_text);
					$related = &$this->_addRelatedPart($message);
				} else {
					$message = &$this->_addRelatedPart($null);
					$related = &$message;
				}
				$this->_addHtmlPart($related);
				for ($i=0; $i<count($this->html_images); $i++) {
					$this->_addHtmlImagePart($related, $this->html_images[$i]);
				}
				break;

			case $html AND $attachments AND !$html_images:
				$message = &$this->_addMixedPart();
				if (!is_null($this->html_text)) {
					$alt = &$this->_addAlternativePart($message);
					$this->_addTextPart($alt, $this->html_text);
					$this->_addHtmlPart($alt);
				} else {
					$this->_addHtmlPart($message);
				}
				for ($i=0; $i<count($this->attachments); $i++) {
					$this->_addAttachmentPart($message, $this->attachments[$i]);
				}
				break;

			case $html AND $attachments AND $html_images:
				$message = &$this->_addMixedPart();
				if (!is_null($this->html_text)) {
					$alt = &$this->_addAlternativePart($message);
					$this->_addTextPart($alt, $this->html_text);
					$rel = &$this->_addRelatedPart($alt);
				} else {
					$rel = &$this->_addRelatedPart($message);
				}
				$this->_addHtmlPart($rel);
				for ($i=0; $i<count($this->html_images); $i++) {
					$this->_addHtmlImagePart($rel, $this->html_images[$i]);
				}
				for ($i=0; $i<count($this->attachments); $i++) {
					$this->_addAttachmentPart($message, $this->attachments[$i]);
				}
				break;

		}

		if (isset($message)) {
			$output = $message->encode();
			$this->output   = $output['body'];
			$this->headers  = array_merge($this->headers, $output['headers']);

			// Add message ID header
			srand((double)microtime()*15030300);
			$message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), !empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST']) ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'] : $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME']);
			$this->headers['Message-ID'] = $message_id;

			$this->is_built = true;
			return true;
		} else {
			return false;
		}
	}

/**
* Function to encode a header if necessary
* according to RFC2047
*/
	function _encodeHeader($input, $charset = 'ISO-8859-1')
	{
		preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);
		foreach ($matches[1] as $value) {
			$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
		}
		
		return $input;
	}

/**
* Sends the mail.
*
* @param  array  $recipients
* @param  string $type OPTIONAL
* @return mixed
*/
	function send($recipients, $type = 'mail')
	{
		if (!defined('CRLF')) {
			$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
		}

		if (!$this->is_built) {
			$this->buildMessage();
		}

		switch ($type) {
			case 'mail':
				$subject = '';
				if (!empty($this->headers['Subject'])) {
					$subject = $this->_encodeHeader($this->headers['Subject'], $this->build_params['head_charset']);
					unset($this->headers['Subject']);
				}

				// Get flat representation of headers
				foreach ($this->headers as $name => $value) {
					$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
				}

				$to = $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);

				if (!empty($this->return_path)) {
					$result = mail($to, $subject, $this->output, implode(CRLF, $headers), '-f' . $this->return_path);
				} else {					
					$result = mail($to, $subject, $this->output, implode(CRLF, $headers));					
				}
				
				// Reset the subject in case mail is resent
				if ($subject !== '') {
					$this->headers['Subject'] = $subject;
				}
				
				// Return
				return $result;
				break;

			case 'smtp':
				require_once(dirname(__FILE__) . '/smtp.php');
				require_once(dirname(__FILE__) . '/RFC822.php');
				$smtp = &smtp::connect($this->smtp_params);
				
				// Parse recipients argument for internet addresses
				foreach ($recipients as $recipient) {
					$addresses = Mail_RFC822::parseAddressList($recipient, $this->smtp_params['helo'], null, false);
					foreach ($addresses as $address) {
						$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
					}
				}
				unset($addresses); // These are reused
				unset($address);   // These are reused

				// Get flat representation of headers, parsing
				// Cc and Bcc as we go
				foreach ($this->headers as $name => $value) {
					if ($name == 'Cc' OR $name == 'Bcc') {
						$addresses = Mail_RFC822::parseAddressList($value, $this->smtp_params['helo'], null, false);
						foreach ($addresses as $address) {
							$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
						}
					}
					if ($name == 'Bcc') {
						continue;
					}
					$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
				}
				// Add To header based on $recipients argument
				$headers[] = 'To: ' . $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);
				
				// Add headers to send_params
				$send_params['headers']    = $headers;
				$send_params['recipients'] = array_values(array_unique($smtp_recipients));
				$send_params['body']       = $this->output;

				// Setup return path
				if (isset($this->return_path)) {
					$send_params['from'] = $this->return_path;
				} elseif (!empty($this->headers['From'])) {
					$from = Mail_RFC822::parseAddressList($this->headers['From']);
					$send_params['from'] = sprintf('%s@%s', $from[0]->mailbox, $from[0]->host);
				} else {
					$send_params['from'] = 'postmaster@' . $this->smtp_params['helo'];
				}

				// Send it
				if (!$smtp->send($send_params)) {
					$this->errors = $smtp->errors;
					return false;
				}
				return true;
				break;
		}
	}

/**
* Use this method to return the email
* in message/rfc822 format. Useful for
* adding an email to another email as
* an attachment. there's a commented
* out example in example.php.
*/
	function getRFC822($recipients)
	{
		// Make up the date header as according to RFC822
		$this->setHeader('Date', date('D, d M y H:i:s O'));

		if (!defined('CRLF')) {
			$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
		}

		if (!$this->is_built) {
			$this->buildMessage();
		}

		// Return path ?
		if (isset($this->return_path)) {
			$headers[] = 'Return-Path: ' . $this->return_path;
		}

		// Get flat representation of headers
		foreach ($this->headers as $name => $value) {
			$headers[] = $name . ': ' . $value;
		}
		$headers[] = 'To: ' . implode(', ', $recipients);

		return implode(CRLF, $headers) . CRLF . CRLF . $this->output;
	}
} // End of class.


function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit) {
	if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
		$urlSite='http://10.1.1.110/amfar';
	else
		$urlSite='http://amfar.tempsite.ws';


// parte text/html
	$ds = date("w");
	$mes = date("n");
	$dia = substr(date("0d"), -2);	
	$ano = date("Y");
	
	$nomeDia = array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado');
	$nomeMes = array('','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$dataext =	$nomeDia[$ds] . ", $dia de " . $nomeMes[$mes] . " de $ano";	
	
	// parte text/html
		
	$shtml = "
		<html>
			<head>
				<title>AMF - Associação Mineira de Farmacêuticos</title>
				<style>
				P, p {
					font: normal 9pt Tahoma, Verdana, Arial, Helvetica, sans-serif;
					color: #808080
					}
					
				td, TD {
					padding: 0 4px;
					font: normal 9pt Arial, Helvetica, sans-serif, Tahoma, Verdana,;
					color: #808080
					}

				a {
					font: bolder 9pt Arial, Helvetica, sans-serif, Tahoma, Verdana,;
					color:#000066;
					text-decoration:none
					}
					
				a:hover {
					color:#ff6600;
					text-decoration:underline;					
					}
					
				.boxborder	{border:1px solid #f0f0f0}
				
				.tleft {
					border:1px solid #f0f0f0;
					padding-left:3pt;
					font-size:9pt;
					font-weight:normal;
					color:#808080
					}
					
				.tcenter {
					border:1px solid #f0f0f0;
					text-align:center
					}
					
				.tright {
					border:1px solid #f0f0f0;
					text-align:right;
					padding-right:3pt;
					font-size:9pt;
					font-weight:bold;
					color:#006666
					}
					
				.mtit {
					border:1px solid #f0f0f0;
					text-align:center;
					font-size:16pt;
					font-weight:bold;
					color:#000066
					}
					
				.tfooter {
					background-color:#D8D4E6;
					text-align:center;
					font-size:12pt
					}

			</style>
		
		</head>
		
		<body topmargin=4 leftmargin=4>	
			<table border=0 cellpadding=2 cellspacing=2 width=565 class=boxborder>
				<tr>
					<td rowspan=2 width='230'>
						<a href='$urlSite'><img src='$urlSite/images/logoMail.gif' border='0' width='230'></a></td>
					<td align=center class=mtit style='width:330px'>$tit</td>
				</tr>
				<tr>
					<td class=tcenter>$dataext</td></tr>
				
				<tr>
					<td colspan=2>$mtb</td>
				</tr>
				<tr>
					<td class=tfooter colspan=2 align=center>
						<a href='$urlSite'/>:: AMF Associação Mineira de Farmacêuticos ::</a></td>
				</tr>
			</table>
		</body>
	</html>";
	
	$text = strip_tags($shtml);
	
	/**
	*
	* instancia novo objeto do class email
	* Preenche o novo objeto
	* envia o email
	* retorna a mensagem de enviado ou não
	*
	*/
	$mail = new htmlMimeMail();
	
	$mail->setFrom("$mnf <$mmf>");
	$mail->setSubject($ms);
	$mail->setReplayTo($mto);
	$mail->setHtml($shtml, $text, './');		
	$result = $mail->send(array($mto));
	$msgMail = $result ? 'E-mail enviado com sucesso' : $mail->errors;	
	return($msgMail);
	}

// teste
	$eBody="		
		<table cellSpacing=2 cellPadding=2 width=100% border=0>
			<tr>
				<td align=right class=boxBorder>Nome:</td>
				<td class=boxBorder><b>Lauro A L Brito</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>E-mail:</td>
				<td class=boxBorder><b>lab.design@globo.com</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Assunto:</td>
				<td class=boxBorder><b>Teste htmlMimeMail</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Mensagem:</td>
				<td class=boxBorder>sahdfkh wqiehrihwqeir hiwhri wheirhkndsk fksandfl asdifi wierhiw irhweir klwen kfdsklnfiansfi wieriw ih</td>
			</tr>
		</table>";
	// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
	$mailsend = sendMail('lab.design@globo.com', 'Webmaster', 'webmaster@amfar.com.br', 'AMF/Teste Contato', $eBody, 'Contato');	
?>
