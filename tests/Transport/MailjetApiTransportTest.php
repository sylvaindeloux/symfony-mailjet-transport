<?php

namespace SylvainDeloux\MailjetTransport\Transport;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailjetApiTransportTest extends TestCase
{
    /**
     * @dataProvider getTransportData
     */
    public function testToString(MailjetApiTransport $transport, string $expected)
    {
        $this->assertSame($expected, (string) $transport);
    }

    public function getTransportData()
    {
        $client = $this->getMockBuilder(HttpClientInterface::class)->disableOriginalConstructor()->getMock();
        $dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->disableOriginalConstructor()->getMock();
        $logger = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();

        return [
            [
                (new MailjetApiTransport($client))->setHost('host'),
                'mailjet+api://host?version=3.1',
            ],
            [
                (new MailjetApiTransport($client))->setHost('host')->setVersion('12'),
                'mailjet+api://host?version=12',
            ],
        ];
    }

    // @todo: more tests
}
