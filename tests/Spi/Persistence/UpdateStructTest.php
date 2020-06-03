<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-03 06:46 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Spi\Persistence;

use PHPUnit\Framework\TestCase;

class UpdateStructTest extends TestCase
{
    /**
     * @return void
     * @test
     */
    public function get_feature_should_return_feature_added_to_constructor(): void
    {
        $feature = new FeatureFlag();
        $updateStruct = new UpdateStruct($feature);

        $this->assertEquals($feature, $updateStruct->getFeature());
    }
}
