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

class DateRepeatWeeklyType extends AbstractType
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
                    'max' => 30,
                    'input_group' => array(
                        'append' => 'weeks',
                    ),
                    'label_col' => 2,
                    'widget_col' => 4
                )
            ));
        $builder->add('day_of_week', 'choice', array(
            'label' => 'Repeat on',
            'expanded' => true,
            'multiple' => false,
            'required' => false,
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
                'style' => 'horizontal',
            ),
        ));
    }

    public function configureOptionsOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'validation_groups' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'campaignchain_hook_campaignchain_date_repeat_daily';
    }
}