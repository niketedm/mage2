<?php

namespace Mancini\ContactUs\Controller\Index;

use Mancini\ContactUs\Controller\Index;

class Employment extends Index
{
    /** @var string */
    protected $redirectPath = 'employment';

    /** @var string */
    protected $noPostPath = 'employment';

    /** @var string */
    protected $persistorKey = 'employment';

    /** @var string */
    protected $subject = 'Employment';

    /** @var bool */
    protected $uploadNeeded = true;
}
