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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DateRepeatMonthlyType extends AbstractType
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
            $data['interval'] = 1;
        } else {
            $data = $options['data'];
        }

        $builder
            ->add('interval', 'integer', array(
                'label' => 'Repeat every',
                'precision' => 0,
                'required' => true,
                'data' => $data['interval'],
                'attr' => array(
                    'min' => 1,
                    'max' => 12,
                    'input_group' => array(
                        'append' => 'months',
                    ),
                    'label_col' => 2,
                    'widget_col' => 4
                )
            ));
        $builder
            ->add('repeat_by', 'choice', array(
                'label' => 'Repeat by',
                'choices' => array(
                    'day_of_month'   => 'Day of the month',
                    'day_of_week' => 'Day of the week',
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => false,
            ));
        $builder->add('dom_occurrence', 'choice', array(
            'required' => false,
            'label' => false,
            'expanded' => false,
            'multiple' => false,
            'choices' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
                '13' => '13',
                '14' => '14',
                '15' => '15',
                '16' => '16',
                '17' => '17',
                '18' => '18',
                '19' => '19',
                '20' => '20',
                '21' => '21',
                '22' => '22',
                '23' => '23',
                '24' => '24',
                '25' => '25',
                '26' => '26',
                '27' => '27',
                '28' => '28',
                '29' => '29',
                '30' => '30',
            ),
            'attr' => array(
                'style' => 'inline',
                'minimum_results_for_search' => 40,
            ),
        ));
        $builder->add('dow_occurrence', 'choice', array(
            'label' => false,
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'first' => 'First',
                'second' => 'Second',
                'third' => 'Third',
                'last' => 'Last',
            ),
            'attr' => array(
                'style' => 'inline',
            ),
        ));
        $builder->add('day_of_week', 'choice', array(
            'label' => false,
            'expanded' => true,
            'multiple' => false,
            'choices' => array(
                'Mon' => 'Mon',
                'Tue' => 'Tue',
                'Wed' => 'Wed',
                'Thu' => 'Thu',
                'Fri' => 'Fri',
                'Sat' => 'Sat',
                'Sun' => 'Sun',
            ),
            'attr' => array(
                'style' => 'inline',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'campaignchain_hook_campaignchain_date_repeat_monthly';
    }
}