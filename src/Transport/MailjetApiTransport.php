<?php

namespace SylvainDeloux\MailjetTransport\Transport;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractApiTransport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MailjetApiTransport extends AbstractApiTransport
{
    private $apiKey;
    private $secretKey;
    private $version;

    public function __toString(): string
    {
        return sprintf('mailjet+api://%s?version=%s', $this->host, $this->version);
    }

    protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface
    {
        $response = $this->client->request('POST', $this->getEndpoint().'/send', array(
            'json' => $this->getPayload($email, $envelope),
            'auth_basic' => array($this->apiKey, $this->secretKey),
        ));

        $result = $response->toArray(false);

        if (array_key_exists('Messages', $result)) {
            $mailjetMessage = reset($result['Messages']);
        }

        if (200 !== $response->getStatusCode()) {
            if (array_key_exists('ErrorMessage', $result)) {
                throw new HttpTransportException(sprintf('Unable to send an email: %s', $result['ErrorMessage']), $response);
            }

            if ('error' === $mailjetMessage['Status']) {
                throw new HttpTransportException(sprintf('Unable to send an email: %s', $mailjetMessage['Errors'][0]['ErrorMessage']), $response);
            }
        }

        $sentMessage->setMessageId($mailjetMessage['To'][0]['MessageID']);

        return $response;
    }

    private function getPayload(Email $email, Envelope $envelope): array
    {
        $payload = array(
            'Subject' => $email->getSubject(),
            'From' => array(
                'Email' => $envelope->getSender()->getAddress(),
            ),
            'To' => array(),
            'Attachments' => array(),
            'InlinedAttachments' => array(),
        );

        if ($email->getTextBody()) {
            $payload['TextPart'] = $email->getTextBody();
        }

        if ($email->getHtmlBody()) {
            $payload['HTMLPart'] = $email->getHtmlBody();
        }

        if ('' !== $envelope->getSender()->getName()) {
            $payload['From']['Name'] = $envelope->getSender()->getName();
        }

        $recipients = $this->getRecipients($email, $envelope);
        foreach ($recipients as $recipient) {
            $payload['To'][] = array(
                'Email' => $recipient->getAddress(),
                'Name' => $recipient->getName(),
            );
        }

        $attachments = $email->getAttachments();
        foreach ($attachments as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            $disposition = $headers->getHeaderBody('Content-Disposition');

            $att = array(
                'Base64Content' => $attachment->bodyToString(),
                'ContentType' => $headers->get('Content-Type')->getBody(),
            );

            if ($name = $headers->getHeaderParameter('Content-Disposition', 'name')) {
                $att['Filename'] = $name;
            }

            if ('inline' === $disposition) {
                $payload['InlinedAttachments'][] = $att;
            } else {
                $payload['Attachments'][] = $att;
            }
        }

        return array('Messages' => array($payload));
    }

    protected function getEndpoint(): string
    {
        return sprintf('https://%s/v%s', $this->host, $this->version);
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }
}
