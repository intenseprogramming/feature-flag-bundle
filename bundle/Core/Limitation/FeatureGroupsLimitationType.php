<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-02 09:18 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Core\Limitation;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\User\Limitation as APILimitationValue;
use eZ\Publish\API\Repository\Values\User\UserReference as APIUserReference;
use eZ\Publish\API\Repository\Values\ValueObject as APIValueObject;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\Limitation\Type as SPILimitationTypeInterface;
use IntProg\FeatureFlagBundle\API\Repository\Values\User\Limitation\FeatureGroupsLimitation;
use IntProg\FeatureFlagBundle\Core\MVC\Symfony\FeatureGroups;
use IntProg\FeatureFlagBundle\Spi\Persistence\FeatureFlag;

/**
 * Class FeatureGroupsLimitationType.
 *
 * @package   IntProg\FeatureFlagBundle\Core\Limitation
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureGroupsLimitationType implements SPILimitationTypeInterface
{
    /** @var array $featureDefinitions */
    public $featureDefinitions;

    /**
     * FeatureGroupsLimitationType constructor.
     *
     * @param array $featureDefinitions
     */
    public function __construct(array $featureDefinitions)
    {
        $this->featureDefinitions = $featureDefinitions;
    }

    /**
     * Accepts a Limitation value and checks for structural validity.
     *
     * Makes sure LimitationValue object and ->limitationValues is of correct type.
     *
     * @param APILimitationValue $limitationValue
     *
     * @throws InvalidArgumentException If the value does not match the expected type/structure
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof FeatureGroupsLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'FeatureGroupsLimitation', $limitationValue);
        }

        if (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            // Value must be a CRC32, so can be either as string or integer.
            if (!is_string($value) && !is_int($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string or integer', $value);
            }
        }
    }

    /**
     * Makes sure LimitationValue->limitationValues is valid according to valueSchema().
     *
     * Make sure {@link acceptValue()} is checked first!
     *
     * @param APILimitationValue $limitationValue
     *
     * @return ValidationError[]
     */
    public function validate(APILimitationValue $limitationValue): array
    {
        return [];
    }

    /**
     * Create the Limitation Value.
     *
     * The is the method to create values as Limitation type needs value knowledge anyway in acceptValue,
     * the reverse relation is provided by means of identifier lookup (Value has identifier, and so does RoleService).
     *
     * @param mixed[] $limitationValues
     *
     * @return FeatureGroupsLimitation
     */
    public function buildValue(array $limitationValues): FeatureGroupsLimitation
    {
        return new FeatureGroupsLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * Evaluate ("Vote") against a main value object and targets for the context.
     *
     * @param APILimitationValue    $value
     * @param APIUserReference      $currentUser
     * @param APIValueObject        $object
     * @param APIValueObject[]|null $targets
     *
     * @return bool|null Returns one of ACCESS_* constants
     *
     * @throws InvalidArgumentException If any of the arguments are invalid
     *         Example: If LimitationValue is instance of ContentTypeLimitationValue, and Type is SectionLimitationType.
     *         However if $object or $targets is unsupported by ROLE limitation, ACCESS_ABSTAIN should be returned!
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): bool
    {
        if (!$value instanceof FeatureGroupsLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: FeatureGroupsLimitation');
        }

        if ($object instanceof FeatureFlag) {
            $object = new FeatureGroups($this->featureDefinitions[$object->identifier]['groups']);
        } elseif (!$object instanceof FeatureGroups) {
            throw new InvalidArgumentException(
                '$object',
                sprintf('Must be of type: %s or %s. %s given', FeatureFlag::class, FeatureGroups::class, get_class($object))
            );
        }

        $anyMatches = false;
        foreach ($value->limitationValues as $limitationValue) {
            if (in_array($limitationValue, $object->groups, true)) {
                $anyMatches = true;

                break;
            }
        }

        return (!empty($value->limitationValues) && $anyMatches);
    }

    /**
     * Returns Criterion for use in find() query.
     *
     * @param APILimitationValue $value
     * @param APIUserReference   $currentUser
     *
     * @throws NotImplementedException
     *
     * @codeCoverageIgnore
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser)
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * Returns info on valid $limitationValues.
     *
     * @throws NotImplementedException
     *
     * @codeCoverageIgnore
     */
    public function valueSchema()
    {
        throw new NotImplementedException(__METHOD__);
    }
}
