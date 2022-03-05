<?php

/*
 * This file is part of the CustomTimesheetFormBundle for Kimai 2.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\CustomTimesheetFormBundle\EventSubscriber;

use App\Event\ThemeEvent;
use Symfony\Component\Asset\Packages;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class ThemeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var Packages
     */
    private $packages;

    public function __construct(\Twig\Environment $twig, Packages $packages)
    {
        $this->twig = $twig;

        $this->packages = $packages;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ThemeEvent::JAVASCRIPT => ['renderContentAfter', 100],
        ];
    }

    /**
     * @param ThemeEvent $event
     */
    public function renderContentAfter(ThemeEvent $event)
    {
        $html = '';

        // timesheets can be edited/created from all pages via toolbar.
        // add timepicker scripts/css to all html pages

        $html .='
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css" 
    integrity="sha512-GgUcFJ5lgRdt/8m5A0d0qEnsoi8cDoF0d6q+RirBPtL423Qsj5cI9OxQ5hWvPi5jjvTLM/YhaaFuIeWCLi6lyQ==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.js" 
    integrity="sha512-17lKwKi7MLRVxOz4ttjSYkwp92tbZNNr2iFyEd22hSZpQr/OnPopmgH8ayN4kkSqHlqMmefHmQU43sjeJDWGKg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
';


        $url = $this->packages->getUrl('bundles/customtimesheetform/js/visible-media-query.js');
        $html .= '
    <script src="' . $url . '"></script>
    <div class="device-xs visible-xs-block"></div>
    <div class="device-sm visible-sm-block"></div>
    <div class="device-md visible-md-block"></div>
    <div class="device-lg visible-lg-block"></div>
';

        $event->addContent($html);
    }
}
