<?php

namespace App\Settings;

use App\Auth\RequiresAdmin;
use Neoan\Enums\RequestMethod;
use Neoan\Helper\Str;
use Neoan\Request\Request;

class MailSettings
{
    public function __invoke(RequiresAdmin $admin, string &$feedback): array
    {
        $mailSettings = MailSettingsModel::retrieveOneOrCreate(['companyId' => $admin->user->company()->id]);

        $defaults = [
            'mailProvider' => MailProvider::MAILJET,
            'senderEmail' => $admin->user->email,
            'senderName' => $admin->user->company()->name
        ];
        foreach($defaults as $key => $value){
            if(!isset($mailSettings->{$key})){
                $mailSettings->{$key} = $value;
            }
        }
        if(Request::getRequestMethod() === RequestMethod::POST) {
            [
                'senderEmail' => $mailSettings->senderEmail,
                'senderName' => $mailSettings->senderName,
                'privateKey' => $mailSettings->privateKey,
                'publicKey' => $mailSettings->publicKey
            ] = Request::getInputs();
            $mailSettings->mailProvider = MailProvider::from(Request::getInput('mailProvider'));

            $mailSettings->store();
        }

        return [
            ...$mailSettings->toArray(),
            'api' => $mailSettings->mailProvider->requiredAuth()
            ];
    }
}