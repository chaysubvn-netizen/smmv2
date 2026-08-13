<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Sanctum writes last_used_at on every authenticated request. Some managed
     * MySQL hosts expose this table read-only, which must not make authentication
     * fail for an otherwise valid token.
     */
    public function save(array $options = [])
    {
        if ($this->exists && array_keys($this->getDirty()) === ['last_used_at']) {
            $this->syncOriginalAttribute('last_used_at');

            return true;
        }

        return parent::save($options);
    }
}
