<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-02 09:07 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\EzPlatformAdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use EzSystems\EzPlatformAdminUi\Translation\Extractor\LimitationTranslationExtractor;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * Class FeatureGroupsLimitationMapper.
 *
 * @package   IntProg\FeatureFlagBundle\Limitation
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2020 Intense Programming
 */
class FeatureGroupsLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    private $featureGroups;

    /**
     * FeatureGroupsLimitationMapper constructor.
     *
     * @param array $featureGroups
     */
    public function __construct(array $featureGroups)
    {
        $this->featureGroups = $featureGroups;
    }

    /**
     * @param Limitation $limitation
     *
     * @return mixed[]
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    /**
     * @return array
     */
    protected function getSelectionChoices(): array
    {
        $groups = [];

        foreach ($this->featureGroups as $item) {
            $groups['intprog.feature_group.scope.' . $item] = $item;
        }

        asort($groups);

        return $groups;
    }

    /**
     * @param FormInterface $form
     * @param Limitation    $data
     *
     * @return void
     */
    public function mapLimitationForm(FormInterface $form, Limitation $data): void
    {
        $options = $this->getChoiceFieldOptions() + [
            'multiple' => true,
            'label' => LimitationTranslationExtractor::identifierToLabel($data->getIdentifier()),
            'required' => false,
        ];
        $choices = $this->getSelectionChoices();
        $options += ['choices' => $choices];
        $options += ['translation_domain' => 'ezrepoforms_policies'];
        $form->add('limitationValues', ChoiceType::class, $options);
    }
}
