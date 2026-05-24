<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class NotificationBell extends Component
{
    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification !== null) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function render(): View
    {
        return view('livewire.notification-bell', [
            'unreadCount' => Auth::user()->unreadNotifications->count(),
            'latestNotifications' => Auth::user()->unreadNotifications
                ->take(5)
                ->values(),
        ]);
    }
}
