<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * An admin vouches for accounts made here, so they skip email verification.
     */
    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->record;

        $user->markEmailAsVerified();
    }
}
