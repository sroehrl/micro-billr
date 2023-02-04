<?php

namespace App\Settings;

use App\Mailing\Mailjet;
use App\Mailing\MailProviderInterface;

enum MailProvider: string
{
    case MAILJET = 'mailjet';

    public function requiredAuth(): array
    {
        return match ($this){
            MailProvider::MAILJET => [
                'publicKey' => 'API key',
                'privateKey' => 'Secret key',
                'info' => 'In Mailjet, your private & public keys are accessible through "Account-settings" => "REST API" => "API Key Management"'
            ]
        };
    }
    public function getProvider(): MailProviderInterface
    {
        return match ($this){
            MailProvider::MAILJET => new Mailjet()
        };
    }

}
