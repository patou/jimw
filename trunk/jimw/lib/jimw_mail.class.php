<?php
/**
 * jimw_mail.class.php
 *
 * @author Patrice de Saint Steban
 * @copyright JIMW
 * @package JIMW
 * @version 2.0
 */

class jimw_mail
{
	private $to = '';
	private $subject = '';
	private $messageText = '';
	private $messageHtml = '';
	private $message = '';
	private $header = array('X-Mailer' => 'PHP JIMW_MAIL');
	private $files = array();
	private $boundary = '';
	/**
	 * Constructor
	 *
	 * @param sting $to
	 * @param string $subject
	 * @return jimw_mail
	 */
	public function __construct($to, $subject)
	{
		$this->boundary = $this->generateBoundary();
		if (isset($to))
			$this->setTo($to);
		if (isset($subject))
			$this->setSubject($subject);
	}
	/**
	 * add an mail to send
	 *
	 * @param string $to
	 * @return void
	 */
	public function addTo($to)
	{
		if ($this->isMail($to))
			if (!empty($this->to))
				$this->to .= ', '. $to;
			else
				$this->to = $to;
	}
	/**
	 * add an mail to send in copy carbon
	 *
	 * @param string $to
	 * @return void
	 */
	public function addCC($to)
	{
		if ($this->isMail($to))
			if (!empty($this->header['CC']))
				$this->header['CC'] .= ', '. $to;
			else
				$this->header['CC'] = $to;
	}
	/**
	 * add an mail to send in b copy carbon
	 *
	 * @param string $to
	 * @return void
	 */
	public function addBCC($to)
	{
		if ($this->isMail($to))
			if (!empty($this->header['BCC']))
				$this->header['BCC'] .= ', '. $to;
			else
				$this->header['BCC'] = $to;
	}
	/**
	 * Set the subject
	 *
	 * @param string $subject
	 * @return void
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}
	/**
	 * Define the From Header
	 *
	 * @param string $from
	 * @return void
	 */
	public function setFrom($from)
	{
		if ($this->isMail($from))
			return $this->addHeader('From', $from);
	}
	/**
	 * Define the Reply-To
	 *
	 * @param string $replyto
	 * @return void
	 */
	public function setReplyTo($replyto)
	{
		if ($this->isMail($replyto))
			return $this->addHeader('Reply-To', $replyto);
	}
	/**
	 * Set the Text Part of the mail
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text)
	{
		$this->messageText = $text;
	}
	/**
	 * Set the Text Part of the mail
	 *
	 * @param string $text
	 * @return void
	 */
	public function setHtml($html)
	{
		$this->messageHtml = $html;
	}
	/**
	 * Set an Header
	 *
	 * @param string $name the name of the header
	 * @param string $value the value of the header
	 * @return void
	 */
	public function addHeader($name, $value)
	{
		if (!empty($value))
		{
			$tmp = explode(':', $name, 2);
			if (count($tmp) > 2)
				return ;
			$name = trim($tmp[0]);
			$value = trim($tmp[1]);
		}
		$this->header[$name] = $value;
	}
	/**
	 * Add many headers
	 *
	 * @param mixed $headers take a string of header or an array of name => value
	 * @return void
	 */
	public function addHeaders($headers)
	{
		if (is_string($headers))
			$headers = explode('\n', $headers);
		foreach ($headers as $name => $value)
		{
			$name = trim($name);
			$value = trim($value);
			if (is_string($name))
				$this->addHeader($value);
			else
				$this->addHeader($name, $value);
		}
	}
	/**
	 * Attach a Files to the mail
	 *
	 * @param string $file
	 * @param string $name
	 */
	public function addFiles($file, $name)
	{
		if (file_exists($file))
		{
			if (empty($name))
				$name = basename($file);
			$this->files[$name] = $file;
		}
	}
	/**
	 * Get the To value
	 *
	 * @return string
	 */
	public function getTo()
	{
		return $this->to;
	}
	/**
	 * Get the Subject
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}
	/**
	 * Get the Message Value who is genered in mime value
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->getMimeMessage();
	}
	/**
	 * Get the Header Value
	 *
	 * @return string
	 */
	public function getHeader()
	{
		return $this->generateHeader();
	}
	/**
	 * Send the mail
	 *
	 * @return bool
	 */
	public function send()
	{
		$message = $this->getMimeMessage();
		$headers = $this->generateHeader();
		if (!(empty($this->to) || empty($this->subject) || empty($message)))
			return @mail($this->to, $this->subject, $message, $headers);
	}
	/**
	 * Validate a Mail
	 *
	 * @param string $email
	 * @return bool
	 */
	public function isMail($email)
	{
		return ereg("([A-Za-z0-9]|-|_|\.)*@([A-Za-z0-9]|-|_|\.)*\.([A-Za-z0-9]|-|_|\.)*",$email);
	}
	/**
	 * Get the Boundary
	 *
	 * @return string
	 */
	private function generateBoundary()
	{
		return md5(uniqid(time()));
	}
	/**
	 * Generate Mime Headers
	 *
	 * @return void
	 */
	private function getMimeHeader()
	{
		if (!empty($this->files)) //Uses Mime Multipart
		{
		    $this->setHeader('MIME-Version', '1.0');
			$this->setHeader('Content-Type', 'multipart/mixed; boundary=\"==Mixed_Boundary_x'.$this->boundary.'x\"');
		}
		elseif (!empty($this->messageHtml) && !empty($this->messageText))
		{
			$this->setHeader('MIME-Version', '1.0');
			$this->setHeader('Content-Type', 'multipart/alternative; boundary=\"==Alternative_Boundary_x'.$this->boundary.'x\"');
		}
		else
			if (!empty($this->messageHtml))
				$this->setHeader('Content-Type', 'text/html');
			else
				$this->setHeader('Content-Type', 'text/plain');
	}
	/**
	 * Return the Message encoded.
	 *
	 * @return void
	 */
	private function getMimeMessage()
	{
		if (!empty($this->files)) //Uses Mime Multipart
		{
			$this->boundary = $this->generateBoundary();
			$this->setHeader('MIME-Version', '1.0');
			$this->setHeader('Content-Type', 'multipart/mixed; boundary=\"==Mixed_Boundary_x'.$this->boundary.'x\"');
			$this->message = $this->generatePart('multipart/alternative; boundary=\"==Alternative_Boundary_x'.$this->boundary.'x', $this->getMultipartMessage());
			$this->message .= $this->generateFiles();
			$this->message .= "--==Mixed_Boundary_x{$limite}x--\n";
		}
		elseif (!empty($this->messageHtml) && !empty($this->messageText))
		{
			$this->boundary = $this->generateBoundary();
			$this->setHeader('MIME-Version', '1.0');
			$this->setHeader('Content-Type', 'multipart/alternative; boundary=\"==Alternative_Boundary_x'.$this->boundary.'x');
			$this->message = $this->getMultipartMessage();
		}
		else
		{
			if (!empty($this->messageHtml))
			{
				$this->setHeader('Content-Type', 'text/html');
				$this->message = $this->messageHtml;
			}
			else
			{
				$this->setHeader('Content-Type', 'text/plain');
				$this->message = $this->messageText;
			}
		}
		return $this->message;
	}
	/**
	 * generate the Multipart message with html and text message
	 *
	 */
	private function getMultipartMessage()
	{
		if (!empty($this->messageText))
		{
			$content .= "\n This is a multi-part message in MIME format. \n\n";
			$content .= "--==Alternative_Boundary_x".$this->boundary."x\n";
			$content .= 'Content-type:text/plain; charset=iso-8859-15' . "\n";
			$content .= "Content-transfer-encoding:8bit\n\n";
			$content .= $this->messageText."\r\n";
		}
		if (!empty($this->messageHtml))
		{
			$content .= "\n--==Alternative_Boundary_x".$this->boundary."x\n";
			$content .= 'Content-type:text/plain; charset=iso-8859-15' . "\n";
			$content .= "Content-transfer-encoding:8bit\n\n";
			$content .= $this->messageHtml."\r\n";
		}
		$content .= "\n--==Alternative_Boundary_x".$this->boundary."x--\n\n";
		return $content;
	}
	/**
	 * Generate the Header Type
	 *
	 * @return string
	 */
	private function generateHeader()
	{
		$header = '';
		getMimeHeader();
		foreach ($this->header as $name => $value)
			$header = $name.': '. $value . "\n";
		return $header;
	}
	/**
	 * Generate the attach files
	 *
	 * @return unknown
	 */
	private function generateFiles()
	{
		$attach = '';
		foreach ($this->files as $name => $file)
		{
			if (file_exists($file))
			{
				$taille = filesize($file);
				$type = filetype($file);
     			$f = @fopen($file, "r");
				$code = fread($f, $taille);
				$code = chunk_split(base64_encode($code));
				$attach .= $this->generatePart($type . "; name=\"$name\"", $code, array('Content-Disposition' => "attachment; filename=\"$name\"", 'Content-transfer-encoding' => 'base64'));
			}
		}
		return $attach;
	}
	/**
	 * generate a Multipart
	 *
	 * @param string $type
	 * @param string $content
	 * @param array $headers
	 * @return string
	 */
	private function generatePart($type, $content, $headers)
	{
		$part .= "--==Mixed_Boundary_x".$this->boundary."x\n";
		$part .= 'Content-Type: ' . $type ."\n";
		foreach ($this->header as $name => $value)
			$part .= $name.': '. $value . "\n";
		$part .= "\n".$content;
		return $part;
	}
}
?>