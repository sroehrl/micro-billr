<?php

namespace App\Bill;

enum BillStatus: string
{
    case PROCESSING = 'processing';
    case GENERATED = 'generated';
    case SENT_OUT = 'sent out';
    case REVOKED = 'revoked';
}
