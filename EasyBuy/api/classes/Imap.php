<?php
use Webklex\PHPIMAP\ClientManager;

require_once __DIR__ . "/../../vendor/autoload.php";
require 'config.php';

class Imap{
    private $clientManager;

    public function __construct(){
        $this->clientManager = new ClientManager();
        $this->clientManager = $this->clientManager->make([
            'host' => $_ENV['IMAP_HOST'],
            'port' => $_ENV['IMAP_PORT'],
            'encryption' => $_ENV['ENCRYPTION'],
            'validate_cert' => $_ENV['VALIDATE_CERT'] === 'true',
            'username' => $_ENV['USER_NAME'],
            'password' => $_ENV['PASSWORD'],
            'protocol' => $_ENV['PROTOCOL']
        ]);
        $this->clientManager->connect();
    }

    public function fetchEmails($limit = 20){
        $folder = $this->clientManager->getFolder('INBOX');
        return $folder->messages()->all()->limit($limit)->get();
    }

    public function fetchUnreadEmails($limit = 20){
        $folder = $this->clientManager->getFolder('INBOX');
        $query =  $folder->messages()->all()->unseen();

        return $query->limit($limit)->get();
    }

    public function getMailSender($email){
        $from = $email->getFrom()->first();
        return $from ? ($from->personal ?? $from->mailbox): 'Unknown';
    }

    public function getEmailAddress($email){
        $from = $email->getFrom()->first();
        return $from ? $from->mailbox . '@' . $from->host : 'unknown@unknown.com';
    }

    public function getSubject($email){
        return $email->getSubject()->first() ?? 'No Subject';
    }

    public function getEmailDate($email){
        try {
        $dateValue = $email->getDate()->first();
        $timestamp = strtotime($dateValue);
        return $timestamp ? date('M j, Y g:i A', $timestamp) : 'Unknown date';
        } catch (Exception $e) {
        return 'Unknown date';
        }
    }

  public function getMailBody($email){
    $htmlBody = $email->getHTMLBody();
    if ($htmlBody) {
        // Remove style and script tags with their content first
        $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $htmlBody);
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        
        // Convert common HTML elements to text equivalents
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<\/div>/i', "\n", $text);
        
        // Remove all remaining HTML tags
        $text = strip_tags($text);
        
        // Clean up excessive whitespace
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text);
        
        return trim($text);
    }
    return $email->getTextBody() ?? 'No content';
  }

    public function getMailBodyHTML($email){
        $htmlBody = $email->getHTMLBody();
        if ($htmlBody) {
            return $htmlBody;
        }
        
        $bodies = $email->getBodies();
        if ($bodies && isset($bodies['html'])) {
            return $bodies['html'];
        }
        
        $textBody = $email->getTextBody();
        if ($textBody) {
            return nl2br(htmlspecialchars($textBody));
        }
        
        return 'No content available';
    }
}