<?php

namespace Mawufemor\Techandfun\services;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

require_once ROOT . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificationServices
{
    public function sendEmail(string $to, string $subject, string $message): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST']     ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom($_ENV['MAIL_USERNAME'] ?? '', 'Enactus Club');
            $mail->addAddress($to);
            $mail->addReplyTo('justtechandfun@gmail.com', 'Information');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            return true;
        } catch (Exception $e) {
            error_log("Email failed for {$to}: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function sendSMS(string $to, string $message): bool
    {
        $apiKey   = $_ENV['MNOTIFY_API_KEY'] ?? '';
        $endPoint = 'https://api.mnotify.com/api/sms/quick';
        $url      = $endPoint . '?key=' . $apiKey;

        $smsData = [
            'recipient'     => [$to],
            'sender'        => 'Techandfun',
            'message'       => $message,
            'is_schedule'   => false,
            'schedule_date' => '',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($smsData));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $smsResponse = curl_exec($ch);
        $curlError   = curl_error($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($smsResponse === false) {
            error_log("SMS transport error for {$to}: {$curlError}");
            return false;
        }

        $decoded = json_decode($smsResponse, true);

        // mNotify returns a "code" field — "1000" indicates success.
        // Adjust this check to match mNotify's actual documented response format.
        if ($httpCode !== 200 || !isset($decoded['code']) || $decoded['code'] !== '1000') {
            error_log("SMS failed for {$to}: " . $smsResponse);
            return false;
        }

        return true;
    }
}