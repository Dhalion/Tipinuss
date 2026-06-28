<?php

declare(strict_types=1);

namespace App\Constants;

final class AppDefaults
{
    public const int START_BALANCE = 1000;

    public const int BETA_KEY_MAX_LENGTH = 32;

    public const int BETA_KEY_MESSAGE_MAX_LENGTH = 500;

    public const int BETA_KEY_RANDOM_LENGTH = 8;

    public const int BET_DESCRIPTION_MAX_LENGTH = 1000;

    public const int MAX_SLUG_ATTEMPTS = 100;

    public const int SLUG_RANDOM_SUFFIX_LENGTH = 6;

    public const int MIN_BETTORS_TO_CLOSE = 2;

    public const float ODDS_MULTIPLIER = 0.5;

    public const int MAX_LOGIN_ATTEMPTS = 5;

    public const int LOGIN_THROTTLE_DECAY = 60;

    public const int DEFAULT_PER_PAGE = 15;

    public const int RECENT_OPEN_LIMIT = 5;

    public const int TOP_USERS_LIMIT = 10;

    public const int RECENT_BETS_LIMIT = 20;

    public const int RECENT_USER_BETS_LIMIT = 10;

    public const int RECENT_TRANSACTIONS_LIMIT = 20;

    public const int CHART_DATA_LIMIT = 100;

    public const int HISTORY_LIMIT = 20;
}
