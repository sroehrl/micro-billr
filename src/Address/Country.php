<?php

namespace App\Address;

use Neoan\Model\Helper\DateTimeProperty;

enum Country: string
{
    case US = 'USA';
    case DE = 'GERMANY';

    public function localName(){
        return match ($this){
            Country::US => 'USA',
            Country::DE => 'Deutschland'
        };
    }
    public function addressFormat(){
        return match ($this){
            Country::US => '<div>
                                {{street}} <br>
                                {{place}} {{state}} {{postalCode}} <br>
                                ' . $this->localName() .'
                            </div>',
            Country::DE => '<div>
                                {{street}} <br>
                                {{postalCode}}, {{place}} <br>
                                ' . $this->localName() .'
                            </div>'
        };
    }
    public function dateFormat(DateTimeProperty $date) {
        return match ($this){
            Country::US => $date->dateTime->format('M d Y'),
            Country::DE => $date->dateTime->format('d.m.Y')
        };
    }
}
