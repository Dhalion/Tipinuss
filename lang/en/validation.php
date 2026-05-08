<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
        'numeric' => 'The :attribute field must be at least :min.',
    ],
    'max' => [
        'string' => 'The :attribute field may not be greater than :max characters.',
        'numeric' => 'The :attribute field may not be greater than :max.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'numeric' => 'The :attribute field must be a number.',
    'regex' => 'The :attribute field format is invalid.',
    'before' => 'The :attribute field must be a date before :date.',
    'after' => 'The :attribute field must be a date after :date.',
];
