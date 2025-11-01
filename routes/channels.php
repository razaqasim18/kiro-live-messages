<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


Broadcast::channel('calluser.{id}', function ($user, $id) {
    Log::info("Auth attempt for user channel: user_id={$user->id}, param_id={$id}");

    if ($user instanceof \App\Models\User && (int)$user->id === (int)$id) {
        Log::info("✅ Authorized user {$user->id} for calluser.{$id}");
        return true;
    }

    Log::warning("❌ Unauthorized user {$user->id} for calluser.{$id}");
    return false;
});

Broadcast::channel('enduser.{id}', function ($user, $id) {
    Log::info("Auth attempt for user channel: user={$user->id}, param={$id}");
    return $user instanceof \App\Models\User && $user->id == $id;
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    Log::info("Chat for user channel: user={$user->id}, param={$id}");

    return (int) $user->id === (int) $id;
});


Broadcast::channel('callgift.{id}', function ($user, $id) {
    Log::info("call gift for user channel: user={$user->id}, param={$id}");

    return (int) $user->id === (int) $id;
});
