<?php

namespace Akeneo\Component\Batch\Step;

/**
 * Determine if a step needs a working directory to perform its work.
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
interface WorkingDirectoryAwareInterface
{
    const CONTEXT_PARAMETER = 'workingDirectory';

    public function getWorkingDirectory();
}
