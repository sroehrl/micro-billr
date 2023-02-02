<?php

namespace App\Settings;

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
}
