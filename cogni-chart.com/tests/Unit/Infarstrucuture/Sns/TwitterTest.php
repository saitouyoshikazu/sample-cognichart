<?php

namespace Tests\Unit\Infrastructure\Sns;

use Tests\TestCase;

class TwitterTest extends TestCase
{

    private $twitterInterfaceName = 'App\Infrastructure\Sns\TwitterInterface';

    public function testProvider()
    {
        $twitter = app($this->twitterInterfaceName);
        $this->assertEquals(get_class($twitter), 'App\Infrastructure\Sns\Twitter');
    }

    public function testPost()
    {
        $twitter = app($this->twitterInterfaceName);

        $message = "怪しいアプリ開発中tweetテストメッセージaaaabbbbbbbbbbccccccccccddddddddddeeeeeeeeee"; //100byte
        $message .= "ffffffffffgggggggggghhhhhhhhhhiiiiiiiiiijjjjjjjjjjkkkkkkkkkkllllllllllmmmmmmmmmmnnnnnnnnnnoooooooooo"; //200byte
        $message .= "ppppppppppqqqqqqqqqqrrrrrrrrrrssssssssssttttttttttuuuuuuuuuuvvvvvvvvvvwwwwwwwwww"; // 280byte
        $message .= "x";
        $result = $twitter->post($message);
        $this->assertFalse($result);

        $message = "怪しいアプリ開発中tweetテストメッセージ";
        $message .= hash("md5", uniqid(rand(), true));
        $message = "これはテストメッセージです。 SNS連携 vagrant-laravel -> twitter -> IFTTT -> Buffer -> Google+, Facebook";
        $result = $twitter->post($message);
        $this->assertTrue($result);
    }

}
