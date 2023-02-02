<?php

namespace App\Settings;

use App\Address\Country;
use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use App\DocumentCreator\Create;
use Config\FormPost;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/:tab*', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/:tab', 'Settings/views/settings.html', BehindLogin::class)]
class Settings implements Routable
{
    private string $feedback = '';
    public function __invoke(RequiresAdmin $admin, Create $documentCreator): array
    {
        Store::write('pageTitle', 'Settings');
        $data = match (Request::getParameter('tab')){
            'products' => (new ProductSettings())($this->feedback),
            'billing' => (new BillingSettings())($admin, $this->feedback),
            'mailing' => (new MailSettings())($admin, $this->feedback),
            default => (new CompanySettings())($this->feedback)
        };
        $countries = [];
        foreach (Country::cases() as $case){
            $countries[$case->value] = $case->value;
        }
        return [
            'tab' => Request::getParameter('tab') ?? 'company',
            'data' => $data,
            'feedback' => $this->feedback,
            'countries' => $countries
        ];
    }

}