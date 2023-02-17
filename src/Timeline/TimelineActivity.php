<?php

namespace App\Timeline;

enum TimelineActivity: string
{
    case PROJECT_CREATED =  'project created';
    case PROJECT_STATUS_CHANGED =  'project status change';
    case ESTIMATE_CREATED =  'estimate created';
    case ESTIMATE_SENT =  'estimate sent out';
    case ESTIMATE_DELETED =  'estimate rescinded';
    case BILL_CREATED =  'invoice created';
    case BILL_SENT =  'invoice sent out';
    case BILL_DELETED =  'invoice rescinded';
    case CUSTOM = 'custom';
}
