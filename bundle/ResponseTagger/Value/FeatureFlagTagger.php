<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-01-03 09:36 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\AbstractValueTagger;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;

/**
 * Class FeatureFlagTagger.
 *
 * @package   IntProg\FeatureFlagBundle\ResponseTagger\Value
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureFlagTagger extends AbstractValueTagger
{
    /**
     * Extracts tags from a value.
     *
     * @param mixed $value
     *
     * @return FeatureFlagTagger
     */
    public function tag($value): self
    {
        if ($value instanceof FeatureFlag) {
            $this->responseTagger->addTags(array_map(
                static function (string $scope) use ($value) {
                    return 'feature-flag-' . $scope . '-' . $value->identifier;
                },
                $value->checkedScopes
            ));
        }

        return $this;
    }
}
