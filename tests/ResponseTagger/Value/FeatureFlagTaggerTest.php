<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2020-06-03 06:52 pm
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2020, Intense Programming
 */

namespace IntProg\FeatureFlagBundle\ResponseTagger\Value;

use FOS\HttpCache\ResponseTagger;
use IntProg\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PHPUnit\Framework\TestCase;

class FeatureFlagTaggerTest extends TestCase
{
    /**
     * @return void
     * @test
     */
    public function tagger_should_add_converted_feature_strings_to_response_tagger(): void
    {
        $responseTagger = $this->createMock(ResponseTagger::class);
        $responseTagger->expects($this->at(0))->method('addTags')->with([
            'ipff-global-identifier',
            'ipff-default-identifier',
            'ipff-_definition_-identifier',
        ]);
        $responseTagger->expects($this->at(1))->method('addTags')->with([
            'ipff-global-identifier',
        ]);

        $tagger = new FeatureFlagTagger($responseTagger);

        $featureFromDefinition = new FeatureFlag([
            'checkedScopes' => ['global', 'default', '_definition_'],
            'identifier' => 'identifier',
            'groups' => [],
        ]);

        $featureFromGlobal = new FeatureFlag([
            'checkedScopes' => ['global'],
            'identifier' => 'identifier',
            'groups' => [],
        ]);

        $tagger->tag($featureFromDefinition);
        $tagger->tag($featureFromGlobal);
    }
}
