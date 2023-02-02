<?php

namespace Config;

use Neoan\Enums\Direction;
use Neoan\Helper\Env;
use Neoan\Helper\Str;
use Neoan\Model\Interfaces\Transformation;

class EncryptionTransformer implements Transformation
{

    public function __invoke(array $inputOutput, Direction $direction, string $property): array
    {
        if($direction === Direction::IN && isset($inputOutput[$property])){
            $inputOutput[$property] = Str::encrypt($inputOutput[$property], Env::get('ENCRYPTION_KEY', 'setThisPlease'));
        } elseif ($direction === Direction::OUT && isset($inputOutput[$property])){
            $inputOutput[$property] = Str::decrypt($inputOutput[$property], Env::get('ENCRYPTION_KEY', 'setThisPlease'));
        }
        return $inputOutput;
    }
}