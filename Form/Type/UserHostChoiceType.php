<?php

namespace JMose\CommandSchedulerBundle\Form\Type;

use JMose\CommandSchedulerBundle\Service\RightsParser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserHostChoiceType, used for selection of UserHost Reequirements when editing commands
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
//                'data_class' => 'JMose\CommandSchedulerBundle\Entity\UserHost',
                'wrapper_attr' => 'default_wrapper',
                'translation_domain' => 'JMoseCommandScheduler',
                'choices' => $this->rightsParser->getRights()
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'rights_choice';
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'choice';
    }
}
