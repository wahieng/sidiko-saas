<?php

namespace App\Core\Notification\Resources;

use App\Core\Notification\Models\Notification;

class NotificationResource
{
    public static function make(
        Notification $notification
    ): array {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ];
    }
}