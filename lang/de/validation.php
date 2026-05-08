<?php

declare(strict_types=1);

return [
    'required' => 'Das Feld :attribute ist erforderlich.',
    'email' => 'Das Feld :attribute muss eine gültige E-Mail-Adresse sein.',
    'min' => [
        'string' => 'Das Feld :attribute muss mindestens :min Zeichen lang sein.',
        'numeric' => 'Das Feld :attribute muss mindestens :min sein.',
    ],
    'max' => [
        'string' => 'Das Feld :attribute darf maximal :max Zeichen lang sein.',
        'numeric' => 'Das Feld :attribute darf maximal :max sein.',
    ],
    'unique' => 'Der Wert für :attribute existiert bereits.',
    'confirmed' => 'Die Bestätigung von :attribute stimmt nicht überein.',
    'numeric' => 'Das Feld :attribute muss eine Zahl sein.',
    'regex' => 'Das Format von :attribute ist ungültig.',
    'before' => 'Das Feld :attribute muss ein Datum vor :date sein.',
    'after' => 'Das Feld :attribute muss ein Datum nach :date sein.',
];
