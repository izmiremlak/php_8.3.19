<?php
/*~ class.phpmailer.php
.---------------------------------------------------------------------------.
|  Software: PHPMailer - PHP email class                                    |
|   Version: 5.2.4                                                          |
|      Site: https://code.google.com/a/apache-extras.org/p/phpmailer/       |
| ------------------------------------------------------------------------- |
|     Admin: Jim Jagielski (project admininistrator)                        |
|   Authors: Andy Prevost (codeworxtech) codeworxtech@users.sourceforge.net |
|          : Marcus Bointon (coolbru) coolbru@users.sourceforge.net         |
|          : Jim Jagielski (jimjag) jimjag@gmail.com                        |
|   Founder: Brent R. Matzelle (original founder)                           |
| Copyright (c) 2010-2012, Jim Jagielski. All Rights Reserved.              |
| Copyright (c) 2004-2009, Andy Prevost. All Rights Reserved.               |
| Copyright (c) 2001-2003, Brent R. Matzelle                                |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Lesser General Public License (LGPL)     |
|            http://www.gnu.org/copyleft/lesser.html                        |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/

/**
 * PHPMailer - PHP email creation and transport class
 * NOTE: Requires PHP version 8.3.17 or later
 * @package PHPMailer
 * @author Andy Prevost
 * @author Marcus Bointon
 * @author Jim Jagielski
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

if (version_compare(PHP_VERSION, '8.3.17', '<')) {
    exit("Sorry, this version of PHPMailer will only run on PHP version 8.3.17 or greater!\n");
}

/**
 * PHP email creation and transport class
 * @package PHPMailer
 */
class PHPMailer {

    /////////////////////////////////////////////////
    // PROPERTIES, PUBLIC
    /////////////////////////////////////////////////

    /**
     * Email priority (1 = High, 3 = Normal, 5 = low).
     * @var int
     */
    public int $Priority = 3;

    /**
     * Sets the CharSet of the message.
     * @var string
     */
    public string $CharSet = 'utf-8';

    /**
     * Sets the Content-type of the message.
     * @var string
     */
    public string $ContentType = 'text/plain';

    /**
     * Sets the Encoding of the message. Options for this are
     *  "8bit", "7bit", "binary", "base64", and "quoted-printable".
     * @var string
     */
    public string $Encoding = '8bit';

    /**
     * Holds the most recent mailer error message.
     * @var string
     */
    public string $ErrorInfo = '';

    /**
     * Sets the From email address for the message.
     * @var string
     */
    public string $From = 'root@localhost';

    /**
     * Sets the From name of the message.
     * @var string
     */
    public string $FromName = 'Root User';

    /**
     * Sets the Sender email (Return-Path) of the message.  If not empty,
     * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string
     */
    public string $Sender = '';

    /**
     * Sets the Return-Path of the message.  If empty, it will
     * be set to either From or Sender.
     * @var string
     */
    public string $ReturnPath = '';

    /**
     * Sets the Subject of the message.
     * @var string
     */
    public string $Subject = '';

    /**
     * Sets the Body of the message.  This can be either an HTML or text body.
     * If HTML then run IsHTML(true).
     * @var string
     */
    public string $Body = '';

    /**
     * Sets the text-only body of the message.  This automatically sets the
     * email to multipart/alternative.  This body can be read by mail
     * clients that do not have HTML email capability such as mutt. Clients
     * that can read HTML will view the normal Body.
     * @var string
     */
    public string $AltBody = '';

    /**
     * Stores the complete compiled MIME message body.
     * @var string
     * @access protected
     */
    protected string $MIMEBody = '';

    /**
     * Stores the complete compiled MIME message headers.
     * @var string
     * @access protected
     */
    protected string $MIMEHeader = '';

    /**
     * Stores the extra header list which CreateHeader() doesn't fold in
     * @var string
     * @access protected
     */
    protected string $mailHeader = '';

    /**
     * Sets word wrapping on the body of the message to a given number of
     * characters.
     * @var int
     */
    public int $WordWrap = 0;

    /**
     * Method to send mail: ("mail", "sendmail", or "smtp").
     * @var string
     */
    public string $Mailer = 'mail';

    /**
     * Sets the path of the sendmail program.
     * @var string
     */
    public string $Sendmail = '/usr/sbin/sendmail';

    /**
     * Determine if mail() uses a fully sendmail compatible MTA that
     * supports sendmail's "-oi -f" options
     * @var bool
     */
    public bool $UseSendmailOptions = true;

    /**
     * Path to PHPMailer plugins.  Useful if the SMTP class
     * is in a different directory than the PHP include path.
     * @var string
     */
    public string $PluginDir = '';

    /**
     * Sets the email address that a reading confirmation will be sent.
     * @var string
     */
    public string $ConfirmReadingTo = '';

    /**
     * Sets the hostname to use in Message-Id and Received headers
     * and as default HELO string. If empty, the value returned
     * by SERVER_NAME is used or 'localhost.localdomain'.
     * @var string
     */
    public string $Hostname = '';

    /**
     * Sets the message ID to be used in the Message-Id header.
     * If empty, a unique id will be generated.
     * @var string
     */
    public string $MessageID = '';

    /**
     * Sets the message Date to be used in the Date header.
     * If empty, the current date will be added.
     * @var string
     */
    public string $MessageDate = '';

    /////////////////////////////////////////////////
    // PROPERTIES FOR SMTP
    /////////////////////////////////////////////////

    /**
     * Sets the SMTP hosts.
     *
     * All hosts must be separated by a
     * semicolon.  You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * Hosts will be tried in order.
     * @var string
     */
    public string $Host = 'localhost';

    /**
     * Sets the default SMTP server port.
     * @var int
     */
    public int $Port = 25;

    /**
     * Sets the SMTP HELO of the message (Default is $Hostname).
     * @var string
     */
    public string $Helo = '';

    /**
     * Sets connection prefix. Options are "", "ssl" or "tls"
     * @var string
     */
    public string $SMTPSecure = '';

    /**
     * Sets SMTP authentication. Utilizes the Username and Password variables.
     * @var bool
     */
    public bool $SMTPAuth = false;

    /**
     * Sets SMTP username.
     * @var string
     */
    public string $Username = '';

    /**
     * Sets SMTP password.
     * @var string
     */
    public string $Password = '';

    /**
     *  Sets SMTP auth type. Options are LOGIN | PLAIN | NTLM  (default LOGIN)
     *  @var string
     */
    public string $AuthType = '';

    /**
     *  Sets SMTP realm.
     *  @var string
     */
    public string $Realm = '';

    /**
     *  Sets SMTP workstation.
     *  @var string
     */
    public string $Workstation = '';

    /**
     * Sets the SMTP server timeout in seconds.
     * This function will not work with the win32 version.
     * @var int
     */
    public int $Timeout = 10;

    /**
     * Sets SMTP class debugging on or off.
     * @var bool
     */
    public bool $SMTPDebug = false;

    /**
     * Sets the function/method to use for debugging output.
     * Right now we only honor "echo" or "error_log"
     * @var string
     */
    public string $Debugoutput = "echo";

    /**
     * Prevents the SMTP connection from being closed after each mail
     * sending.  If this is set to true then to close the connection
     * requires an explicit call to SmtpClose().
     * @var bool
     */
    public bool $SMTPKeepAlive = false;

    /**
     * Provides the ability to have the TO field process individual
     * emails, instead of sending to entire TO addresses
     * @var bool
     */
    public bool $SingleTo = false;

    /**
     * If SingleTo is true, this provides the array to hold the email addresses
     * @var array
     */
    public array $SingleToArray = [];

    /**
     * Provides the ability to change the generic line ending
     * NOTE: The default remains '\n'. We force CRLF where we KNOW
     *        it must be used via self::CRLF
     * @var string
     */
    public string $LE = "\n";

    /**
     * Used with DKIM Signing
     * required parameter if DKIM is enabled
     *
     * domain selector example domainkey
     * @var string
     */
    public string $DKIM_selector = '';

    /**
     * Used with DKIM Signing
     * required if DKIM is enabled, in format of email address 'you@yourdomain.com' typically used as the source of the email
     * @var string
     */
    public string $DKIM_identity = '';

    /**
     * Used with DKIM Signing
     * optional parameter if your private key requires a passphrase
     * @var string
     */
    public string $DKIM_passphrase = '';

    /**
     * Used with DKIM Signing
     * required if DKIM is enabled, in format of email address 'domain.com'
     * @var string
     */
    public string $DKIM_domain = '';

    /**
     * Used with DKIM Signing
     * required if DKIM is enabled, path to private key file
     * @var string
     */
    public string $DKIM_private = '';

    /**
     * Callback Action function name.
     * The function that handles the result of the send email action.
     * It is called out by Send() for each email sent.
     *
     * Value can be:
     * - 'function_name' for function names
     * - 'Class::Method' for static method calls
     * - array($object, 'Method') for calling methods on $object
     * See http://php.net/is_callable manual page for more details.
     *
     * Parameters:
     *   bool    $result        result of the send action
     *   string  $to            email address of the recipient
     *   string  $cc            cc email addresses
     *   string  $bcc           bcc email addresses
     *   string  $subject       the subject
     *   string  $body          the email body
     *   string  $from          email address of sender
     * @var string
     */
    public string $action_function = ''; //'callbackAction';

    /**
     * Sets the PHPMailer Version number
     * @var string
     */
    public string $Version = '5.2.4';

    /**
     * What to use in the X-Mailer header
     * @var string NULL for default, whitespace for None, or actual string to use
     */
    public string $XMailer = '';
	
	
///////////////////////////////////////////////
/// PROPERTIES, PRIVATE AND PROTECTED
///////////////////////////////////////////////

/**
 * @var SMTP An instance of the SMTP sender class
 * @access protected
 */
protected $smtp = null;

/**
 * @var array An array of 'to' addresses
 * @access protected
 */
protected array $to = [];

/**
 * @var array An array of 'cc' addresses
 * @access protected
 */
protected array $cc = [];

/**
 * @var array An array of 'bcc' addresses
 * @access protected
 */
protected array $bcc = [];

/**
 * @var array An array of reply-to name and address
 * @access protected
 */
protected array $ReplyTo = [];

/**
 * @var array An array of all kinds of addresses: to, cc, bcc, replyto
 * @access protected
 */
protected array $all_recipients = [];

/**
 * @var array An array of attachments
 * @access protected
 */
protected array $attachment = [];

/**
 * @var array An array of custom headers
 * @access protected
 */
protected array $CustomHeader = [];

/**
 * @var string The message's MIME type
 * @access protected
 */
protected string $message_type = '';

/**
 * @var array An array of MIME boundary strings
 * @access protected
 */
protected array $boundary = [];

/**
 * @var array An array of available languages
 * @access protected
 */
protected array $language = [];

/**
 * @var int The number of errors encountered
 * @access protected
 */
protected int $error_count = 0;

/**
 * @var string The filename of a DKIM certificate file
 * @access protected
 */
protected string $sign_cert_file = '';

/**
 * @var string The filename of a DKIM key file
 * @access protected
 */
protected string $sign_key_file = '';

/**
 * @var string The password of a DKIM key
 * @access protected
 */
protected string $sign_key_pass = '';

/**
 * @var bool Whether to throw exceptions for errors
 * @access protected
 */
protected bool $exceptions = false;

///////////////////////////////////////////////
/// CONSTANTS
///////////////////////////////////////////////

const STOP_MESSAGE = 0; // message only, continue processing
const STOP_CONTINUE = 1; // message?, likely ok to continue processing
const STOP_CRITICAL = 2; // message, plus full stop, critical error reached
const CRLF = "\r\n"; // SMTP RFC specified EOL

///////////////////////////////////////////////
/// METHODS, VARIABLES
///////////////////////////////////////////////

/**
 * Calls actual mail() function, but in a safe_mode aware fashion
 * Also, unless sendmail_path points to sendmail (or something that
 * claims to be sendmail), don't pass params (not a perfect fix,
 * but it will do)
 * @param string $to To
 * @param string $subject Subject
 * @param string $body Message Body
 * @param string $header Additional Header(s)
 * @param string $params Params
 * @access private
 * @return bool
 */
private function mail_passthru(string $to, string $subject, string $body, string $header, string $params): bool {
    if (ini_get('safe_mode') || !$this->UseSendmailOptions) {
        return @mail($to, $this->EncodeHeader($this->SecureHeader($subject)), $body, $header);
    } else {
        return @mail($to, $this->EncodeHeader($this->SecureHeader($subject)), $body, $header, $params);
    }
}

/**
 * Outputs debugging info via user-defined method
 * @param string $str
 */
private function edebug(string $str): void {
    if ($this->Debugoutput == "error_log") {
        error_log($str);
    } else {
        echo $str;
    }
}

/**
 * Constructor
 * @param bool $exceptions Should we throw external exceptions?
 */
public function __construct(bool $exceptions = false) {
    $this->exceptions = $exceptions;
}

/**
 * Sets message type to HTML.
 * @param bool $ishtml
 * @return void
 */
public function IsHTML(bool $ishtml = true): void {
    $this->ContentType = $ishtml ? 'text/html' : 'text/plain';
}

/**
 * Sets Mailer to send message using SMTP.
 * @return void
 */
public function IsSMTP(): void {
    $this->Mailer = 'smtp';
}

/**
 * Sets Mailer to send message using PHP mail() function.
 * @return void
 */
public function IsMail(): void {
    $this->Mailer = 'mail';
}

/**
 * Sets Mailer to send message using the $Sendmail program.
 * @return void
 */
public function IsSendmail(): void {
    if (!stristr(ini_get('sendmail_path'), 'sendmail')) {
        $this->Sendmail = '/var/qmail/bin/sendmail';
    }
    $this->Mailer = 'sendmail';
}

/**
 * Sets Mailer to send message using the qmail MTA.
 * @return void
 */
public function IsQmail(): void {
    if (stristr(ini_get('sendmail_path'), 'qmail')) {
        $this->Sendmail = '/var/qmail/bin/sendmail';
    }
    $this->Mailer = 'sendmail';
}

///////////////////////////////////////////////
/// METHODS, MAIL SENDING
///////////////////////////////////////////////

/**
 * Creates message and assigns Mailer. If the message is
 * not sent successfully then it returns false. Use the ErrorInfo
 * variable to view description of the error.
 * @throws phpmailerException
 * @return bool
 */
public function Send(): bool {
    try {
        if (!$this->PreSend()) return false;
        return $this->PostSend();
    } catch (phpmailerException $e) {
        $this->mailHeader = '';
        $this->SetError($e->getMessage());
        if ($this->exceptions) {
            throw $e;
        }
        return false;
    }
}

/**
 * Prep mail by constructing all message entities
 * @throws phpmailerException
 * @return bool
 */
public function PreSend(): bool {
    try {
        $this->mailHeader = "";
        if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
            throw new phpmailerException($this->Lang('provide_address'), self::STOP_CRITICAL);
        }

        // Set whether the message is multipart/alternative
        if (!empty($this->AltBody)) {
            $this->ContentType = 'multipart/alternative';
        }

        $this->error_count = 0; // reset errors
        $this->SetMessageType();
        // Refuse to send an empty message
        if (empty($this->Body)) {
            throw new phpmailerException($this->Lang('empty_message'), self::STOP_CRITICAL);
        }

        $this->MIMEHeader = $this->CreateHeader();
        $this->MIMEBody = $this->CreateBody();

        // To capture the complete message when using mail(), create an extra header list which CreateHeader() doesn't fold in
        if ($this->Mailer == 'mail') {
            if (count($this->to) > 0) {
                $this->mailHeader .= $this->AddrAppend("To", $this->to);
            } else {
                $this->mailHeader .= $this->HeaderLine("To", "undisclosed-recipients:;");
            }
            $this->mailHeader .= $this->HeaderLine('Subject', $this->EncodeHeader($this->SecureHeader(trim($this->Subject))));
        }

        // Digitally sign with DKIM if enabled
        if (!empty($this->DKIM_domain) && !empty($this->DKIM_private) && !empty($this->DKIM_selector) && !empty($this->DKIM_domain) && file_exists($this->DKIM_private)) {
            $header_dkim = $this->DKIM_Add($this->MIMEHeader, $this->EncodeHeader($this->SecureHeader($this->Subject)), $this->MIMEBody);
            $this->MIMEHeader = str_replace("\r\n", "\n", $header_dkim) . $this->MIMEHeader;
        }

        return true;

    } catch (phpmailerException $e) {
        $this->SetError($e->getMessage());
        if ($this->exceptions) {
            throw $e;
        }
        return false;
    }
}

/**
 * Actual Email transport function
 * Send the email via the selected mechanism
 * @throws phpmailerException
 * @return bool
 */
public function PostSend(): bool {
    try {
        // Choose the mailer and send through it
        switch ($this->Mailer) {
            case 'sendmail':
                return $this->SendmailSend($this->MIMEHeader, $this->MIMEBody);
            case 'smtp':
                return $this->SmtpSend($this->MIMEHeader, $this->MIMEBody);
            case 'mail':
                return $this->MailSend($this->MIMEHeader, $this->MIMEBody);
            default:
                return $this->MailSend($this->MIMEHeader, $this->MIMEBody);
        }
    } catch (phpmailerException $e) {
        $this->SetError($e->getMessage());
        if ($this->exceptions) {
            throw $e;
        }
        if ($this->SMTPDebug) {
            $this->edebug($e->getMessage() . "\n");
        }
    }
    return false;
}

/**
 * Sends mail using the $Sendmail program.
 * @param string $header The message headers
 * @param string $body The message body
 * @throws phpmailerException
 * @access protected
 * @return bool
 */
protected function SendmailSend(string $header, string $body): bool {
    if ($this->Sender != '') {
        $sendmail = sprintf("%s -oi -f%s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
    } else {
        $sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
    }
    if ($this->SingleTo === true) {
        foreach ($this->SingleToArray as $val) {
            if (!@$mail = popen($sendmail, 'w')) {
                throw new phpmailerException($this->Lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
            fputs($mail, "To: " . $val . "\n");
            fputs($mail, $header);
            fputs($mail, $body);
            $result = pclose($mail);
            // Implement call back function if it exists
            $isSent = ($result == 0) ? 1 : 0;
            $this->doCallback($isSent, $val, $this->cc, $this->bcc, $this->Subject, $body);
            if ($result != 0) {
                throw new phpmailerException($this->Lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
            }
        }
    } else {
        if (!@$mail = popen($sendmail, 'w')) {
            throw new phpmailerException($this->Lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
        }
        fputs($mail, $header);
        fputs($mail, $body);
        $result = pclose($mail);
        // Implement call back function if it exists
        $isSent = ($result == 0) ? 1 : 0;
        $this->doCallback($isSent, $this->to, $this->cc, $this->bcc, $this->Subject, $body);
        if ($result != 0) {
            throw new phpmailerException($this->Lang('execute') . $this->Sendmail, self::STOP_CRITICAL);
        }
    }
    return true;
}

/**
 * Sends mail using the PHP mail() function.
 * @param string $header The message headers
 * @param string $body The message body
 * @throws phpmailerException
 * @access protected
 * @return bool
 */
protected function MailSend(string $header, string $body): bool {
    $toArr = array();
    foreach ($this->to as $t) {
        $toArr[] = $this->AddrFormat($t);
    }
    $to = implode(', ', $toArr);

    if (empty($this->Sender)) {
        $params = "-oi ";
    } else {
        $params = sprintf("-oi -f%s", $this->Sender);
    }
    if ($this->Sender != '' && !ini_get('safe_mode')) {
        $old_from = ini_get('sendmail_from');
        ini_set('sendmail_from', $this->Sender);
    }
    $rt = false;
    if ($this->SingleTo === true && count($toArr) > 1) {
        foreach ($toArr as $val) {
            $rt = $this->mail_passthru($val, $this->Subject, $body, $header, $params);
            // Implement call back function if it exists
            $isSent = ($rt == 1) ? 1 : 0;
            $this->doCallback($isSent, $val, $this->cc, $this->bcc, $this->Subject, $body);
        }
    } else {
        $rt = $this->mail_passthru($to, $this->Subject, $body, $header, $params);
        // Implement call back function if it exists
        $isSent = ($rt == 1) ? 1 : 0;
        $this->doCallback($isSent, $to, $this->cc, $this->bcc, $this->Subject, $body);
    }
    if (isset($old_from)) {
        ini_set('sendmail_from', $old_from);
    }
    if (!$rt) {
        throw new phpmailerException($this->Lang('instantiate'), self::STOP_CRITICAL);
    }
    return true;
}

/**
 * Sends mail via SMTP using PhpSMTP
 * Returns false if there is a bad MAIL FROM, RCPT, or DATA input.
 * @param string $header The message headers
 * @param string $body The message body
 * @throws phpmailerException
 * @uses SMTP
 * @access protected
 * @return bool
 */
protected function SmtpSend(string $header, string $body): bool {
    require_once $this->PluginDir . 'class.smtp.php';
    $bad_rcpt = array();

    if (!$this->SmtpConnect()) {
        throw new phpmailerException($this->Lang('smtp_connect_failed'), self::STOP_CRITICAL);
    }
    $smtp_from = ($this->Sender == '') ? $this->From : $this->Sender;
    if (!$this->smtp->Mail($smtp_from)) {
        $this->SetError($this->Lang('from_failed') . $smtp_from . " : " . implode(",", $this->smtp->getError()));
        throw new phpmailerException($this->ErrorInfo, self::STOP_CRITICAL);
    }

    // Attempt to send attach all recipients
    foreach ($this->to as $to) {
        if (!$this->smtp->Recipient($to[0])) {
            $bad_rcpt[] = $to[0];
            // Implement call back function if it exists
            $isSent = 0;
            $this->doCallback($isSent, $to[0], '', '', $this->Subject, $body);
        } else {
            // Implement call back function if it exists
            $isSent = 1;
            $this->doCallback($isSent, $to[0], '', '', $this->Subject, $body);
        }
    }
    foreach ($this->cc as $cc) {
        if (!$this->smtp->Recipient($cc[0])) {
            $bad_rcpt[] = $cc[0];
            // Implement call back function if it exists
            $isSent = 0;
            $this->doCallback($isSent, '', $cc[0], '', $this->Subject, $body);
        } else {
            // Implement call back function if it exists
            $isSent = 1;
            $this->doCallback($isSent, '', $cc[0], '', $this->Subject, $body);
        }
    }
    foreach ($this->bcc as $bcc) {
        if (!$this->smtp->Recipient($bcc[0])) {
            $bad_rcpt[] = $bcc[0];
            // Implement call back function if it exists
            $isSent = 0;
            $this->doCallback($isSent, '', '', $bcc[0], $this->Subject, $body);
        } else {
            // Implement call back function if it exists
            $isSent = 1;
            $this->doCallback($isSent, '', '', $bcc[0], $this->Subject, $body);
        }
    }

    if (count($bad_rcpt) > 0) { // Create error message for any bad addresses
        $badaddresses = implode(', ', $bad_rcpt);
        throw new phpmailerException($this->Lang('recipients_failed') . $badaddresses);
    }
    if (!$this->smtp->Data($header . $body)) {
        throw new phpmailerException($this->Lang('data_not_accepted'), self::STOP_CRITICAL);
    }
    if ($this->SMTPKeepAlive == true) {
        $this->smtp->Reset();
    } else {
        $this->smtp->Quit();
        $this->smtp->Close();
    }
    return true;
}
	
 /**
     * Initiates a connection to an SMTP server.
     * Returns false if the operation failed.
     * @uses SMTP
     * @access public
     * @throws phpmailerException
     * @return bool
     */
    public function SmtpConnect(): bool {
        if (is_null($this->smtp)) {
            $this->smtp = new SMTP;
        }

        $this->smtp->Timeout = $this->Timeout;
        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts = explode(';', $this->Host);
        $index = 0;
        $connection = $this->smtp->Connected();

        // Retry while there is no connection
        try {
            while ($index < count($hosts) && !$connection) {
                $hostinfo = array();
                if (preg_match('/^(.+):([0-9]+)$/', $hosts[$index], $hostinfo)) {
                    $host = $hostinfo[1];
                    $port = $hostinfo[2];
                } else {
                    $host = $hosts[$index];
                    $port = $this->Port;
                }

                $tls = ($this->SMTPSecure == 'tls');
                $ssl = ($this->SMTPSecure == 'ssl');

                if ($this->smtp->Connect(($ssl ? 'ssl://' : '') . $host, $port, $this->Timeout)) {

                    $hello = ($this->Helo != '' ? $this->Helo : $this->ServerHostname());
                    $this->smtp->Hello($hello);

                    if ($tls) {
                        if (!$this->smtp->StartTLS()) {
                            throw new phpmailerException($this->Lang('connect_host'));
                        }

                        // We must resend HELO after tls negotiation
                        $this->smtp->Hello($hello);
                    }

                    $connection = true;
                    if ($this->SMTPAuth) {
                        if (!$this->smtp->Authenticate($this->Username, $this->Password, $this->AuthType,
                            $this->Realm, $this->Workstation)) {
                            throw new phpmailerException($this->Lang('authenticate'));
                        }
                    }
                }
                $index++;
                if (!$connection) {
                    throw new phpmailerException($this->Lang('connect_host'));
                }
            }
        } catch (phpmailerException $e) {
            $this->smtp->Reset();
            if ($this->exceptions) {
                throw $e;
            }
        }
        return true;
    }

    /**
     * Closes the active SMTP session if one exists.
     * @return void
     */
    public function SmtpClose(): void {
        if ($this->smtp !== null) {
            if ($this->smtp->Connected()) {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }

    /**
     * Sets the language for all class error messages.
     * Returns false if it cannot load the language file. The default language is English.
     * @param string $langcode ISO 639-1 2-character language code (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @return bool
     * @access public
     */
    public function SetLanguage(string $langcode = 'en', string $lang_path = 'language/'): bool {
        // Define full set of translatable strings
        $PHPMAILER_LANG = array(
            'authenticate' => 'SMTP Error: Could not authenticate.',
            'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: Data not accepted.',
            'empty_message' => 'Message body empty',
            'encoding' => 'Unknown encoding: ',
            'execute' => 'Could not execute: ',
            'file_access' => 'Could not access file: ',
            'file_open' => 'File Error: Could not open file: ',
            'from_failed' => 'The following From address failed: ',
            'instantiate' => 'Could not instantiate mail function.',
            'invalid_address' => 'Invalid address',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address' => 'You must provide at least one recipient email address.',
            'recipients_failed' => 'SMTP Error: The following recipients failed: ',
            'signing' => 'Signing Error: ',
            'smtp_connect_failed' => 'SMTP Connect() failed.',
            'smtp_error' => 'SMTP server error: ',
            'variable_set' => 'Cannot set or reset variable: '
        );
        // Overwrite language-specific strings. This way we'll never have missing translations - no more "language string failed to load"!
        $l = true;
        if ($langcode != 'en') { // There is no English translation file
            $l = @include $lang_path . 'phpmailer.lang-' . $langcode . '.php';
        }
        $this->language = $PHPMAILER_LANG;
        return $l;
    }

    /**
     * Return the current array of language strings.
     * @return array
     */
    public function GetTranslations(): array {
        return $this->language;
    }

    /////////////////////////////////////////////////
    // METHODS, MESSAGE CREATION
    /////////////////////////////////////////////////

    /**
     * Creates recipient headers.
     * @access public
     * @param string $type
     * @param array $addr
     * @return string
     */
    public function AddrAppend(string $type, array $addr): string {
        $addr_str = $type . ': ';
        $addresses = array();
        foreach ($addr as $a) {
            $addresses[] = $this->AddrFormat($a);
        }
        $addr_str .= implode(', ', $addresses);
        $addr_str .= $this->LE;

        return $addr_str;
    }

    /**
     * Formats an address correctly.
     * @access public
     * @param array $addr
     * @return string
     */
    public function AddrFormat(array $addr): string {
        if (empty($addr[1])) {
            return $this->SecureHeader($addr[0]);
        } else {
            return $this->EncodeHeader($this->SecureHeader($addr[1]), 'phrase') . " <" . $this->SecureHeader($addr[0]) . ">";
        }
    }

    /**
     * Wraps message for use with mailers that do not
     * automatically perform wrapping and for quoted-printable.
     * @param string $message The message to wrap
     * @param int $length The line length to wrap to
     * @param bool $qp_mode Whether to run in Quoted-Printable mode
     * @access public
     * @return string
     */
    public function WrapText(string $message, int $length, bool $qp_mode = false): string {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;
        // If utf-8 encoding is used, we will need to make sure we don't split multibyte characters when we wrap
        $is_utf8 = (strtolower($this->CharSet) == "utf-8");
        $lelen = strlen($this->LE);
        $crlflen = strlen(self::CRLF);

        $message = $this->FixEOL($message);
        if (substr($message, -$lelen) == $this->LE) {
            $message = substr($message, 0, -$lelen);
        }

        $line = explode($this->LE, $message);   // Magic. We know FixEOL uses $LE
        $message = '';
        for ($i = 0 ;$i < count($line); $i++) {
            $line_part = explode(' ', $line[$i]);
            $buf = '';
            for ($e = 0; $e<count($line_part); $e++) {
                $word = $line_part[$e];
                if ($qp_mode and (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - $crlflen;
                    if ($e != 0) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this->UTF8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) == "=") {
                                $len--;
                            } elseif (substr($word, $len - 2, 1) == "=") {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf("=%s", self::CRLF);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this->UTF8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) == "=") {
                            $len--;
                        } elseif (substr($word, $len - 2, 1) == "=") {
                            $len -= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) > 0) {
                            $message .= $part . sprintf("=%s", self::CRLF);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    $buf .= ($e == 0) ? $word : (' ' . $word);

                    if (strlen($buf) > $length and $buf_o != '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
            }
            $message .= $buf . self::CRLF;
        }

        return $message;
    }

    /**
     * Finds last character boundary prior to maxLength in a utf-8
     * quoted (printable) encoded string.
     * @param string $encodedText utf-8 QP text
     * @param int $maxLength find last character boundary prior to this length
     * @access public
     * @return int
     */
    public function UTF8CharBoundary(string $encodedText, int $maxLength): int {
        $foundSplitPos = false;
        $lookBack = 3;
        while (!$foundSplitPos) {
            $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, "=");
            if ($encodedCharPos !== false) {
                // Found start of encoded character byte within $lookBack block.
                // Check the encoded byte value (the 2 chars after the '=')
                $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec < 128) { // Single byte character.
                    // If the encoded char was found at pos 0, it will fit
                    // otherwise reduce maxLength to start of the encoded char
                    $maxLength = ($encodedCharPos == 0) ? $maxLength :
                    $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec >= 192) { // First byte of a multi byte character
                    // Reduce maxLength to split at start of character
                    $maxLength = $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec < 192) { // Middle byte of a multi byte character, look further back
                    $lookBack += 3;
                }
            } else {
                // No encoded character found
                $foundSplitPos = true;
            }
        }
        return $maxLength;
    }

    /**
     * Set the body wrapping.
     * @access public
     * @return void
     */
    public function SetWordWrap(): void {
        if ($this->WordWrap < 1) {
            return;
        }

        switch ($this->message_type) {
            case 'alt':
            case 'alt_inline':
            case 'alt_attach':
            case 'alt_inline_attach':
                $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
                break;
            default:
                $this->Body = $this->WrapText($this->Body, $this->WordWrap);
                break;
        }
    }

    /**
     * Assembles message header.
     * @access public
     * @return string The assembled header
     */
    public function CreateHeader(): string {
        $result = '';

        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = 'b1_' . $uniq_id;
        $this->boundary[2] = 'b2_' . $uniq_id;
        $this->boundary[3] = 'b3_' . $uniq_id;

        if ($this->MessageDate == '') {
            $result .= $this->HeaderLine('Date', self::RFCDate());
        } else {
            $result .= $this->HeaderLine('Date', $this->MessageDate);
        }

        if ($this->ReturnPath) {
            $result .= $this->HeaderLine('Return-Path', trim($this->ReturnPath));
        } elseif ($this->Sender == '') {
            $result .= $this->HeaderLine('Return-Path', trim($this->From));
        } else {
            $result .= $this->HeaderLine('Return-Path', trim($this->Sender));
        }

        // To be created automatically by mail()
        if ($this->Mailer != 'mail') {
            if ($this->SingleTo === true) {
                foreach ($this->to as $t) {
                    $this->SingleToArray[] = $this->AddrFormat($t);
                }
            } else {
                if (count($this->to) > 0) {
                    $result .= $this->AddrAppend('To', $this->to);
                } elseif (count($this->cc) == 0) {
                    $result .= $this->HeaderLine('To', 'undisclosed-recipients:;');
                }
            }
        }
		

$from = array();
$from[0][0] = trim($this->From);
$from[0][1] = $this->FromName;
$result .= $this->AddrAppend('From', $from);

// sendmail and mail() extract Cc from the header before sending
if (count($this->cc) > 0) {
    $result .= $this->AddrAppend('Cc', $this->cc);
}

// sendmail and mail() extract Bcc from the header before sending
if ((($this->Mailer == 'sendmail') || ($this->Mailer == 'mail')) && (count($this->bcc) > 0)) {
    $result .= $this->AddrAppend('Bcc', $this->bcc);
}

if (count($this->ReplyTo) > 0) {
    $result .= $this->AddrAppend('Reply-To', $this->ReplyTo);
}

// mail() sets the subject itself
if ($this->Mailer != 'mail') {
    $result .= $this->HeaderLine('Subject', $this->EncodeHeader($this->SecureHeader($this->Subject)));
}

if ($this->MessageID != '') {
    $result .= $this->HeaderLine('Message-ID', $this->MessageID);
} else {
    $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
}
$result .= $this->HeaderLine('X-Priority', $this->Priority);
if ($this->XMailer == '') {
    $result .= $this->HeaderLine('X-Mailer', 'PHPMailer ' . $this->Version . ' (http://code.google.com/a/apache-extras.org/p/phpmailer/)');
} else {
    $myXmailer = trim($this->XMailer);
    if ($myXmailer) {
        $result .= $this->HeaderLine('X-Mailer', $myXmailer);
    }
}

if ($this->ConfirmReadingTo != '') {
    $result .= $this->HeaderLine('Disposition-Notification-To', '<' . trim($this->ConfirmReadingTo) . '>');
}

// Add custom headers
for ($index = 0; $index < count($this->CustomHeader); $index++) {
    $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
}
if (!$this->sign_key_file) {
    $result .= $this->HeaderLine('MIME-Version', '1.0');
    $result .= $this->GetMailMIME();
}

return $result;
}

/**
 * Returns the message MIME.
 * @access public
 * @return string
 */
public function GetMailMIME(): string {
    $result = '';
    switch ($this->message_type) {
        case 'inline':
            $result .= $this->HeaderLine('Content-Type', 'multipart/related;');
            $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
            break;
        case 'attach':
        case 'inline_attach':
        case 'alt_attach':
        case 'alt_inline_attach':
            $result .= $this->HeaderLine('Content-Type', 'multipart/mixed;');
            $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
            break;
        case 'alt':
        case 'alt_inline':
            $result .= $this->HeaderLine('Content-Type', 'multipart/alternative;');
            $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
            break;
        default:
            // Catches case 'plain': and case '':
            $result .= $this->HeaderLine('Content-Transfer-Encoding', $this->Encoding);
            $result .= $this->TextLine('Content-Type: ' . $this->ContentType . '; charset=' . $this->CharSet);
            break;
    }

    if ($this->Mailer != 'mail') {
        $result .= $this->LE;
    }

    return $result;
}

/**
 * Returns the MIME message (headers and body). Only really valid post PreSend().
 * @access public
 * @return string
 */
public function GetSentMIMEMessage(): string {
    return $this->MIMEHeader . $this->mailHeader . self::CRLF . $this->MIMEBody;
}

/**
 * Assembles the message body. Returns an empty string on failure.
 * @access public
 * @throws phpmailerException
 * @return string The assembled message body
 */
public function CreateBody(): string {
    $body = '';

    if ($this->sign_key_file) {
        $body .= $this->GetMailMIME() . $this->LE;
    }

    $this->SetWordWrap();

    switch ($this->message_type) {
        case 'inline':
            $body .= $this->GetBoundary($this->boundary[1], '', '', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->AttachAll("inline", $this->boundary[1]);
            break;
        case 'attach':
            $body .= $this->GetBoundary($this->boundary[1], '', '', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->AttachAll("attachment", $this->boundary[1]);
            break;
        case 'inline_attach':
            $body .= $this->TextLine("--" . $this->boundary[1]);
            $body .= $this->HeaderLine('Content-Type', 'multipart/related;');
            $body .= $this->TextLine("\tboundary=\"" . $this->boundary[2] . '"');
            $body .= $this->LE;
            $body .= $this->GetBoundary($this->boundary[2], '', '', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->AttachAll("inline", $this->boundary[2]);
            $body .= $this->LE;
            $body .= $this->AttachAll("attachment", $this->boundary[1]);
            break;
        case 'alt':
            $body .= $this->GetBoundary($this->boundary[1], '', 'text/plain', '');
            $body .= $this->EncodeString($this->AltBody, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->GetBoundary($this->boundary[1], '', 'text/html', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->EndBoundary($this->boundary[1]);
            break;
        case 'alt_inline':
            $body .= $this->GetBoundary($this->boundary[1], '', 'text/plain', '');
            $body .= $this->EncodeString($this->AltBody, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->TextLine("--" . $this->boundary[1]);
            $body .= $this->HeaderLine('Content-Type', 'multipart/related;');
            $body .= $this->TextLine("\tboundary=\"" . $this->boundary[2] . '"');
            $body .= $this->LE;
            $body .= $this->GetBoundary($this->boundary[2], '', 'text/html', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->AttachAll("inline", $this->boundary[2]);
            $body .= $this->LE;
            $body .= $this->EndBoundary($this->boundary[1]);
            break;
        case 'alt_attach':
            $body .= $this->TextLine("--" . $this->boundary[1]);
            $body .= $this->HeaderLine('Content-Type', 'multipart/alternative;');
            $body .= $this->TextLine("\tboundary=\"" . $this->boundary[2] . '"');
            $body .= $this->LE;
            $body .= $this->GetBoundary($this->boundary[2], '', 'text/plain', '');
            $body .= $this->EncodeString($this->AltBody, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->GetBoundary($this->boundary[2], '', 'text/html', '');
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            $body .= $this->LE . $this->LE;
            $body .= $this->EndBoundary($this->boundary[2]);
            $body .= $this->LE;
            $body .= $this->AttachAll("attachment", $this->boundary[1]);
            break;
        default:
            // catch case 'plain' and case ''
            $body .= $this->EncodeString($this->Body, $this->Encoding);
            break;
    }

    if ($this->IsError()) {
        $body = '';
    } elseif ($this->sign_key_file) {
        try {
            $file = tempnam('', 'mail');
            file_put_contents($file, $body); // TODO check this worked
            $signed = tempnam("", "signed");
            if (@openssl_pkcs7_sign($file, $signed, "file://" . $this->sign_cert_file, array("file://" . $this->sign_key_file, $this->sign_key_pass), NULL)) {
                @unlink($file);
                $body = file_get_contents($signed);
                @unlink($signed);
            } else {
                @unlink($file);
                @unlink($signed);
                throw new phpmailerException($this->Lang("signing") . openssl_error_string());
            }
        } catch (phpmailerException $e) {
            $body = '';
            if ($this->exceptions) {
                throw $e;
            }
        }
    }

    return $body;
}

/**
 * Returns the start of a message boundary.
 * @access protected
 * @param string $boundary
 * @param string $charSet
 * @param string $contentType
 * @param string $encoding
 * @return string
 */
protected function GetBoundary(string $boundary, string $charSet, string $contentType, string $encoding): string {
    $result = '';
    if ($charSet == '') {
        $charSet = $this->CharSet;
    }
    if ($contentType == '') {
        $contentType = $this->ContentType;
    }
    if ($encoding == '') {
        $encoding = $this->Encoding;
    }
    $result .= $this->TextLine('--' . $boundary);
    $result .= sprintf("Content-Type: %s; charset=%s", $contentType, $charSet);
    $result .= $this->LE;
    $result .= $this->HeaderLine('Content-Transfer-Encoding', $encoding);
    $result .= $this->LE;

    return $result;
}

/**
 * Returns the end of a message boundary.
 * @access protected
 * @param string $boundary
 * @return string
 */
protected function EndBoundary(string $boundary): string {
    return $this->LE . '--' . $boundary . '--' . $this->LE;
}

/**
 * Sets the message type.
 * @access protected
 * @return void
 */
protected function SetMessageType(): void {
    $this->message_type = array();
    if ($this->AlternativeExists()) $this->message_type[] = "alt";
    if ($this->InlineImageExists()) $this->message_type[] = "inline";
    if ($this->AttachmentExists()) $this->message_type[] = "attach";
    $this->message_type = implode("_", $this->message_type);
    if ($this->message_type == "") $this->message_type = "plain";
}

/**
 * Returns a formatted header line.
 * @access public
 * @param string $name
 * @param string $value
 * @return string
 */
public function HeaderLine(string $name, string $value): string {
    return $name . ': ' . $value . $this->LE;
}

/**
 * Returns a formatted mail line.
 * @access public
 * @param string $value
 * @return string
 */
public function TextLine(string $value): string {
    return $value . $this->LE;
}

///////////////////////////////////////////////
/// CLASS METHODS, ATTACHMENTS
///////////////////////////////////////////////

/**
 * Adds an attachment from a path on the filesystem.
 * Returns false if the file could not be found
 * or accessed.
 * @param string $path Path to the attachment.
 * @param string $name Overrides the attachment name.
 * @param string $encoding File encoding (see $Encoding).
 * @param string $type File extension (MIME) type.
 * @throws phpmailerException
 * @return bool
 */
public function AddAttachment(string $path, string $name = '', string $encoding = 'base64', string $type = 'application/octet-stream'): bool {
    try {
        if (!@is_file($path)) {
            throw new phpmailerException($this->Lang('file_access') . $path, self::STOP_CONTINUE);
        }
        $filename = basename($path);
        if ($name == '') {
            $name = $filename;
        }

        $this->attachment[] = array(
            0 => $path,
            1 => $filename,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => false,  // isStringAttachment
            6 => 'attachment',
            7 => 0
        );

    } catch (phpmailerException $e) {
        $this->SetError($e->getMessage());
        if ($this->exceptions) {
            throw $e;
        }
        if ($this->SMTPDebug) {
            $this->edebug($e->getMessage() . "\n");
        }
        if ($e->getCode() == self::STOP_CRITICAL) {
            return false;
        }
    }
    return true;
}
	
/**
 * Return the current array of attachments.
 * @return array
 */
public function GetAttachments(): array {
    return $this->attachment;
}

/**
 * Attaches all fs, string, and binary attachments to the message.
 * Returns an empty string on failure.
 * @access protected
 * @param string $disposition_type
 * @param string $boundary
 * @return string
 */
protected function AttachAll(string $disposition_type, string $boundary): string {
    // Return text of body
    $mime = array();
    $cidUniq = array();
    $incl = array();

    // Add all attachments
    foreach ($this->attachment as $attachment) {
        // CHECK IF IT IS A VALID DISPOSITION_FILTER
        if ($attachment[6] == $disposition_type) {
            // Check for string attachment
            $string = '';
            $path = '';
            $bString = $attachment[5];
            if ($bString) {
                $string = $attachment[0];
            } else {
                $path = $attachment[0];
            }

            $inclhash = md5(serialize($attachment));
            if (in_array($inclhash, $incl)) {
                continue;
            }
            $incl[] = $inclhash;
            $filename = $attachment[1];
            $name = $attachment[2];
            $encoding = $attachment[3];
            $type = $attachment[4];
            $disposition = $attachment[6];
            $cid = $attachment[7];
            if ($disposition == 'inline' && isset($cidUniq[$cid])) {
                continue;
            }
            $cidUniq[$cid] = true;

            $mime[] = sprintf("--%s%s", $boundary, $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $this->EncodeHeader($this->SecureHeader($name)), $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if ($disposition == 'inline') {
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
            }

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $this->EncodeHeader($this->SecureHeader($name)), $this->LE . $this->LE);

            // Encode as string attachment
            if ($bString) {
                $mime[] = $this->EncodeString($string, $encoding);
                if ($this->IsError()) {
                    return '';
                }
                $mime[] = $this->LE . $this->LE;
            } else {
                $mime[] = $this->EncodeFile($path, $encoding);
                if ($this->IsError()) {
                    return '';
                }
                $mime[] = $this->LE . $this->LE;
            }
        }
    }

    $mime[] = sprintf("--%s--%s", $boundary, $this->LE);

    return implode("", $mime);
}

/**
 * Encodes attachment in requested format.
 * Returns an empty string on failure.
 * @param string $path The full path to the file
 * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 * @throws phpmailerException
 * @see EncodeFile()
 * @access protected
 * @return string
 */
protected function EncodeFile(string $path, string $encoding = 'base64'): string {
    try {
        if (!is_readable($path)) {
            throw new phpmailerException($this->Lang('file_open') . $path, self::STOP_CONTINUE);
        }
        $magic_quotes = get_magic_quotes_runtime();
        if ($magic_quotes) {
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                set_magic_quotes_runtime(0);
            } else {
                ini_set('magic_quotes_runtime', 0);
            }
        }
        $file_buffer = file_get_contents($path);
        $file_buffer = $this->EncodeString($file_buffer, $encoding);
        if ($magic_quotes) {
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                set_magic_quotes_runtime($magic_quotes);
            } else {
                ini_set('magic_quotes_runtime', $magic_quotes);
            }
        }
        return $file_buffer;
    } catch (Exception $e) {
        $this->SetError($e->getMessage());
        return '';
    }
}

/**
 * Encodes string to requested format.
 * Returns an empty string on failure.
 * @param string $str The text to encode
 * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
 * @access public
 * @return string
 */
public function EncodeString(string $str, string $encoding = 'base64'): string {
    $encoded = '';
    switch (strtolower($encoding)) {
        case 'base64':
            $encoded = chunk_split(base64_encode($str), 76, $this->LE);
            break;
        case '7bit':
        case '8bit':
            $encoded = $this->FixEOL($str);
            // Make sure it ends with a line break
            if (substr($encoded, -(strlen($this->LE))) != $this->LE)
                $encoded .= $this->LE;
            break;
        case 'binary':
            $encoded = $str;
            break;
        case 'quoted-printable':
            $encoded = $this->EncodeQP($str);
            break;
        default:
            $this->SetError($this->Lang('encoding') . $encoding);
            break;
    }
    return $encoded;
}

/**
 * Encode a header string to best (shortest) of Q, B, quoted or none.
 * @access public
 * @param string $str
 * @param string $position
 * @return string
 */
public function EncodeHeader(string $str, string $position = 'text'): string {
    $x = 0;

    switch (strtolower($position)) {
        case 'phrase':
            if (!preg_match('/[\200-\377]/', $str)) {
                // Can't use addslashes as we don't know what value has magic_quotes_sybase
                $encoded = addcslashes($str, "\0..\37\177\\\"");
                if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                    return ($encoded);
                } else {
                    return ("\"$encoded\"");
                }
            }
            $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
            break;
        case 'comment':
            $x = preg_match_all('/[()"]/', $str, $matches);
            // Fall-through
        case 'text':
        default:
            $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
            break;
    }

    if ($x == 0) {
        return ($str);
    }

    $maxlen = 75 - 7 - strlen($this->CharSet);
    // Try to select the encoding which should produce the shortest output
    if (strlen($str) / 3 < $x) {
        $encoding = 'B';
        if (function_exists('mb_strlen') && $this->HasMultiBytes($str)) {
            // Use a custom function which correctly encodes and wraps long
            // multibyte strings without breaking lines within a character
            $encoded = $this->Base64EncodeWrapMB($str, "\n");
        } else {
            $encoded = base64_encode($str);
            $maxlen -= $maxlen % 4;
            $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
        }
    } else {
        $encoding = 'Q';
        $encoded = $this->EncodeQ($str, $position);
        $encoded = $this->WrapText($encoded, $maxlen, true);
        $encoded = str_replace('=' . self::CRLF, "\n", trim($encoded));
    }

    $encoded = preg_replace('/^(.*)$/m', " =?" . $this->CharSet . "?$encoding?\\1?=", $encoded);
    $encoded = trim(str_replace("\n", $this->LE, $encoded));

    return $encoded;
}

/**
 * Checks if a string contains multibyte characters.
 * @access public
 * @param string $str multi-byte text to wrap encode
 * @return bool
 */
public function HasMultiBytes(string $str): bool {
    if (function_exists('mb_strlen')) {
        return (strlen($str) > mb_strlen($str, $this->CharSet));
    } else { // Assume no multibytes (we can't handle without mbstring functions anyway)
        return false;
    }
}

/**
 * Correctly encodes and wraps long multibyte strings for mail headers
 * without breaking lines within a character.
 * Adapted from a function by paravoid at http://uk.php.net/manual/en/function.mb-encode-mimeheader.php
 * @access public
 * @param string $str multi-byte text to wrap encode
 * @param string $lf string to use as linefeed/end-of-line
 * @return string
 */
public function Base64EncodeWrapMB(string $str, string $lf = null): string {
    $start = "=?" . $this->CharSet . "?B?";
    $end = "?=";
    $encoded = "";
    if ($lf === null) {
        $lf = $this->LE;
    }

    $mb_length = mb_strlen($str, $this->CharSet);
    // Each line must have length <= 75, including $start and $end
    $length = 75 - strlen($start) - strlen($end);
    // Average multi-byte ratio
    $ratio = $mb_length / strlen($str);
    // Base64 has a 4:3 ratio
    $offset = $avgLength = floor($length * $ratio * .75);

    for ($i = 0; $i < $mb_length; $i += $offset) {
        $lookBack = 0;

        do {
            $offset = $avgLength - $lookBack;
            $chunk = mb_substr($str, $i, $offset, $this->CharSet);
            $chunk = base64_encode($chunk);
            $lookBack++;
        } while (strlen($chunk) > $length);

        $encoded .= $chunk . $lf;
    }

    // Chomp the last linefeed
    $encoded = substr($encoded, 0, -strlen($lf));
    return $encoded;
}

/**
 * Encode string to quoted-printable.
 * Only uses standard PHP, slow, but will always work.
 * @access public
 * @param string $input
 * @param int $line_max Number of chars allowed on a line before wrapping
 * @param bool $space_conv
 * @internal param string $string the text to encode
 * @return string
 */
public function EncodeQPphp(string $input = '', int $line_max = 76, bool $space_conv = false): string {
    $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
    $lines = preg_split('/(?:\r\n|\r|\n)/', $input);
    $eol = "\r\n";
    $escape = '=';
    $output = '';
    
    foreach ($lines as $line) {
        $linlen = strlen($line);
        $newline = '';
        for ($i = 0; $i < $linlen; $i++) {
            $c = substr($line, $i, 1);
            $dec = ord($c);
            if (($i == 0) && ($dec == 46)) { // convert first point in the line into =2E
                $c = '=2E';
            }
            if ($dec == 32) {
                if ($i == ($linlen - 1)) { // convert space at eol only
                    $c = '=20';
                } elseif ($space_conv) {
                    $c = '=20';
                }
            } elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) { // always encode "\t", which is *not* required
                $h2 = (int)floor($dec / 16);
                $h1 = (int)floor($dec % 16);
                $c = $escape . $hex[$h2] . $hex[$h1];
            }
            if ((strlen($newline) + strlen($c)) >= $line_max) { // CRLF is not counted
                $output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
                $newline = '';
                // check if newline first character will be point or not
                if ($dec == 46) {
                    $c = '=2E';
                }
            }
            $newline .= $c;
        } // end of for
        $output .= $newline . $eol;
    } // end of foreach
    return $output;
}
	
/**
 * Encode string to RFC2045 (6.7) quoted-printable format
 * Uses a PHP5 stream filter to do the encoding about 64x faster than the old version
 * Also results in same content as you started with after decoding
 * @see EncodeQPphp()
 * @access public
 * @param string $string the text to encode
 * @param int $line_max Number of chars allowed on a line before wrapping
 * @param bool $space_conv Dummy param for compatibility with existing EncodeQP function
 * @return string
 * @author Marcus Bointon
 */
public function EncodeQP(string $string, int $line_max = 76, bool $space_conv = false): string {
    if (function_exists('quoted_printable_encode')) { // Use native function if it's available (>= PHP5.3)
        return quoted_printable_encode($string);
    }
    $filters = stream_get_filters();
    if (!in_array('convert.*', $filters)) { // Got convert stream filter?
        return $this->EncodeQPphp($string, $line_max, $space_conv); // Fall back to old implementation
    }
    $fp = fopen('php://temp/', 'r+');
    $string = preg_replace('/\r\n?/', $this->LE, $string); // Normalise line breaks
    $params = array('line-length' => $line_max, 'line-break-chars' => $this->LE);
    $s = stream_filter_append($fp, 'convert.quoted-printable-encode', STREAM_FILTER_READ, $params);
    fputs($fp, $string);
    rewind($fp);
    $out = stream_get_contents($fp);
    stream_filter_remove($s);
    $out = preg_replace('/^\./m', '=2E', $out); // Encode . if it is first char on a line, workaround for bug in Exchange
    fclose($fp);
    return $out;
}

/**
 * Encode string to q encoding.
 * @link http://tools.ietf.org/html/rfc2047
 * @param string $str the text to encode
 * @param string $position Where the text is going to be used, see the RFC for what that means
 * @access public
 * @return string
 */
public function EncodeQ(string $str, string $position = 'text'): string {
    // There should not be any EOL in the string
    $pattern = "";
    $encoded = str_replace(array("\r", "\n"), '', $str);
    switch (strtolower($position)) {
        case 'phrase':
            $pattern = '^A-Za-z0-9!*+\/ -';
            break;

        case 'comment':
            $pattern = '\(\)"';
            // note that we don't break here!
            // for this reason we build the $pattern without including delimiters and []

        case 'text':
        default:
            // Replace every high ascii, control =, ? and _ characters
            // We put \075 (=) as first value to make sure it's the first one in being converted, preventing double encode
            $pattern = '\075\000-\011\013\014\016-\037\077\137\177-\377' . $pattern;
            break;
    }

    if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
        foreach (array_unique($matches[0]) as $char) {
            $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
        }
    }

    // Replace every spaces to _ (more readable than =20)
    return str_replace(' ', '_', $encoded);
}

/**
 * Adds a string or binary attachment (non-filesystem) to the list.
 * This method can be used to attach ascii or binary data,
 * such as a BLOB record from a database.
 * @param string $string String attachment data.
 * @param string $filename Name of the attachment.
 * @param string $encoding File encoding (see $Encoding).
 * @param string $type File extension (MIME) type.
 * @return void
 */
public function AddStringAttachment(string $string, string $filename, string $encoding = 'base64', string $type = 'application/octet-stream'): void {
    // Append to $attachment array
    $this->attachment[] = array(
        0 => $string,
        1 => $filename,
        2 => basename($filename),
        3 => $encoding,
        4 => $type,
        5 => true,  // isStringAttachment
        6 => 'attachment',
        7 => 0
    );
}

/**
 * Adds an embedded attachment. This can include images, sounds, and
 * just about any other document. Make sure to set the $type to an
 * image type. For JPEG images use "image/jpeg" and for GIF images
 * use "image/gif".
 * @param string $path Path to the attachment.
 * @param string $cid Content ID of the attachment. Use this to identify
 *        the Id for accessing the image in an HTML form.
 * @param string $name Overrides the attachment name.
 * @param string $encoding File encoding (see $Encoding).
 * @param string $type File extension (MIME) type.
 * @return bool
 */
public function AddEmbeddedImage(string $path, string $cid, string $name = '', string $encoding = 'base64', string $type = 'application/octet-stream'): bool {

    if (!@is_file($path)) {
        $this->SetError($this->Lang('file_access') . $path);
        return false;
    }

    $filename = basename($path);
    if ($name == '') {
        $name = $filename;
    }

    // Append to $attachment array
    $this->attachment[] = array(
        0 => $path,
        1 => $filename,
        2 => $name,
        3 => $encoding,
        4 => $type,
        5 => false,  // isStringAttachment
        6 => 'inline',
        7 => $cid
    );

    return true;
}

/**
 * Adds an embedded stringified attachment. This can include images, sounds, and
 * just about any other document. Make sure to set the $type to an
 * image type. For JPEG images use "image/jpeg" and for GIF images
 * use "image/gif".
 * @param string $string The attachment.
 * @param string $cid Content ID of the attachment. Use this to identify
 *        the Id for accessing the image in an HTML form.
 * @param string $name Overrides the attachment name.
 * @param string $encoding File encoding (see $Encoding).
 * @param string $type File extension (MIME) type.
 * @return bool
 */
public function AddStringEmbeddedImage(string $string, string $cid, string $name = '', string $encoding = 'base64', string $type = 'application/octet-stream'): bool {
    // Append to $attachment array
    $this->attachment[] = array(
        0 => $string,
        1 => $name,
        2 => $name,
        3 => $encoding,
        4 => $type,
        5 => true,  // isStringAttachment
        6 => 'inline',
        7 => $cid
    );
    return true;
}

/**
 * Returns true if an inline attachment is present.
 * @access public
 * @return bool
 */
public function InlineImageExists(): bool {
    foreach ($this->attachment as $attachment) {
        if ($attachment[6] == 'inline') {
            return true;
        }
    }
    return false;
}

/**
 * Returns true if an attachment (non-inline) is present.
 * @return bool
 */
public function AttachmentExists(): bool {
    foreach ($this->attachment as $attachment) {
        if ($attachment[6] == 'attachment') {
            return true;
        }
    }
    return false;
}

/**
 * Does this message have an alternative body set?
 * @return bool
 */
public function AlternativeExists(): bool {
    return !empty($this->AltBody);
}

///////////////////////////////////////////////
/// CLASS METHODS, MESSAGE RESET
///////////////////////////////////////////////

/**
 * Clears all recipients assigned in the TO array. Returns void.
 * @return void
 */
public function ClearAddresses(): void {
    foreach ($this->to as $to) {
        unset($this->all_recipients[strtolower($to[0])]);
    }
    $this->to = array();
}

/**
 * Clears all recipients assigned in the CC array. Returns void.
 * @return void
 */
public function ClearCCs(): void {
    foreach ($this->cc as $cc) {
        unset($this->all_recipients[strtolower($cc[0])]);
    }
    $this->cc = array();
}

/**
 * Clears all recipients assigned in the BCC array. Returns void.
 * @return void
 */
public function ClearBCCs(): void {
    foreach ($this->bcc as $bcc) {
        unset($this->all_recipients[strtolower($bcc[0])]);
    }
    $this->bcc = array();
}

/**
 * Clears all recipients assigned in the ReplyTo array. Returns void.
 * @return void
 */
public function ClearReplyTos(): void {
    $this->ReplyTo = array();
}

/**
 * Clears all recipients assigned in the TO, CC and BCC
 * array. Returns void.
 * @return void
 */
public function ClearAllRecipients(): void {
    $this->to = array();
    $this->cc = array();
    $this->bcc = array();
    $this->all_recipients = array();
}

/**
 * Clears all previously set filesystem, string, and binary
 * attachments. Returns void.
 * @return void
 */
public function ClearAttachments(): void {
    $this->attachment = array();
}

/**
 * Clears all custom headers. Returns void.
 * @return void
 */
public function ClearCustomHeaders(): void {
    $this->CustomHeader = array();
}

///////////////////////////////////////////////
/// CLASS METHODS, MISCELLANEOUS
///////////////////////////////////////////////

/**
 * Adds the error message to the error container.
 * @access protected
 * @param string $msg
 * @return void
 */
protected function SetError(string $msg): void {
    $this->error_count++;
    if ($this->Mailer == 'smtp' && !is_null($this->smtp)) {
        $lasterror = $this->smtp->getError();
        if (!empty($lasterror) && array_key_exists('smtp_msg', $lasterror)) {
            $msg .= '<p>' . $this->Lang('smtp_error') . $lasterror['smtp_msg'] . "</p>\n";
        }
    }
    $this->ErrorInfo = $msg;
}

/**
 * Returns the proper RFC 822 formatted date.
 * @access public
 * @return string
 * @static
 */
public static function RFCDate(): string {
    $tz = date('Z');
    $tzs = ($tz < 0) ? '-' : '+';
    $tz = abs($tz);
    $tz = (int)($tz / 3600) * 100 + ($tz % 3600) / 60;
    return sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);
}

/**
 * Returns the server hostname or 'localhost.localdomain' if unknown.
 * @access protected
 * @return string
 */
protected function ServerHostname(): string {
    if (!empty($this->Hostname)) {
        return $this->Hostname;
    } elseif (isset($_SERVER['SERVER_NAME'])) {
        return $_SERVER['SERVER_NAME'];
    } else {
        return 'localhost.localdomain';
    }
}

/**
 * Returns a message in the appropriate language.
 * @access protected
 * @param string $key
 * @return string
 */
protected function Lang(string $key): string {
    if (count($this->language) < 1) {
        $this->SetLanguage('en'); // set the default language
    }

    if (isset($this->language[$key])) {
        return $this->language[$key];
    } else {
        return 'Language string failed to load: ' . $key;
    }
}

/**
 * Returns true if an error occurred.
 * @access public
 * @return bool
 */
public function IsError(): bool {
    return ($this->error_count > 0);
}

/**
 * Changes every end of line from CRLF, CR or LF to $this->LE.
 * @access public
 * @param string $str String to FixEOL
 * @return string
 */
public function FixEOL(string $str): string {
    // condense down to \n
    $nstr = str_replace(array("\r\n", "\r"), "\n", $str);
    // Now convert LE as needed
    if ($this->LE !== "\n") {
        $nstr = str_replace("\n", $this->LE, $nstr);
    }
    return $nstr;
}

/**
 * Adds a custom header. $name value can be overloaded to contain
 * both header name and value (name:value).
 * @access public
 * @param string $name custom header name
 * @param string $value header value
 * @return void
 */
public function AddCustomHeader(string $name, string $value = null): void {
    if ($value === null) {
        // Value passed in as name:value
        $this->CustomHeader[] = explode(':', $name, 2);
    } else {
        $this->CustomHeader[] = array($name, $value);
    }
}

/**
 * Evaluates the message and returns modifications for inline images and backgrounds.
 * @access public
 * @param string $message Text to be HTML modified
 * @param string $basedir baseline directory for path
 * @return string $message
 */
public function MsgHTML(string $message, string $basedir = ''): string {
    preg_match_all("/(src|background)=[\"'](.*)[\"']/Ui", $message, $images);
    if (isset($images[2])) {
        foreach ($images[2] as $i => $url) {
            // do not change urls for absolute images (thanks to corvuscorax)
            if (!preg_match('#^[A-z]+://#', $url)) {
                $filename = basename($url);
                $directory = dirname($url);
                if ($directory == '.') {
                    $directory = '';
                }
                $cid = 'cid:' . md5($url);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $mimeType = self::_mime_types($ext);
                if (strlen($basedir) > 1 && substr($basedir, -1) != '/') {
                    $basedir .= '/';
                }
                if (strlen($directory) > 1 && substr($directory, -1) != '/') {
                    $directory .= '/';
                }
                if ($this->AddEmbeddedImage($basedir . $directory . $filename, md5($url), $filename, 'base64', $mimeType)) {
                    $message = preg_replace("/" . $images[1][$i] . "=[\"']" . preg_quote($url, '/') . "[\"']/Ui", $images[1][$i] . "=\"" . $cid . "\"", $message);
                }
            }
        }
    }
    $this->IsHTML(true);
    $this->Body = $message;
    if (empty($this->AltBody)) {
        $textMsg = trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s', '', $message)));
        if (!empty($textMsg)) {
            $this->AltBody = html_entity_decode($textMsg, ENT_QUOTES, $this->CharSet);
        }
    }
    if (empty($this->AltBody)) {
        $this->AltBody = 'To view this email message, open it in a program that understands HTML!' . "\n\n";
    }
    return $message;
}
	
	
/**
 * Gets the MIME type of the embedded or inline image.
 * @param string $ext File extension
 * @access public
 * @return string MIME type of ext
 * @static
 */
public static function _mime_types(string $ext = ''): string {
    $mimes = array(
        'xl' => 'application/excel',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'bin' => 'application/macbinary',
        'doc' => 'application/msword',
        'word' => 'application/msword',
        'class' => 'application/octet-stream',
        'dll' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'exe' => 'application/octet-stream',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'psd' => 'application/octet-stream',
        'sea' => 'application/octet-stream',
        'so' => 'application/octet-stream',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'mif' => 'application/vnd.mif',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dxr' => 'application/x-director',
        'dvi' => 'application/x-dvi',
        'gtar' => 'application/x-gtar',
        'php3' => 'application/x-httpd-php',
        'php4' => 'application/x-httpd-php',
        'php' => 'application/x-httpd-php',
        'phtml' => 'application/x-httpd-php',
        'phps' => 'application/x-httpd-php-source',
        'js' => 'application/x-javascript',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'tar' => 'application/x-tar',
        'tgz' => 'application/x-tar',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'zip' => 'application/zip',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'ra' => 'audio/x-realaudio',
        'wav' => 'audio/x-wav',
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'eml' => 'message/rfc822',
        'css' => 'text/css',
        'html' => 'text/html',
        'htm' => 'text/html',
        'shtml' => 'text/html',
        'log' => 'text/plain',
        'text' => 'text/plain',
        'txt' => 'text/plain',
        'rtx' => 'text/richtext',
        'rtf' => 'text/rtf',
        'xml' => 'text/xml',
        'xsl' => 'text/xml',
        'mpeg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
        'rv' => 'video/vnd.rn-realvideo',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie'
    );
    return (!isset($mimes[strtolower($ext)])) ? 'application/octet-stream' : $mimes[strtolower($ext)];
}

/**
 * Set (or reset) Class Objects (variables)
 *
 * Usage Example:
 * $page->set('X-Priority', '3');
 *
 * @access public
 * @param string $name Parameter Name
 * @param mixed $value Parameter Value
 * NOTE: will not work with arrays, there are no arrays to set/reset
 * @throws phpmailerException
 * @return bool
 * @todo Should this not be using __set() magic function?
 */
public function set(string $name, $value = ''): bool {
    try {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            throw new phpmailerException($this->Lang('variable_set') . $name, self::STOP_CRITICAL);
        }
    } catch (Exception $e) {
        $this->SetError($e->getMessage());
        if ($e->getCode() == self::STOP_CRITICAL) {
            return false;
        }
    }
    return true;
}

/**
 * Strips newlines to prevent header injection.
 * @access public
 * @param string $str String
 * @return string
 */
public function SecureHeader(string $str): string {
    return trim(str_replace(array("\r", "\n"), '', $str));
}

/**
 * Set the private key file and password to sign the message.
 *
 * @access public
 * @param string $cert_filename
 * @param string $key_filename Parameter File Name
 * @param string $key_pass Password for private key
 */
public function Sign(string $cert_filename, string $key_filename, string $key_pass): void {
    $this->sign_cert_file = $cert_filename;
    $this->sign_key_file = $key_filename;
    $this->sign_key_pass = $key_pass;
}

/**
 * Generate DKIM signature
 *
 * @access public
 * @param string $s Header
 * @return string
 */
public function DKIM_Sign(string $s): string {
    $privKeyStr = file_get_contents($this->DKIM_private);
    if ($this->DKIM_passphrase != '') {
        $privKey = openssl_pkey_get_private($privKeyStr, $this->DKIM_passphrase);
    } else {
        $privKey = $privKeyStr;
    }
    if (openssl_sign($s, $signature, $privKey)) {
        return base64_encode($signature);
    }
    return '';
}

/**
 * Generate DKIM Canonicalization Header
 *
 * @access public
 * @param string $s Header
 * @return string
 */
public function DKIM_HeaderC(string $s): string {
    $s = preg_replace("/\r\n\s+/", " ", $s);
    $lines = explode("\r\n", $s);
    foreach ($lines as $key => $line) {
        list($heading, $value) = explode(":", $line, 2);
        $heading = strtolower($heading);
        $value = preg_replace("/\s+/", " ", $value); // Compress useless spaces
        $lines[$key] = $heading . ":" . trim($value); // Don't forget to remove WSP around the value
    }
    $s = implode("\r\n", $lines);
    return $s;
}

/**
 * Generate DKIM Canonicalization Body
 *
 * @access public
 * @param string $body Message Body
 * @return string
 */
public function DKIM_BodyC(string $body): string {
    if ($body == '') return "\r\n";
    // stabilize line endings
    $body = str_replace("\r\n", "\n", $body);
    $body = str_replace("\n", "\r\n", $body);
    // END stabilize line endings
    while (substr($body, strlen($body) - 4, 4) == "\r\n\r\n") {
        $body = substr($body, 0, strlen($body) - 2);
    }
    return $body;
}

/**
 * Create the DKIM header, body, as new header
 *
 * @access public
 * @param string $headers_line Header lines
 * @param string $subject Subject
 * @param string $body Body
 * @return string
 */
public function DKIM_Add(string $headers_line, string $subject, string $body): string {
    $DKIMsignatureType = 'rsa-sha1'; // Signature & hash algorithms
    $DKIMcanonicalization = 'relaxed/simple'; // Canonicalization of header/body
    $DKIMquery = 'dns/txt'; // Query method
    $DKIMtime = time(); // Signature Timestamp = seconds since 00:00:00 - Jan 1, 1970 (UTC time zone)
    $subject_header = "Subject: $subject";
    $headers = explode($this->LE, $headers_line);
    $from_header = "";
    $to_header = "";
    foreach ($headers as $header) {
        if (strpos($header, 'From:') === 0) {
            $from_header = $header;
        } elseif (strpos($header, 'To:') === 0) {
            $to_header = $header;
        }
    }
    $from = str_replace('|', '=7C', $this->DKIM_QP($from_header));
    $to = str_replace('|', '=7C', $this->DKIM_QP($to_header));
    $subject = str_replace('|', '=7C', $this->DKIM_QP($subject_header)); // Copied header fields (dkim-quoted-printable)
    $body = $this->DKIM_BodyC($body);
    $DKIMlen = strlen($body); // Length of body
    $DKIMb64 = base64_encode(pack("H*", sha1($body))); // Base64 of packed binary SHA-1 hash of body
    $ident = ($this->DKIM_identity == '') ? '' : " i=" . $this->DKIM_identity . ";";
    $dkimhdrs = "DKIM-Signature: v=1; a=" . $DKIMsignatureType . "; q=" . $DKIMquery . "; l=" . $DKIMlen . "; s=" . $this->DKIM_selector . ";\r\n" .
        "\tt=" . $DKIMtime . "; c=" . $DKIMcanonicalization . ";\r\n" .
        "\th=From:To:Subject;\r\n" .
        "\td=" . $this->DKIM_domain . ";" . $ident . "\r\n" .
        "\tz=$from\r\n" .
        "\t|$to\r\n" .
        "\t|$subject;\r\n" .
        "\tbh=" . $DKIMb64 . ";\r\n" .
        "\tb=";
    $toSign = $this->DKIM_HeaderC($from_header . "\r\n" . $to_header . "\r\n" . $subject_header . "\r\n" . $dkimhdrs);
    $signed = $this->DKIM_Sign($toSign);
    return "X-PHPMAILER-DKIM: code.google.com/a/apache-extras.org/p/phpmailer/\r\n" . $dkimhdrs . $signed . "\r\n";
}

/**
 * Perform callback.
 * @param bool $isSent
 * @param string $to
 * @param string $cc
 * @param string $bcc
 * @param string $subject
 * @param string $body
 * @param string|null $from
 */
protected function doCallback(bool $isSent, string $to, string $cc, string $bcc, string $subject, string $body, string $from = null): void {
    if (!empty($this->action_function) && is_callable($this->action_function)) {
        $params = array($isSent, $to, $cc, $bcc, $subject, $body, $from);
        call_user_func_array($this->action_function, $params);
    }
}
}
/**
 * Exception handler for PHPMailer
 * @package PHPMailer
 */
class phpmailerException extends Exception {
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage(): string {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}