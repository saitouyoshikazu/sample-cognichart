<?php

namespace Tests\Unit\Infrastructure\Sns;

use Tests\TestCase;

class GMailerTest extends TestCase
{

    private $gMailerInterfaceName = 'App\Infrastructure\Sns\GMailerInterface';

    public function testProvider()
    {
        $gMailer = app($this->gMailerInterfaceName);
        $this->assertEquals(get_class($gMailer), 'App\Infrastructure\Sns\GMailer');
    }

    public function testGetMailerReturnNull()
    {
        $gMailer = app($this->gMailerInterfaceName);

        $host = 'smtp.gmail.com';
        $port = 587;
        $username = 'magicianturtle@gmail.com';
        $password = 'turtlexsw21qaz';
        $encryption = 'tls';

        $result = $gMailer->getMailer(' ', $port, $username, $password, $encryption);
        $this->assertNull($result);

        $result = $gMailer->getMailer($host, -1, $username, $password, $encryption);
        $this->assertNull($result);

        $result = $gMailer->getMailer($host, 65536, $username, $password, $encryption);
        $this->assertNull($result);

        $result = $gMailer->getMailer($host, $port, ' ', $password, $encryption);
        $this->assertNull($result);

        $result = $gMailer->getMailer($host, $port, $username, ' ', $encryption);
        $this->assertNull($result);

        $result = $gMailer->getMailer($host, $port, $username, $password, ' ');
        $this->assertNull($result);
    }

    public function testGetMailer()
    {
        $gMailer = app($this->gMailerInterfaceName);

        $host = 'smtp.gmail.com';
        $port = 587;
        $username = 'magicianturtle@gmail.com';
        $password = 'turtlexsw21qaz';
        $encryption = 'tls';

        $result = $gMailer->getMailer($host, $port, $username, $password, $encryption);
        $this->assertEquals(get_class($result), 'Swift_Mailer');
    }

    public function testGetMessageReturnNull()
    {
        $gMailer = app($this->gMailerInterfaceName);

        $subject = 'テスト';
        $from = 'magicianturtle@gmail.com';
        $to = 'buffer-30e1995baee7e5b29cb6@to.bufferapp.com';

        $result = $gMailer->getMessage(' ', $from, $to);
        $this->assertNull($result);

        $result = $gMailer->getMessage($subject, ' ', $to);
        $this->assertNull($result);

        $result = $gMailer->getMessage($subject, $from, ' ');
        $this->assertNull($result);
    }

    public function testGetMessage()
    {
        $gMailer = app($this->gMailerInterfaceName);

        $subject = 'テスト';
        $from = 'magicianturtle@gmail.com';
        $to = 'buffer-30e1995baee7e5b29cb6@to.bufferapp.com';

        $result = $gMailer->getMessage($subject, $from, $to);
        $this->assertEquals(get_class($result), 'Swift_Message');
    }

}
