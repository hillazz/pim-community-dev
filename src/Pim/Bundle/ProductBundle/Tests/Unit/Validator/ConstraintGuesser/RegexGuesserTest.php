<?php

namespace Pim\Bundle\ProductBundle\Tests\Unit\Validator\ConstraintGuesser;

use Oro\Bundle\FlexibleEntityBundle\AttributeType\AbstractAttributeType;
use Pim\Bundle\ProductBundle\Validator\ConstraintGuesser\RegexGuesser;

/**
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RegexGuesserTest extends ConstraintGuesserTest
{
    public function setUp()
    {
        $this->target = new RegexGuesser;
    }

    public function testInstanceOfContraintGuesserInterface()
    {
        $this->assertInstanceOf('Oro\Bundle\FlexibleEntityBundle\Form\Validator\ConstraintGuesserInterface', $this->target);
    }

    public function testSupportVarcharAttribute()
    {
        $this->assertTrue($this->target->supportAttribute(
            $this->getAttributeMock(array(
                'backendType' => AbstractAttributeType::BACKEND_TYPE_VARCHAR
            ))
        ));
    }

    public function testGuessMinMaxConstraint()
    {
        $constraints = $this->target->guessConstraints($this->getAttributeMock(array(
            'backendType'      => AbstractAttributeType::BACKEND_TYPE_VARCHAR,
            'validationRule'   => 'regexp',
            'validationRegexp' => '/foo/',
        )));

        $this->assertContainsInstanceOf('Symfony\Component\Validator\Constraints\Regex', $constraints);
        $this->assertConstraintsConfiguration('Symfony\Component\Validator\Constraints\Regex', $constraints, array(
            'pattern' => '/foo/',
        ));
    }

    public function testDoNotGuessRangeConstraint()
    {
        $this->assertEquals(0, count($this->target->guessConstraints($this->getAttributeMock(array(
            'backendType'    => AbstractAttributeType::BACKEND_TYPE_VARCHAR,
            'validationRule' => 'regexp',
        )))));

        $this->assertEquals(0, count($this->target->guessConstraints($this->getAttributeMock(array(
            'backendType'    => AbstractAttributeType::BACKEND_TYPE_VARCHAR,
            'validationRule' => null,
        )))));
    }
}
