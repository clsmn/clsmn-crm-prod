<?php

namespace App\Listeners\Backend\Lead;

/**
 * Class LeadEventListener.
 */
class LeadEventListener
{
    /**
     * @var string
     */
    private $history_slug = 'Lead';

    function onLeadAssigned($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('assigned')
                ->withEntity($event->lead->id)
                ->withText('trans("history.backend.lead.assigned")')
                ->withIcon('plus')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => $event->user->name,
                    'date_string' => $event->lead->created_at->format('d M y'),
                ])
                ->log();
    }

    function onLeadOpened($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('opened')
                ->withEntity($event->lead->id)
                ->withText('trans("history.backend.lead.opened")')
                ->withIcon('folder-open-o')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => $event->user->name,
                    'date_string' => $event->lead->created_at->format('d M y'),
                ])
                ->log();
    }

    function onLeadCalled($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('call')
                ->withEntity($event->lead->id)
                ->withSubEntity($event->callHistory->id)
                ->withText('trans("history.backend.lead.called")')
                ->withIcon('phone')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => $event->user->name,
                    'date_string' => $event->lead->created_at->format('d M y'),
                ])
                ->log();
    }

    function onLeadNoteAdded($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('note')
                ->withEntity($event->lead->id)
                ->withSubEntity($event->leadNote->id)
                ->withText('trans("history.backend.lead.note_added")')
                ->withIcon('sticky-note')
                ->withClass('bg-green')
                ->withAssets([
                    'user_string' => $event->user->name,
                    'date_string' => $event->lead->created_at->format('d M y'),
                ])
                ->log();
    }

    function onLeadChangeNumber($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('change_number')
                ->withEntity($event->lead->id)
                ->withText('Primary number change from '.$event->oldNumber.' to '. $event->lead->phone)
                ->withIcon('exchange')
                ->withClass('bg-green')
                ->log();
    }

    function onLeadUnattachedCall($event)
    {
        history()->withType($this->history_slug)
                ->withSubType('unattached_call')
                ->withEntity($event->callRecord->lead_id)
                ->withSubEntity($event->callRecord->id)
                ->withUserId($event->user->id)
                ->withText('call unattached')
                ->withIcon('phone')
                ->withClass('bg-green')
                ->log();
    }

    public function subscribe($events)
    {
        $events->listen(
            \App\Events\Backend\Lead\LeadAssigned::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadAssigned'
        );

        $events->listen(
            \App\Events\Backend\Lead\LeadOpened::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadOpened'
        );

        $events->listen(
            \App\Events\Backend\Lead\LeadCalled::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadCalled'
        );

        $events->listen(
            \App\Events\Backend\Lead\LeadNoteAdded::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadNoteAdded'
        );

        $events->listen(
            \App\Events\Backend\Lead\LeadChangeNumber::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadChangeNumber'
        );

        $events->listen(
            \App\Events\Backend\Lead\LeadUnattachedCall::class,
            'App\Listeners\Backend\Lead\LeadEventListener@onLeadUnattachedCall'
        );
    }
}