<?php

namespace App\Settings;

use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/mailing', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/mailing', 'Settings/views/settings.html', BehindLogin::class)]
class MailSettings implements Routable
{
    public function __invoke(RequiresAdmin $admin): array
    {
        Store::write('pageTitle', 'Mail settings');
        $feedback = '';
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
            'tab' => 'mailing',
            'data' => [...$mailSettings->toArray(),
                'api' => $mailSettings->mailProvider->requiredAuth()
            ],
            'feedback' => $feedback,
        ];
    }
}