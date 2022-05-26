Mailjet Transport for Symfony Mailer Component
==============================================

[![Packagist](https://img.shields.io/packagist/v/sylvaindeloux/symfony-mailjet-transport.svg)](https://packagist.org/packages/sylvaindeloux/symfony-mailjet-transport)
[![Packagist](https://img.shields.io/packagist/dt/sylvaindeloux/symfony-mailjet-transport.svg)](https://packagist.org/packages/sylvaindeloux/symfony-mailjet-transport)
[![Travis build](https://img.shields.io/travis/sylvaindeloux/symfony-mailjet-transport.svg)](https://travis-ci.org/github/sylvaindeloux/symfony-mailjet-transport)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/sylvaindeloux/symfony-mailjet-transport/blob/master/LICENSE.md)

**Deprecated repository**

Since Mailjet has been added by Symfony team to `symfony/mailjet-mailer`, this bundle will not evolve. 

**Installation**

Install the bundle:

    composer require sylvaindeloux/symfony-mailjet-transport

Add it to `config/bundles.php`:

    <?php

    return [
        // ...
        SylvainDeloux\MailjetTransport\MailjetTransportBundle::class => ['all' => true],
    ];

Now you can use your Mailjet account with Symfony Mailer. You just need to configure the `MAILER_DSN` environment variable with your credentials:

* SMTP: `mailjet+smtp://<your api key>:<your api secret>@in-v3.mailjet.com`
* API: `mailjet+api://<your api key>:<your api secret>@api.mailjet.com?version=3.1`

Use Mailjet templates with variables
------------------------------------

If you want to use a custom template instead of a Twig HTML / text body, and inject your own variables:

    $email = (new \SylvainDeloux\MailjetTransport\Mailer\Email())
        // ...
        ->setTemplateId(<your template id>)
        ->setErrorReportingEmail(<your email address for debugging>) // optional, to get a detailled message if template error occurs
        ->setTemplateErrorDeliver() // optional, if you want the mail to be delivered if template error occurs
        ->setVariables(array(
            'key' => 'value',
        ))
    ;
