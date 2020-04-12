<?php

namespace SylvainDeloux\MailjetTransport\Transport;

use Symfony\Component\Mailer\Test\TransportFactoryTestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

class MailjetTransportFactoryTest extends TransportFactoryTestCase
{
    public function getFactory(): TransportFactoryInterface
    {
        return new MailjetTransportFactory($this->getDispatcher(), $this->getClient(), $this->getLogger());
    }

    public function supportsProvider(): iterable
    {
        yield [
            new Dsn('mailjet', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet+api', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet+smtp', 'default'),
            true,
        ];
    }

    public function createProvider(): iterable
    {
        $client = $this->getClient();
        $dispatcher = $this->getDispatcher();
        $logger = $this->getLogger();

        $apiTransport = new MailjetApiTransport($client, $dispatcher, $logger);
        $apiTransport->setApiKey(self::USER);
        $apiTransport->setSecretKey(self::PASSWORD);
        $apiTransport->setHost('default');
        $apiTransport->setVersion('3.1');

        $smtpTransport = new EsmtpTransport('default', 587, false, $dispatcher, $logger);
        $smtpTransport->setUsername(self::USER);
        $smtpTransport->setPassword(self::PASSWORD);

        yield [
            new Dsn('mailjet+api', 'default', self::USER, self::PASSWORD),
            $apiTransport,
        ];

        yield [
            new Dsn('mailjet+smtp', 'default', self::USER, self::PASSWORD),
            $smtpTransport,
        ];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield [
            new Dsn('mailjet+foo', 'default', self::USER, self::PASSWORD),
            'The "mailjet+foo" scheme is not supported; supported schemes for mailer "mailjet" are: "mailjet", "mailjet+api", "mailjet+smtp".',
        ];
    }

    public function incompleteDsnProvider(): iterable
    {
        yield [new Dsn('mailjet+api', 'default', self::USER)];

        yield [new Dsn('mailjet+api', 'default', null, self::PASSWORD)];
    }
}
