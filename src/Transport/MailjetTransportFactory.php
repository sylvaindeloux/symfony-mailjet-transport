<?php

namespace SylvainDeloux\MailjetTransport\Transport;

use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class MailjetTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();
        $user = $this->getUser($dsn);
        $password = $this->getPassword($dsn);
        $host = $dsn->getHost();
        $port = $dsn->getPort(587);

        if ('mailjet+api' === $scheme) {
            $transport = new MailjetApiTransport($this->client, $this->dispatcher, $this->logger);
            $transport->setApiKey($user);
            $transport->setSecretKey($password);
            $transport->setHost($host);
            $transport->setVersion($dsn->getOption('version', '3.1'));
            return $transport;
        }

        if ('mailjet+smtp' === $scheme) {
            $transport = new EsmtpTransport($host, $port, false, $this->dispatcher, $this->logger);
            $transport->setUsername($user);
            $transport->setPassword($password);
            return $transport;
        }

        throw new UnsupportedSchemeException($dsn, 'mailjet', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return array('mailjet', 'mailjet+api', 'mailjet+smtp');
    }
}
