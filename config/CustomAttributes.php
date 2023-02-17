<?php

namespace Config;

use Neoan3\Apps\Template\Template;
use NumberFormatter;

class CustomAttributes
{
    static function gender(\DOMAttr &$attr, $contextData = []):void
    {
        $attr->parentNode->nodeValue = match ((int) $attr->nodeValue){
            0 => 'Mrs.',
            1 => 'Mr.',
            2 => 'Mx.'
        };
    }
    static function renderDecimal(\DOMAttr &$attr, $contextData = []): void
    {
        $fmt = new NumberFormatter($_SERVER['HTTP_ACCEPT_LANGUAGE'], NumberFormatter::DECIMAL );
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
        if(is_numeric($attr->parentNode->nodeValue)){
            $attr->parentNode->nodeValue = $fmt->format((float) $attr->parentNode->nodeValue);
        }

    }
    static function renderPartial(\DOMAttr &$attr, $templatePath, $contextData = []): void
    {
        if(!$attr->parentNode->hasChildNodes()){
            foreach($attr->parentNode->attributes as $sibling){
                if(str_starts_with($sibling->name, 'as-')){
                    $candidate = $contextData;
                    foreach(explode('.', $sibling->nodeValue) as $part){
                        $candidate = $candidate[$part];
                    }

                    $contextData[substr($sibling->name,3)] = $candidate;
                }
            }

            $htmlString = Template::embraceFromFile($templatePath . $attr->nodeValue, $contextData);
            $fresh = new \DOMDocument();
            @$fresh->loadHTML( $htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $imported = $attr->ownerDocument->importNode($fresh->documentElement, true);
            $attr->parentNode->appendChild($imported);
        }
    }
    static function timelineIcon(\DOMAttr &$attr, $contextData = []): void
    {
        if($attr->parentNode->firstChild) {
            $attr->parentNode->removeChild($attr->parentNode->firstChild);
        }

        $html = match (trim($attr->nodeValue)){
            'project created' => '<div class="bg-secondary w-6 border-rounder text-white p-1" >
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>',
            'invoice created',
            'estimate created' => '<div class="bg-accent w-6 border-rounder text-white p-1" >
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                        </svg>
                    </div>',
            'invoice sent out',
            'estimate sent out' => '<div class="bg-warning-dark w-6 border-rounder text-white p-1" >
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                        </svg>
                    </div>',
            'project status change' => '<div class="bg-primary-light w-6 border-rounder text-white p-1" >
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                        </svg>
                    </div>',
            'estimate rescinded',
            'invoice rescinded' => '<div class="bg-danger-light w-6 border-rounder text-white p-1" >
                        <svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z"></path>
                        </svg>
                    </div>',
            'custom' => '<div class="bg-secondary w-6 border-rounder text-white p-1" >
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"></path>
                            </svg>
                        </div>',
            default => ''
        };
        try{
            $fresh = new \DOMDocument();
            @$fresh->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $attr->parentNode->appendChild($attr->ownerDocument->importNode($fresh->documentElement, true));
        } catch (\Error $e) {}

    }
}