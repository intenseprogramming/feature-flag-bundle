<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-09-30 08:10 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

declare(strict_types=1);

namespace IntProg\FeatureFlagBundle\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;
use EzSystems\RepositoryForms\Limitation\LimitationValueMapperInterface;
use EzSystems\RepositoryForms\Limitation\Mapper\MultipleSelectionBasedMapper;
use EzSystems\RepositoryForms\Translation\LimitationTranslationExtractor;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * Class ConfigurationScopeLimitationMapper.
 *
 * @package   ${NAMESPACE}
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class ConfigurationScopeLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    /** @var array $siteaccessList */
    protected $siteaccessList;

    /** @var array $siteaccessGroups */
    protected $siteaccessGroups;

    /**
     * ConfigurationScopeLimitationMapper constructor.
     *
     * @param array $siteaccessList
     * @param array $siteaccessGroups
     */
    public function __construct(array $siteaccessList, array $siteaccessGroups)
    {
        $this->siteaccessList = $siteaccessList;
        $this->siteaccessGroups = $siteaccessGroups;
    }

    /**
     * Map the limitation values, in order to pass them as context of limitation value rendering.
     *
     * @param Limitation $limitation
     *
     * @return mixed[]
     */
    public function mapLimitationValue(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    /**
     * Returns value choices to display, as expected by the "choices" option from Choice field.
     *
     * @return array
     */
    protected function getSelectionChoices(): array
    {
        $groups = [];
        $list   = [];
        $system = [];

        foreach (['global', 'default'] as $item) {
            $system['intprog.feature_flag.scope.' . $item] = $item;
        }

        foreach ($this->siteaccessList as $item) {
            $list['intprog.feature_flag.scope.' . $item] = $item;
        }

        foreach (array_keys($this->siteaccessGroups) as $item) {
            $groups['intprog.feature_flag.scope.' . $item] = $item;
        }

        return [
            'intprog.feature_flag.general'               => $system,
            'intprog.feature_flag.siteaccess.list'       => $list,
            'intprog.feature_flag.siteaccess.group.list' => $groups,
        ];
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data): void
    {
        $options = $this->getChoiceFieldOptions() + [
            'multiple' => true,
            'label' => LimitationTranslationExtractor::identifierToLabel($data->getIdentifier()),
            'required' => false,
        ];
        $choices = $this->getSelectionChoices();
        $options += ['choices' => $choices];
        $form->add('limitationValues', ChoiceType::class, $options);
    }
}
