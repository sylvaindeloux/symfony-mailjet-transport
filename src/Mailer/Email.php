<?php

namespace SylvainDeloux\MailjetTransport\Mailer;

use Symfony\Component\Mime\Email as BaseEmail;
use Symfony\Component\Mime\Exception\LogicException;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;

class Email extends BaseEmail
{
    protected $templateId = null;
    protected $variables = array();
    protected $errorReportingEmail = null;
    protected $templateErrorDeliver = false;

    public function __construct(Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($headers, $body);

        $this->html('');
        $this->text('');
    }

    public function getTemplateId()
    {
        return $this->templateId;
    }

    public function setTemplateId(int $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    public function setVariable(string $key, string $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    public function getErrorReportingEmail()
    {
        return $this->errorReportingEmail;
    }

    public function setErrorReportingEmail(string $errorReportingEmail): self
    {
        $this->errorReportingEmail = $errorReportingEmail;

        return $this;
    }

    public function isTemplateErrorDeliver(): bool
    {
        return $this->templateErrorDeliver;
    }

    public function setTemplateErrorDeliver(bool $templateErrorDeliver = true): self
    {
        $this->templateErrorDeliver = $templateErrorDeliver;

        return $this;
    }

    public function ensureValidity()
    {
        if (null === $this->templateId) {
            if (count($this->variables)) {
                throw new \LogicException('A template id is required.');
            }
        }

        parent::ensureValidity();
    }
}
