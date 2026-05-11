<?php

declare(strict_types=1);

return [
    'users' => [
        'title' => 'User Management',
        'registered_count' => ':count users registered',
        'balance_placeholder' => '±0',
        'apply' => 'OK',
        'make_admin' => 'Make admin',
        'revoke_admin' => 'Revoke admin',
        'confirm_delete' => 'Really delete? All bets and data will be deleted.',
        'table' => [
            'user' => 'User',
            'balance' => 'Balance 🌰',
            'admin' => 'Admin',
            'organisation' => 'Organisation',
            'bets' => 'Bets',
            'registered' => 'Registered',
            'actions' => 'Actions',
        ],
    ],
    'organisations' => [
        'title' => 'Manage Organisations',
        'description' => 'Create groups and assign users',
        'create_title' => 'New Organisation',
        'name_label' => 'Name',
        'name_placeholder' => 'e.g. Team Alpha',
        'create_button' => 'Create Organisation',
        'confirm_delete' => 'Really delete?',
        'delete' => 'Delete',
        'member_count' => ':count member|:count members',
        'remove' => 'Remove',
        'empty' => 'No organisations yet.',
        'assign_users' => 'Assign Users',
        'none' => '— None —',
        'no_group' => '— No Group —',
    ],
];
