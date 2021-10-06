<?php

return [

    /*
    |--------------------------------------------------------------------------
    | History Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain strings associated to the
    | system adding lines to the history table.
    |
    */

    'backend' => [
        'none'            => 'There is no recent history.',
        'none_for_type'   => 'There is no history for this type.',
        'none_for_entity' => 'There is no history for this :entity.',
        'recent_history'  => 'Recent History',

        'roles' => [
            'created' => 'created role',
            'deleted' => 'deleted role',
            'updated' => 'updated role',
        ],
        'users' => [
            'changed_password'    => 'changed password for user',
            'created'             => 'created user',
            'deactivated'         => 'deactivated user',
            'deleted'             => 'deleted user',
            'permanently_deleted' => 'permanently deleted user',
            'updated'             => 'updated user',
            'reactivated'         => 'reactivated user',
            'restored'            => 'restored user',
            'check_in'            => 'checked in',
            'check_out'           => 'checked out',
            'fetch_leads'         => 'fetch leads',
        ],

        'lead' => [
            'transferred' => 'Lead assigned to {user} from {olduser} on {date}',
            'assigned' => 'Lead assigned to {user} on {date}',
            'opened' => 'Lead opened by {user} on {date}',
            'called' => 'Called to lead by {user} on {date}',
            'note_added' => 'Note added to lead by {user} on {date}',
        ],
    ],
];
