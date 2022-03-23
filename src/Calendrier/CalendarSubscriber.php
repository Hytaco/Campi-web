<?php


namespace  App\EventSubscriber;

use App\Entity\Livraisons;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;;
use App\Repository\LivraisonsRepository ;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $lv ;
    function __construct(LivraisonsRepository $lv){
$this->lv=$lv;
    }
    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // You may want to make a custom query from your database to fill the calendar
        // You may want to make a custom query from your database to fill the calendar
        $livs = $this->lv->findAll();
        /** @var Livraisons $liv */
        foreach ($livs as $liv) {
            $calendar->addEvent(new Event(
                ' Liv ' . strval($liv->getId()),
                new \DateTime($liv->getDatelivraison()->format('d-m-Y'))
            ));
        }

    }
}