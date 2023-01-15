<?php

namespace App\Settings;

use App\Product\BillingType;
use App\Product\ProductModel;
use Neoan\Enums\GenericEvent;
use Neoan\Enums\RequestMethod;
use Neoan\Event\Event;
use Neoan\Request\Request;
use Neoan\Routing\Interfaces\Routable;

class ProductSettings implements Routable
{
    public function __invoke(&$feedback): array
    {


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
        return ProductModel::retrieve(['^deletedAt'])->toArray();
    }
}