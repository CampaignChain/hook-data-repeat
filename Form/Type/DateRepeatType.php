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

use CampaignChain\CoreBundle\Form\Type\HookType;
use CampaignChain\Hook\DateRepeatBundle\EntityService\DateRepeatService;
use SC\DatetimepickerBundle\Form\Type\DatetimeType;
use Symfony\Component\Form\FormBuilderInterface;
use CampaignChain\CoreBundle\Util\DateTimeUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DateRepeatType extends HookType
{
    protected $datetime;
    protected $dateRepeatService;

    public function __construct(DateTimeUtil $dateTimeUtil, DateRepeatService $dateRepeatService)
    {
        $this->datetime = $dateTimeUtil;
        $this->dateRepeatService = $dateRepeatService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setOptions($options);

        $hook = $options['data'];
        $dataDaily = null;
        $dataWeekly = null;
        $dataMonthly = null;
        $dataYearly = null;
        $dataEnds = null;

        /*
         * Pre-fill form if data is available.
         */
        if(!$hook || !$hook->getInterval()){
            // Default values.
            $data['frequency'] = 'daily';
            $data['interval_start_date'] = $this->datetime->getUserNow();
        } else {
            // Fill form values from Hook data.
            if (strpos($hook->getInterval(),'days') !== false) {
                $data['frequency'] = 'daily';
                $intervalParts = explode(' ',$hook->getInterval());
                $dataDaily['interval'] = str_replace('+', '', $intervalParts[0]);
            } elseif (strpos($hook->getInterval(),'weeks') !== false) {
                $data['frequency'] = 'weekly';
                $intervalParts = explode(' ',$hook->getInterval());
                if (strpos($hook->getInterval(),'Next') !== false) {
                    $dataWeekly['interval'] = str_replace('+', '', $intervalParts[2]);
                    $dataWeekly['day_of_week'] = $intervalParts[1];
                } else {
                    $dataWeekly['interval'] = str_replace('+', '', $intervalParts[0]);
                }
            } elseif (strpos($hook->getInterval(),'month') !== false) {
                $data['frequency'] = 'monthly';
                $intervalParts = explode(' ',$hook->getInterval());

                if (strpos($hook->getInterval(),'hours') !== false) {
                    // Day of month
                    $dataMonthly['repeat_by'] = 'day_of_month';
                    $dataMonthly['interval'] = str_replace('+', '', $intervalParts[7]) + 1;
                    $dataMonthly['dom_occurrence'] = str_replace('+', '', $intervalParts[5])/24;
                } else {
                    // Day of week
                    $dataMonthly['repeat_by'] = 'day_of_week';
                    $dataMonthly['interval'] = str_replace('+', '', $intervalParts[5]);
                    $dataMonthly['dow_occurrence'] = $intervalParts[0];
                    $dataMonthly['day_of_week'] = $intervalParts[1];
                }
            } elseif (strpos($hook->getInterval(),'years') !== false) {
                $data['frequency'] = 'yearly';
                $intervalParts = explode(' ',$hook->getInterval());
                $dataYearly['interval'] = str_replace('+', '', $intervalParts[0]);
            }

            if($hook->getIntervalStartDate()){
                $data['interval_start_date'] = $hook->getIntervalStartDate();
            } else {
                $data['interval_start_date'] = $this->datetime->getUserNow();
            }

            if($hook->getIntervalEndOccurrence()){
                $dataEnds['end'] = 'occurrences';
                $dataEnds['occurrences'] = $hook->getIntervalEndOccurrence();
            }

            if($hook->getIntervalEndDate()){
                $dataEnds['end'] = 'date';
                $dataEnds['interval_end_date'] = $hook->getIntervalEndDate();
            }
        }

        if(!isset($this->hooksOptions['disabled'])){
            $this->hooksOptions['disabled'] = false;
        }

        /*
         * Building the form.
         */
        $builder->add('frequency', 'choice', array(
            'label' => 'Repeats',
            'mapped' => false,
            'choices' => array(
                'Daily'     => 'daily',
                'Weekly'    => 'weekly',
                'Monthly'   => 'monthly',
                'Yearly'    => 'yearly',
            ),
            'data' => $data['frequency'],
            'attr' => array('label_col' => 2, 'widget_col' => 6)
        ));
        $builder->add('daily', DateRepeatDailyType::class, array(
            'label' => false,
            'mapped' => false,
            'required' => false,
            'data' => $dataDaily,
            'attr' => array('label_col' => 2, 'widget_col' => 10)
        ));
        $builder->add('weekly', DateRepeatWeeklyType::class, array(
            'label' => false,
            'mapped' => false,
            'required' => false,
            'data' => $dataWeekly,
            'attr' => array('label_col' => 2, 'widget_col' => 10)
        ));
        $builder->add('monthly', DateRepeatMonthlyType::class, array(
            'label' => false,
            'mapped' => false,
            'required' => false,
            'data' => $dataMonthly,
            'attr' => array('label_col' => 4, 'widget_col' => 8)
        ));
        $builder->add('yearly', DateRepeatYearlyType::class, array(
            'label' => false,
            'mapped' => false,
            'required' => false,
            'data' => $dataYearly,
            'attr' => array('label_col' => 4, 'widget_col' => 8)
        ));
        $builder
            ->add('interval_start_date', DatetimeType::class, array(
                'label' => 'Starts on',
                'required' => true,
                'mapped' => true,
                'data' => $data['interval_start_date'],
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
        $builder
            ->add('ends', DateRepeatEndType::class, array(
                'label' => 'Ends',
                'mapped' => false,
                'required' => false,
                'data' => $dataEnds,
                'attr' => array('label_col' => 2, 'widget_col' => 10)
            ));
        $builder
            ->add('timezone', 'hidden', array(
                'data' => $this->datetime->getUserTimezone(),
            ));

        /*
         * This event listener rewrites the array generated with the various
         * sub form to match the DateRepeat entity.
         */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = $event->getData();

            $newData = $this->dateRepeatService->requestDataToPreSubmitData($data);

            $form = $event->getForm();
            $form->add('interval', 'text');
            
            $form->add('interval_start_date', DatetimeType::class, array(
                'required' => true,
                'mapped' => true,
                'constraints' => array(),
                'model_timezone' => 'UTC',
                'view_timezone' => $this->datetime->getUserTimezone(),
                'pickerOptions' => array(
                    'format' => $this->datetime->getUserDatetimeFormat('datepicker'),
                ),
            ));

            $form->add('interval_next_run', DatetimeType::class, array(
                'required' => true,
                'mapped' => true,
                'constraints' => array(),
                'model_timezone' => 'UTC',
                'view_timezone' => $this->datetime->getUserTimezone(),
                'pickerOptions' => array(
                    'format' => $this->datetime->getUserDatetimeFormat('datepicker'),
                ),
            ));

            $form->add('interval_end_occurrence', 'integer');
            $form->add('interval_end_date', DatetimeType::class, array(
                'required' => false,
                'mapped' => true,
                'constraints' => array(),
                'model_timezone' => 'UTC',
                'view_timezone' => $this->datetime->getUserTimezone(),
                'pickerOptions' => array(
                    'format' => $this->datetime->getUserDatetimeFormat('datepicker'),
                ),
            ));

            $event->setData($newData);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'validation_groups' => false,
                'data_class'      => 'CampaignChain\Hook\DateRepeatBundle\Entity\DateRepeat',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'campaignchain_hook_campaignchain_date_repeat';
    }
}