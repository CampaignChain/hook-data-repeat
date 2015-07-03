<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Hook\DateRepeatBundle\Entity;

class DateRepeat
{
    protected $startDate;

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
    protected $nextRun;

    protected $endDate;

    /**
     * The number of times an Action is supposed to be repeated.
     */
    protected $endOccurrence;

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
    public function setStartDate(\DateTime $startDate = null)
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
    public function setNextRun($nextRun)
    {
        $this->nextRun = $nextRun;

        return $this;
    }

    /**
     * Get nextRun
     *
     * @return \DateTime
     */
    public function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Duration
     */
    public function setEndDate(\DateTime $endDate = null)
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

    /**
     * @param mixed $endOccurrence
     */
    public function setEndOccurrence($endOccurrence)
    {
        $this->endOccurrence = $endOccurrence;
    }

    /**
     * @return mixed
     */
    public function getEndOccurrence()
    {
        return $this->endOccurrence;
    }
}
