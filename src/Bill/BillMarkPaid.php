<?php

namespace App\Bill;

use App\Auth\Auth;
use App\Auth\BehindLogin;
use App\Helper\FeedbackWrapper;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Post;
use Neoan\Routing\Interfaces\Routable;

#[Post('/bill/:id/mark-paid', BehindLogin::class)]
class BillMarkPaid implements Routable
{
    public function __invoke(Auth $auth): void
    {
        $bill = BillModel::get(Request::getParameter('id'));
        $bill->paidAt->set(Request::getInput('paidAt'));
        $bill->transactionCode = Request::getInput('transactionCode');
        $bill->store();
        FeedbackWrapper::redirectBack('Bill marked as paid');
    }
}