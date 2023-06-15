<?php

namespace App\Infrastructure\Sns;

class GMailer implements GMailerInterface
{

    public function getMailer(string $host, int $port, string $userName, string $password, string $encryption)
    {
        $host = trim($host);
        $userName = trim($userName);
        $password = trim($password);
        $encryption = trim($encryption);
        if (empty($host) || empty($userName) || empty($password) || empty($encryption)) {
            return null;
        }

        if ($port < 0 || 65535 < $port) {
            return null;
        }

        $transport = new \Swift_SmtpTransport($host, $port, $encryption);
        $transport
            ->setUsername($userName)
            ->setPassword($password);
        return new \Swift_Mailer($transport);
    }

    public function getMessage(string $subject, string $from, string $to, string $body = null, string $fromName = null, string $toName = null)
    {
        $subject = trim($subject);
        $from = trim($from);
        $to = trim($to);
        $body = trim($body);
        $fromName = trim($fromName);
        $toName = trim($toName);
        if (empty($subject) || empty($from) || empty($to)) {
            return null;
        }
        $fromElement = $from;
        if (!empty($fromName)) {
            $fromElement = [$from => $fromName];
        }
        $toElement = $to;
        if (!empty($toName)) {
            $toElement = [$to => $toName];
        }

        $message = new \Swift_Message($subject);
        $message
            ->setFrom($fromElement)
            ->setTo($toElement)
            ->setBody($body);
        return $message;
    }

}
