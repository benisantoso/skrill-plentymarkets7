<?php

namespace Skrill\Containers;

use Plenty\Plugin\Templates\Twig;
    
class SkrillPaymentsContainers
{
    public function call(Twig $twig):string
    {
        return $twig->render('Skrill::content.SkrillPaymentsContainers');
    }
}