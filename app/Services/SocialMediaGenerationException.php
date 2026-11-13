<?php

namespace App\Services;

use RuntimeException;

/**
 * Błąd generowania posta. Komunikat jest czytelny dla użytkownika i trafia do flash message.
 */
class SocialMediaGenerationException extends RuntimeException
{
}
