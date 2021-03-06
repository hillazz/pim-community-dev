<?php

namespace spec\Akeneo\Component\Localization\Localizer;

use Akeneo\Component\Localization\Factory\DateFactory;
use Akeneo\Component\Localization\Validator\Constraints\DateFormat;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DateLocalizerSpec extends ObjectBehavior
{
    function let(ValidatorInterface $validator, DateFactory $dateFactory)
    {
        $this->beConstructedWith($validator, $dateFactory, ['pim_catalog_date']);
    }

    function it_is_a_localizer()
    {
        $this->shouldImplement('Akeneo\Component\Localization\Localizer\LocalizerInterface');
    }

    function it_supports_attribute_type()
    {
        $this->supports('pim_catalog_date')->shouldReturn(true);
        $this->supports('pim_catalog_number')->shouldReturn(false);
    }

    function it_validates_the_format()
    {
        $this->validate('28/10/2015', 'date', ['date_format' => 'd/m/Y'])->shouldReturn(null);
        $this->validate('01/10/2015', 'date', ['date_format' => 'd/m/Y'])->shouldReturn(null);
        $this->validate('2015/10/25', 'date', ['date_format' => 'Y/m/d'])->shouldReturn(null);
        $this->validate('2015/10/01', 'date', ['date_format' => 'Y/m/d'])->shouldReturn(null);
        $this->validate('2015-10-25', 'date', ['date_format' => 'Y-m-d'])->shouldReturn(null);
        $this->validate('2015-10-01', 'date', ['date_format' => 'Y-m-d'])->shouldReturn(null);
        $this->validate('', 'date', ['date_format' => 'Y-m-d'])->shouldReturn(null);
        $this->validate(null, 'date', ['date_format' => 'Y-m-d'])->shouldReturn(null);
        $this->validate(new \DateTime(), 'date', ['date_format' => 'Y-m-d'])->shouldReturn(null);
    }

    function it_returns_a_constraint_if_the_format_is_not_valid(
        $validator,
        ConstraintViolationListInterface $constraints
    ) {
        $validator->validate('28/10/2015', Argument::any())->willReturn($constraints);

        $this->validate('28/10/2015', 'date', ['date_format' => 'd-m-Y'])->shouldReturn($constraints);
    }

    function it_returns_a_constraint_if_date_format_does_not_respect_format_locale(
        $validator,
        $dateFactory,
        ConstraintViolationListInterface $constraints,
        \IntlDateFormatter $dateFormatter
    ) {
        $dateConstraint = new DateFormat();
        $dateConstraint->dateFormat = 'dd/MM/yyyy';
        $dateConstraint->path = 'date';
        $validator->validate('28-10-2015', $dateConstraint)->willReturn($constraints);

        $dateFactory->create(['locale' => 'fr_FR'])->willReturn($dateFormatter);
        $dateFormatter->getPattern()->willReturn('dd/MM/yyyy');
        $this->validate('28-10-2015', 'date', ['locale' => 'fr_FR'])->shouldReturn($constraints);
    }

    function it_delocalizes_with_date_format_option($dateFactory, \IntlDateFormatter $dateFormatter)
    {
        $dateFactory->create(['date_format' => 'dd/MM/yyyy'])->willReturn($dateFormatter);
        $dateFormatter->setLenient(false)->shouldBeCalled();
        $dateFormatter->parse('28/10/2015')->willReturn(1445986800);
        $dateFormatter->setPattern('yyyy-MM-dd')->shouldBeCalled();
        $dateFormatter->format(1445986800)->willReturn('2015-10-28');

        $this->delocalize('28/10/2015', ['date_format' => 'dd/MM/yyyy'])->shouldReturn('2015-10-28');
    }
}
