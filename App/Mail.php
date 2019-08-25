<?php
namespace App;

use Mailgun\Mailgun;
use App\Config;

/**
 * Mail
 */

class Mail {
  /**
   * Send a message
   * 
   * @param string $to Recipient
   * @param string $subject Subject
   * @param string $text Text-only content of the message
   * @param string $html HTML content of the message
   * 
   * @return mixed
   */
  public static function send($to, $subject, $text, $html) {
    $mg = new Mailgun(Config::MAILGUN_API_KEY);
    $domain = Config::MAILGUN_DOMAIN;
    // Compose and send your message
    $mg->sendMessage($domain, array('from' => 'no-reply@vdorogu.rf.gd',
                                    'to' => $to,
                                    'subject' => $subject,
                                    'text' => $text,
                                    'html' => $html));
  }
}