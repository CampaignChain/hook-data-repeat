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

namespace CampaignChain\Hook\DateRepeatBundle\EntityService;

use CampaignChain\CoreBundle\EntityService\HookServiceTriggerInterface;
use CampaignChain\CoreBundle\Entity\Hook;
use CampaignChain\Hook\DateRepeatBundle\Entity\DateRepeat;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DateRepeatService implements HookServiceTriggerInterface
{
    protected $em;
    protected $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function getHook($entity, $mode = Hook::MODE_DEFAULT){
        // If Action has not been created yet, return null for Hook.
        if(!$entity->getId()){
            return null;
        }

        $hook = new DateRepeat();
        $hook->setStartDate($entity->getStartDate());
        $hook->setEndDate($entity->getEndDate());
        $hook->setIntervalStartDate($entity->getIntervalStartDate());
        $hook->setInterval($entity->getInterval());
        $hook->setIntervalNextRun($entity->getIntervalNextRun());
        $hook->setIntervalEndOccurrence($entity->getIntervalEndOccurrence());
        $hook->setIntervalEndDate($entity->getIntervalEndDate());

        return $hook;
    }

    public function processHook($entity, $hook){
        if($hook->getIntervalStartDate()){
            $entity->setIntervalStartDate($hook->getIntervalStartDate());
        }

        $entity->setInterval($hook->getInterval());
        $entity->setIntervalNextRun($hook->getIntervalNextRun());

        if($hook->getIntervalEndDate()){
            $entity->setIntervalEndDate($hook->getIntervalEndDate());
            $entity->setIntervalEndOccurrence(null);
        } elseif($hook->getIntervalEndOccurrence()){
            $entity->setIntervalEndOccurrence($hook->getIntervalEndOccurrence());
            $entity->setIntervalEndDate(null);
        } else {
            $entity->setIntervalEndDate(null);
            $entity->setIntervalEndOccurrence(null);
        }

        // If the entity is an Activity and it equals the Operation, then
        // - the same dates will be set for the Operation
        // - the same trigger Hook will be set for the Operation
        $class = get_class($entity);
        if(strpos($class, 'CoreBundle\Entity\Activity') !== false && $entity->getEqualsOperation() == true){
            $operation = $entity->getOperations()[0];
            $operation->setStartDate($hook->getStartDate());
            $operation->setEndDate($hook->getEndDate());
            $operation->setIntervalStartDate($entity->getIntervalStartDate());
            $operation->setIntervalEndDate($entity->getIntervalEndDate());
            $operation->setInterval($entity->getInterval());
            $operation->setIntervalNextRun($entity->getIntervalNextRun());
            $operation->setTriggerHook($entity->getTriggerHook());
        }

        return $entity;
    }

    public function arrayToObject($hookData){
        if(is_array($hookData) && count($hookData)){
            $datetimeUtil = $this->container->get('campaignchain.core.util.datetime');

            $newData = $this->requestDataToPreSubmitData($hookData);

            $hook = new DateRepeat();
            $hook->setInterval($newData['interval']);

            if(isset($newData['interval_start_date'])) {
                $hook->setIntervalStartDate(
                    new \DateTime(
                        $newData['interval_start_date'],
                        new \DateTimeZone($newData['timezone'])
                    )
                );
            }

            $hook->setIntervalNextRun(
                new \DateTime(
                    $newData['interval_next_run'],
                    new \DateTimeZone($newData['timezone'])
                )
            );

            if(isset($newData['interval_end_occurrence'])) {
                $hook->setIntervalEndOccurrence($newData['interval_end_occurrence']);
            }

            if(isset($newData['interval_end_date'])) {
                $hook->setIntervalEndDate(
                    new \DateTime(
                        $newData['interval_end_date'],
                        new \DateTimeZone($newData['timezone'])
                    )
                );
            }
        }

        return $hook;
    }

    /**
     * Maps a form's request data array to an array that fits with the
     * DataRepeat entity's properties.
     *
     * Typically, you use this in a form's PRE_SUBMIT event.
     *
     * @param $data array
     * @return array
     */
    public function requestDataToPreSubmitData($data)
    {
        $datetimeUtil = $this->container->get('campaignchain.core.util.datetime');

        $frequency = $data['frequency'];
        $options = $data[$frequency];
        $startDate = \DateTime::createFromFormat(
            $datetimeUtil->getUserDatetimeFormat('php_date'),
            $data['interval_start_date']
        );

        // Preserve this for later adjustment. Might be a bug
        // in PHP that sets time to 00:00 when working with relative dates?
        $startDateTime = $startDate->format('H:i');

        $newData = array();
        $nextRun = null;

        $newData['timezone'] = $data['timezone'];
        $newData['interval_start_date'] = $data['interval_start_date'];

        switch($frequency){
            case 'daily':
                $newData['interval'] = '+'.$options['interval'].' days';
                $nextRun = $startDate->modify($newData['interval']);
                break;
            case 'weekly':
                if(isset($options['day_of_week']) && strlen($options['day_of_week'])){
                    $newData['interval'] = 'Next '.$options['day_of_week']
                        .' +'.$options['interval'].' weeks';
                } else {
                    $newData['interval'] = '+'.$options['interval'].' weeks';
                }
                $nextRun = $startDate->modify($newData['interval']);
                break;
            case 'monthly':
                switch($options['repeat_by']){
                    case 'day_of_month':
                        /*
                         * Adding days in a "last day of this month" statement
                         * does not work in PHP. Seems like this is because
                         * the word "day" already occurs in it. Hence, we
                         * work around this by adding the equivalent in hours.
                         */
                        $newData['interval'] = 'last day of this month '
                            .'+'.($options['dom_occurrence']*24).' hours';

                        $nextRun = $startDate->modify($newData['interval']);
                        break;
                    case 'day_of_week':
                        $newData['interval'] = $options['dow_occurrence'].' '
                            .$options['day_of_week'].' of next month';

                        // Is the start date prior to the defined day of the month?
                        $nextRunThisMonth = clone $startDate;
                        $nextRunThisMonth->modify(
                            $options['dow_occurrence'].' '
                            .$options['day_of_week'].' of this month'
                        );
                        if($startDate < $nextRunThisMonth){
                            $nextRun = $nextRunThisMonth;
                        } else {
                            $nextRun = $startDate->modify($newData['interval']);
                        }
                        break;
                }

                // Add the monthly interval.
                $newData['interval'] .= ' +'.($options['interval'] - 1).' months';

                break;
            case 'yearly':
                $newData['interval'] = '+'.$options['interval'].' years';
                $nextRun = $startDate->modify($newData['interval']);
                break;
        }

        // Make sure we preserve the time.
        $nextRun = new \DateTime(
            $nextRun->format('Y-m-d')
            .' '.$startDateTime
        );
        $newData['interval_next_run'] = $nextRun->format($datetimeUtil->getUserDatetimeFormat('php_date'));

        // TODO: Throw error if next run after end date.

        $newData['interval_end_occurrence'] = null;
        $newData['interval_end_date'] = null;

        switch($data['ends']['end']){
            case 'occurrences':
                $newData['interval_end_occurrence'] = $data['ends']['occurrences'];
                break;
            case 'date':
                $newData['interval_end_date'] = $data['ends']['interval_end_date'];
                break;
        }

        return $newData;
    }

    public function tplInline($entity){
        return $this->container->get('templating')->render(
            'CampaignChainHookDateRepeatBundle::inline.html.twig',
            array('entity' => $entity)
        );
    }

    /**
     * Returns the corresponding start date field attribute name as specified in the respective form type.
     *
     * @return string
     */
    public function getStartDateIdentifier(){
        return 'date';
    }

    /**
     * Returns the corresponding end date field attribute name as specified in the respective form type.
     *
     * @return string
     */
    public function getEndDateIdentifier(){
        return 'date';
    }
}