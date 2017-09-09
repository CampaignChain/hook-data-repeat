<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Hook\DateRepeatBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                    'pickerPosition' => 'top-right',
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'campaignchain_hook_campaignchain_date_repeat_end';
    }
}