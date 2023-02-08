<?php

namespace Config;

use Neoan\Enums\Direction;
use Neoan\Helper\Str;

class ShortSlugTransformer implements \Neoan\Model\Interfaces\Transformation
{

    public function __invoke(array $inputOutput, Direction $direction, string $property): array
    {
        if($direction === Direction::IN && !isset($inputOutput[$property])) {
            $inputOutput[$property] = Str::randomAlphaNumeric(6);
        }
        return $inputOutput;
    }
}