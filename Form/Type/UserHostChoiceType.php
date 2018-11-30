<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Service\RightsParser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserHostChoiceType, used for selection of UserHost Requirements when editing commands
 *
 * @author  Daniel Fischer <dfischer000@gmail.com>
 */
class UserHostChoiceType extends AbstractType
{
    private $rightsParser;

    /**
     * @param RightsParser $rightsParser
     */
    public function __construct(RightsParser $rightsParser)
    {
        $this->rightsParser = $rightsParser;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'wrapper_attr' => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler',
                'choices' => $this->rightsParser->getRights()
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }
}
