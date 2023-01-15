<?php

namespace App\Settings;

use App\Address\Support\Countries;
use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use App\Company\CompanyModel;
use Config\FormPost;
use Neoan\Enums\RequestMethod;
use Neoan\Enums\ResponseOutput;
use Neoan\Request\Request;
use Neoan\Response\Response;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/:tab*', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/:tab', 'Settings/views/settings.html', BehindLogin::class)]
class Settings implements Routable
{
    private string $feedback = '';
    public function __invoke(RequiresAdmin $admin, Countries $countries): array
    {
        Store::write('pageTitle', 'Settings');
        $data = match (Request::getParameter('tab')){
            'products' => (new ProductSettings())($this->feedback),
            'billing' => (new BillingSettings())($this->feedback),
            default => (new CompanySettings())($this->feedback)
        };
        return [
            'tab' => Request::getParameter('tab') ?? 'company',
            'data' => $data,
            'feedback' => $this->feedback,
            'countries' => $countries->countries
        ];
    }

}