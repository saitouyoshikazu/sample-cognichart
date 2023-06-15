<?php

namespace App\Infrastructure\Sns;

interface GMailerInterface
{

    public function getMailer(string $host, int $port, string $userName, string $password, string $encryption);

    public function getMessage(string $subject, string $from, string $to, string $body = null, string $fromName = null, string $toName = null);

}
