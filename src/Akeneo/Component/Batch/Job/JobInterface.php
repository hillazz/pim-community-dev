<?php

namespace Akeneo\Component\Batch\Job;

use Akeneo\Component\Batch\Model\JobExecution;
use Akeneo\Component\Batch\Step\StepInterface;

/**
 * Batch domain object representing a job. Job is an explicit abstraction
 * representing the configuration of a job specified by a developer.
 *
 * Inspired by Spring Batch  org.springframework.batch.core.Job;
 *
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
interface JobInterface
{
    /**
     * @return string the name of this job
     */
    public function getName();


    /**
     * Return all the steps
     *
     * @return StepInterface[] steps
     */
    public function getSteps();

    /**
     * Retrieve the step with the given name. If there is no Step with the given
     * name, then return null.
     *
     * @param string $stepName
     *
     * @return StepInterface the Step
     */
    public function getStep($stepName);

    /**
     * Run the {@link JobExecution} and update the meta information like status
     * and statistics as necessary. This method should not throw any exceptions
     * for failed execution. Clients should be careful to inspect the
     * {@link JobExecution} status to determine success or failure.
     *
     * @param JobExecution $execution a {@link JobExecution}
     */
    public function execute(JobExecution $execution);
}
