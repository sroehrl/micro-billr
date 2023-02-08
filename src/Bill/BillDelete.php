<?php

namespace App\Bill;

use App\Auth\BehindLogin;
use App\Mailing\Mail;
use Neoan\Request\Request;
use Neoan\Routing\Attributes\Delete;
use Neoan\Routing\Interfaces\Routable;

#[Delete('/api/bill/:id', BehindLogin::class)]
class BillDelete implements Routable
{
    public function __invoke(Mail $mail): array
    {
        $bill = BillModel::get(Request::getParameter('id'));
        // already sent out?
        if($bill->billStatus === BillStatus::SENT_OUT){
            $mail->sendBillCancellation($bill);
        }

        $bill->billStatus = BillStatus::REVOKED;
        $bill->deletedAt->set('now');
        $bill->store();

        return ['deleted' => $bill->billNumber];
    }
}