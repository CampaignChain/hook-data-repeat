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

namespace CampaignChain\Hook\DateRepeatBundle\Entity;

class DateRepeat
{
    protected $date;

    protected $startDate;

    protected $intervalStartDate;

    /**
     * A string defining the interval range as a relative date format with a
     * value in the future. For example, if the report operation is supposed
     * to run every hour, the interval would be "1 hour".
     *
     * Relative date formats are defined here:
     * http://php.net/manual/en/datetime.formats.relative.php
     *
     * TODO: Make sure that provided interval has a future value (not pointing
     * to the past).
     */
    protected $interval;

    /**
     * The date when the Action will be run the next time.
     */
    protected $intervalNextRun;

    protected $intervalEndDate;

    protected $endDate;

    /**
     * The number of times an Action is supposed to be repeated.
     */
    protected $intervalEndOccurrence;

    protected $timezone = 'UTC';

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return DateRepeat
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Duration
     */
    public function setIntervalStartDate(\DateTime $intervalStartDate = null)
    {
        $this->intervalStartDate = $intervalStartDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getIntervalStartDate()
    {
        return $this->intervalStartDate;
    }

    /**
     * Set interval
     *
     * @param string $interval
     * @return DateRepeat
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get interval
     *
     * @return string
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set nextRun
     *
     * @param \DateTime $nextRun
     * @return DateRepeat
     */
    public function setIntervalNextRun($intervalNextRun)
    {
        $this->intervalNextRun = $intervalNextRun;

        return $this;
    }

    /**
     * Get intervalNextRun
     *
     * @return \DateTime
     */
    public function getIntervalNextRun()
    {
        return $this->intervalNextRun;
    }

    /**
     * Set intervalEndDate
     *
     * @param \DateTime $intervalEndDate
     * @return Duration
     */
    public function setIntervalEndDate(\DateTime $intervalEndDate = null)
    {
        $this->intervalEndDate = $intervalEndDate;

        return $this;
    }

    /**
     * Get intervalEndDate
     *
     * @return \DateTime
     */
    public function getIntervalEndDate()
    {
        return $this->intervalEndDate;
    }

    /**
     * @param mixed $intervalEndOccurrence
     */
    public function setIntervalEndOccurrence($intervalEndOccurrence)
    {
        $this->intervalEndOccurrence = $intervalEndOccurrence;
    }

    /**
     * @return mixed
     */
    public function getIntervalEndOccurrence()
    {
        return $this->intervalEndOccurrence;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Duration
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Duration
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
