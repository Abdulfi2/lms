<?php

namespace App\Mail\Transport;

use App\Services\GoogleService;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class GmailApiTransport extends AbstractTransport
{
    public function __construct(protected GoogleService $google)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $this->google->sendRawMessage($message->toString());
    }

    public function __toString(): string
    {
        return 'gmail-api';
    }
}
