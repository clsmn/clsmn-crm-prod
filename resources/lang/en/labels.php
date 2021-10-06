<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Labels Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in labels throughout the system.
    | Regardless where it is placed, a label can be listed here so it is easily
    | found in a intuitive way.
    |
    */

    'general' => [
        'all'     => 'All',
        'yes'     => 'Yes',
        'no'      => 'No',
        'custom'  => 'Custom',
        'actions' => 'Actions',
        'active'  => 'Active',
        'buttons' => [
            'save'   => 'Save',
            'update' => 'Update',
        ],
        'hide'              => 'Hide',
        'inactive'          => 'Inactive',
        'none'              => 'None',
        'show'              => 'Show',
        'toggle_navigation' => 'Toggle Navigation',
    ],

    'backend' => [
        'access' => [
            'roles' => [
                'create'     => 'Create Role',
                'edit'       => 'Edit Role',
                'management' => 'Role Management',

                'table' => [
                    'number_of_users' => 'Number of Users',
                    'permissions'     => 'Permissions',
                    'role'            => 'Role',
                    'sort'            => 'Sort',
                    'total'           => 'role total|roles total',
                ],
            ],

            'users' => [
                'active'              => 'Active Users',
                'all_permissions'     => 'All Permissions',
                'change_password'     => 'Change Password',
                'change_password_for' => 'Change Password for :user',
                'create'              => 'Create User',
                'deactivated'         => 'Deactivated Users',
                'deleted'             => 'Deleted Users',
                'edit'                => 'Edit User',
                'management'          => 'User Management',
                'no_permissions'      => 'No Permissions',
                'no_roles'            => 'No Roles to set.',
                'permissions'         => 'Permissions',

                'table' => [
                    'confirmed'      => 'Confirmed',
                    'created'        => 'Created',
                    'email'          => 'E-mail',
                    'id'             => 'ID',
                    'last_updated'   => 'Last Updated',
                    'name'           => 'Name',
                    'no_deactivated' => 'No Deactivated Users',
                    'no_deleted'     => 'No Deleted Users',
                    'roles'          => 'Roles',
                    'total'          => 'user total|users total',
                ],

                'tabs' => [
                    'titles' => [
                        'overview' => 'Overview',
                        'history'  => 'History',
                    ],

                    'content' => [
                        'overview' => [
                            'avatar'       => 'Avatar',
                            'confirmed'    => 'Confirmed',
                            'created_at'   => 'Created At',
                            'deleted_at'   => 'Deleted At',
                            'email'        => 'E-mail',
                            'last_updated' => 'Last Updated',
                            'name'         => 'Name',
                            'status'       => 'Status',
                        ],
                    ],
                ],

                'view' => 'View User',
            ],
        ],

        'leads' => [
            'management'          => 'Lead Management',
            'call_history'        => 'Call History',
            'assigned'              => 'Assigned Leads',

            'table' => [
                'id'                    => 'Lead Id',
                'phone'                 => 'Phone',
                'name'                  => 'Name',
                'city'                  => 'City',
                'assigned_to'           => 'Assigned To',
                'call_duration'         => 'Call Duration',
                'called_by'             => 'Last Call By',
                'last_call'             => 'Last Call',
                'called_at'             => 'Last Call At',
                'follow_up'             => 'Follow Up',
                'data_medium'           => 'Source',
                'subscription_type'     => 'Subscription Type',
                'phase'                 => 'Lead Phase',
                'status'                => 'Status',
                'alternate_number'      => 'Alternate Number',
                'last_call'             => 'Last Called At',
                'action'                => 'Action',
            ],
        ],

        'data_bank' => [
            'users'          => 'Data Bank Users',

            'table' => [
                'id'                    => 'Id',
                'phone'                 => 'Phone',
                'name'                  => 'Name',
                'city'                  => 'City',
                'messenger'             => 'Messenger',
                'community'             => 'Community',
                'learning'              => 'Learning',
                'data_medium'           => 'Source',
                'subscription_type'     => 'Subscription Type',
                'phase'                 => 'Lead Phase',
                'moved_to_lead'         => 'Moved to lead',
                'assigned_to'           => 'Assigned To',
                'updated_at'            => 'Updated At',
                'action'                => 'Action',
            ],
        ],

        'workforce' => [
            'management'          => 'Workforce Management',

            'table' => [
                'executive'         => 'Executive',
                'check_in'          => 'Check In',
                'check_out'         => 'Check Out',
                'calls'             => 'Calls',
                'call_time'         => 'Call TIme',
                'conversion'        => 'Conversion',
                'sale'              => 'Sale',
                'hot'               => 'Hot',
                'mild'              => 'Mild',
                'cold'              => 'Cold',
                'no_answer'         => 'No Answer',
                'busy'              => 'Busy',
                'not_interested'    => 'Not Interested',
                'dead'              => 'Dead',
                'note'              => 'Note',
            ],
        ],

    ],

    'frontend' => [

        'auth' => [
            'login_box_title'    => 'Login',
            'login_button'       => 'Login',
            'login_with'         => 'Login with :social_media',
            'register_box_title' => 'Register',
            'register_button'    => 'Register',
            'remember_me'        => 'Remember Me',
        ],

        'passwords' => [
            'forgot_password'                 => 'Forgot Your Password?',
            'reset_password_box_title'        => 'Reset Password',
            'reset_password_button'           => 'Reset Password',
            'send_password_reset_link_button' => 'Send Password Reset Link',
        ],

        'macros' => [
            'country' => [
                'alpha'   => 'Country Alpha Codes',
                'alpha2'  => 'Country Alpha 2 Codes',
                'alpha3'  => 'Country Alpha 3 Codes',
                'numeric' => 'Country Numeric Codes',
            ],

            'macro_examples' => 'Macro Examples',

            'state' => [
                'mexico' => 'Mexico State List',
                'us'     => [
                    'us'       => 'US States',
                    'outlying' => 'US Outlying Territories',
                    'armed'    => 'US Armed Forces',
                ],
            ],

            'territories' => [
                'canada' => 'Canada Province & Territories List',
            ],

            'timezone' => 'Timezone',
        ],

        'user' => [
            'passwords' => [
                'change' => 'Change Password',
            ],

            'profile' => [
                'avatar'             => 'Avatar',
                'created_at'         => 'Created At',
                'edit_information'   => 'Edit Information',
                'email'              => 'E-mail',
                'last_updated'       => 'Last Updated',
                'name'               => 'Name',
                'update_information' => 'Update Information',
            ],
        ],

    ],
];
