<?php

namespace Tests\Unit\Infrastructure\Sns;

use Tests\TestCase;

class BufferMailTest extends TestCase
{

    private $bufferMailInterfaceName = "App\\Infrastructure\\Sns\\BufferMailInterface";

    public function testProvider()
    {
        $bufferMail = app($this->bufferMailInterfaceName);
        $this->assertEquals(get_class($bufferMail), "App\\Infrastructure\\Sns\\BufferMail");
    }

    public function testSend()
    {
        $bufferMail = app($this->bufferMailInterfaceName);

        $message = "これはテストメッセージです。\nlocal laravel->Buffer->Google+,Facebook,Twitter\n";
        $ttl = hash("md5", uniqid(rand(), true));
        $message .= $ttl;
        $result = $bufferMail->send($message);
        $this->assertEquals($result, 1);
    }

}
