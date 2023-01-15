<?php

namespace App\Product;

use Neoan\Enums\Direction;
use Neoan\Model\Interfaces\Transformation;

class PriceTransformation implements Transformation
{

    public function __invoke(array $inputOutput, Direction $direction, string $property): array
    {
        if($direction === Direction::IN && !empty($inputOutput[$property])) {
            $inputOutput[$property] = $inputOutput[$property] * 100;
        }
        if($direction === Direction::OUT && !empty($inputOutput[$property])) {
            $inputOutput[$property] = $inputOutput[$property] / 100;
        }
        return $inputOutput;
    }
}