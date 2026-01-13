<?php
require_once __DIR__ . "/../../vendor/autoload.php";
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer{
    private $mail;
    public function __construct(){
        $this->mail = new PHPMailer(true);
        $this->mail->CharSet = 'UTF-8';
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $_ENV['SMTP_PORT'];
        $this->mail->Username = $_ENV['SMTP_USER'];
        $this->mail->Password = $_ENV['SMTP_PASS'];
    }

    public function setSender($email, $name){
        $this->mail->setFrom($email, $name);
    }

    public function addRecipient($email, $name=''){
        $this->mail->addAddress($email, $name);
    }

    public function addAttachment($attachment){
        if ($attachment && isset($attachment['error']) && $attachment['error'] == UPLOAD_ERR_OK) {
            $maxSize = 3 * 1024 * 1024; // maximum of 3MB
            if ($attachment['size'] > $maxSize) {
                throw new Exception('Attachment size exceeds limit');
            }
            $this->mail->addAttachment($attachment['tmp_name'], $attachment['name']);
        } elseif ($attachment && isset($attachment['error']) && $attachment['error'] != UPLOAD_ERR_NO_FILE) {
            throw new Exception('Error uploading attachment');
        }
    }

    public function setSubject($subject){
        $this->mail->Subject = $subject;
    }

    public function setBody($body){
        $this->mail->Body = $body;
    }

    public function send(){
        $this->mail->send();
    }

    public function setReplyTo($email, $name = '') {
        $this->mail->addReplyTo($email, $name);
    }

    public function setInReplyTo($messageId) {
        $this->mail->addCustomHeader('In-Reply-To', $messageId);
        $this->mail->addCustomHeader('References', $messageId);
    }

    public function isHTML($isHtml = true) {
        $this->mail->isHTML($isHtml);
        return $this;
    }
}