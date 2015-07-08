<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Hook\DateRepeatBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DateRepeatEndType extends AbstractType
{
    protected $container;
    protected $datetime;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->datetime = $this->container->get('campaignchain.core.util.datetime');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!is_array($options['data'])){
            $data['end'] = 'never';
        } else {
            $data = $options['data'];
        }

        $builder
            ->add('end', 'choice', array(
                'label' => false,
                'choices' => array(
                    'never'   => 'Never',
                    'occurrences' => 'After',
                    'date'   => 'On',
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'data' => $data['end'],
            ));
        $builder
            ->add('occurrences', 'integer', array(
                'label' => false,
                'precision' => 0,
                'required' => false,
                'attr' => array(
                    'min' => 0,
                    'input_group' => array(
                        'append' => 'occurrences',
                    ),
                    'label_col' => 2,
                    'widget_col' => 5
                )
            ));
        $builder
            ->add('interval_end_date', 'collot_datetime', array(
                'label' => false,
                'required' => false,
                'constraints' => array(),
                'model_timezone' => 'UTC',
                'view_timezone' => $this->datetime->getUserTimezone(),
                'pickerOptions' => array(
                    'format' => $this->datetime->getUserDatetimeFormat('datepicker'),
                    'weekStart' => 0,
                    'startDate' => $this->datetime->formatLocale($this->datetime->getUserNow()),
                    //'endDate' => $endDatePicker,
                    'autoclose' => true,
                    'startView' => 'month',
                    'minView' => 'hour',
                    'maxView' => 'decade',
                    'todayBtn' => false,
                    'todayHighlight' => true,
                    'keyboardNavigation' => true,
                    'language' => 'en',
                    'forceParse' => true,
                    'minuteStep' => 5,
                    'pickerReferer ' => 'default', //deprecated
                    'pickerPosition' => 'bottom-right',
                    'viewSelect' => 'hour',
                    'showMeridian' => false,
                    //                    'initialDate' => $startDatePicker,
                ),
                'attr' => array(
                    //'help_text' => $helpText,
                    'input_group' => array(
                        'append' => '<span class="fa fa-calendar">',
                    ),
                    'label_col' => 2,
                    'widget_col' => 6
                )
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => false,
            ]);
    }

    public function getName()
    {
        return 'campaignchain_hook_campaignchain_date_repeat_end';
    }
}