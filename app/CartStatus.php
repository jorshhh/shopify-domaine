<?php

namespace App;

/***
 * CartStatus
 * Enum created to eliminate the chance of typos when updating states
 ***/

enum CartStatus: string
{
    const CREATED = 'created';

    const SUBMITTED = 'submitted';

    const FINISHED = 'finished';
}
