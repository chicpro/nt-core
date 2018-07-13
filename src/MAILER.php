<?php
/**
 * Mail send
 * type : text=0, html=1, text+html=2
 */

use PHPMailer\PHPMailer\PHPMailer;

class MAILER
{
    protected $fname;
    protected $fmail;
    protected $to;
    protected $subject;
    protected $content;
    protected $replyto = '';
    protected $type = 1;

    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(); // defaults to using php "mail()"

        $this->mail->IsSMTP();
        $this->mail->Host = NT_SMTP_HOST;
        $this->mail->Port = NT_SMTP_PORT;

        if (NT_SMTP_AUTH === true) {
            $this->mail->SMTPAuth   = NT_SMTP_AUTH;
            $this->mail->Username   = NT_SMTP_USER;
            $this->mail->Password   = NT_SMTP_PASS;
            $this->mail->SMTPSecure = NT_SMTP_SECURE;
        }

        $this->mail->CharSet = 'UTF-8';
        $this->mail->AltBody = '';
    }

    public function setFrom(string $fmail)
    {
        $this->mail->From = $fmail;
    }

    public function setFromName(string $fname)
    {
        $this->mail->FromName = $fname;
    }

    public function setSubject(string $subject)
    {
        $this->mail->Subject = $subject;
    }

    public function setAltBody(string $abody)
    {
        $this->mail->AltBody = $abody;
    }

    public function setContent(string $content)
    {
        if ($this->type != 1)
            $content = nl2br($content);

        $this->mail->msgHTML($content);
    }

    public function setAddress(string $to)
    {
        $this->mail->addAddress($to);
    }

    public function setReply(string $reply)
    {
        $this->mail->addReplyTo($reply);
    }

    public function setType(int $type)
    {
        $this->type = $type;
    }

    public function send()
    {
        return $this->mail->send();
    }
}
