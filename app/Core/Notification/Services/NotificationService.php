<?php

namespace App\Core\Notification\Services;

use App\Core\Notification\Models\Notification;
use App\Core\Identity\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function send(
        User $user,
        string $title,
        string $message,
        string $type,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function unread(User $user): Collection
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->get();
    }

    public function markAsRead(
        Notification $notification
    ): Notification {
        $notification->update([
            'read_at' => now(),
        ]);

        return $notification->refresh();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }
}