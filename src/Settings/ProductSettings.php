<?php

namespace App\Settings;

use App\Auth\BehindLogin;
use App\Auth\RequiresAdmin;
use App\Product\BillingType;
use App\Product\ProductModel;
use Config\FormPost;
use Neoan\Enums\GenericEvent;
use Neoan\Enums\RequestMethod;
use Neoan\Event\Event;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Web;
use Neoan\Routing\Interfaces\Routable;
use Neoan\Store\Store;

#[Web('/settings/products', 'Settings/views/settings.html', BehindLogin::class)]
#[FormPost('/settings/products', 'Settings/views/settings.html', BehindLogin::class)]
class ProductSettings implements Routable
{
    public function __invoke(RequiresAdmin $admin): array
    {
        Store::write('pageTitle', 'Product settings');
        $feedback = '';

        if(Request::getRequestMethod() === RequestMethod::POST) {
            $newProduct = new ProductModel(Request::getInputs());
            $newProduct->billingType = BillingType::tryFrom(Request::getInput('billingType'));
            try{
                $newProduct->store();
                $feedback = 'Saved!';
            } catch (\Exception $e) {
                $feedback = $e->getMessage();
            }
        }
        return [
            'tab' => 'products',
            'data' => ProductModel::forCompany($admin->user)->toArray(),
            'feedback' => $feedback,
        ];
    }
}