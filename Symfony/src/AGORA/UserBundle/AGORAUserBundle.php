<?php

namespace AGORA\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AGORAUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}