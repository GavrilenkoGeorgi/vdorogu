<?php
namespace App;

use App\Config;

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * PHP Mailer with gmail smtp
 */

class Mailer {
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
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
      // Server settings
      $mail->SMTPDebug = 0;                               // Enable verbose debug output
      $mail->isSMTP();                                    // Set mailer to use SMTP
      $mail->Host       = 'smtp.gmail.com';               // Specify main and backup SMTP servers
      $mail->SMTPAuth   = true;                           // Enable SMTP authentication
      $mail->Username   = Config::GOOGLE_GMAIL_ACC;       // SMTP username
      $mail->Password   = Config::GOOGLE_PHP_MAILER_PASS; // SMTP password
      $mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
      $mail->Port       = 587;                            // TCP port to connect to
      $mail->CharSet = 'UTF-8';

      // Recipients
      // $mail->setFrom('gavrilenko.georgi@gmail.com', 'Vdorogu mailer.');
      $mail->setFrom('info@vdorogu.rf.gd', 'Info vdorogu.rf.gd');
      $mail->addAddress($to);
      $mail->addReplyTo('info@vdorogu.rf.gd', 'Information');
      // $mail->addCC('cc@example.com');
      // $mail->addBCC('bcc@example.com');

      // Attachments
      // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject = $subject;
      $mail->Body    = $html;
      $mail->AltBody = $text;
      // $mail->Subject = 'Here is the subject';
      // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $mail->send();
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}