Mailjet Transport for Symfony Mailer Component
==============================================

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
