<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-30 07:01 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Core\MVC\Symfony;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Class ConfigurationScope.
 *
 * @package   IntProg\FeatureFlagBundle\Core\MVC\Symfony
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class ConfigurationScope extends ValueObject
{
    /**
     * Name of the siteaccess.
     *
     * @var string
     */
    public $name;

    /**
     * ConfigurationScope constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
