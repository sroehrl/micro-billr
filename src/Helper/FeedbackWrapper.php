<?php

namespace App\Helper;

use Neoan\Response\Response;

class FeedbackWrapper
{
    static function redirectBack(string $feedback = ''): void
    {
        Response::redirect(self::appendFeedback($_SERVER['HTTP_REFERER'], $feedback));
    }
    static function appendFeedback(string $url = '', string $feedback = ''): string
    {
        $url = preg_replace('/(\?|&)feedback=[^$]+/','', $url);
        return $url . (preg_match('/\?[^=]+=/', $url) ? '&' : '?') . 'feedback=' . $feedback;
    }
}