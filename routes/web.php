<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Events\CallEnded;
use App\Events\IncomingCall;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\SettingController;
use App\Livewire\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return to_route('login');
});

// php artisan config:clear
// php artisan view:clear
// php artisan route:clear
// php artisan optimize:clear



Auth::routes();


Route::middleware(['auth:web'])->group(function () {
    // Language switch
    Route::get('index/{locale}', [HomeController::class, 'lang'])->name('language.switch');
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');


    Route::prefix("package")->as("package.")->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/purchase/{id}', [PurchaseController::class, 'purchase'])->name('purchase');

        Route::post('/payment/process/{id}', [PurchaseController::class, 'processPayment'])->name('payment.process');

        Route::get('/payment/success', [PackageController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/payment/cancel', [PackageController::class, 'paymentCancel'])->name('payment.cancel');
    });

    Route::prefix('friends')->as('friends.')->group(function () {
        Route::get('/coversation', [FriendController::class, 'index'])->name('index');
        // Start a new call (generates a new channel)
        Route::get('/call/{id}', [FriendController::class, 'call'])->name('call.start');
        // Join an existing call
        Route::get('/calling/{id}/{channelname}', [FriendController::class, 'videoCall'])
            ->name('call.join');
        // coin dedunction
        Route::get('/call/deduct-coins/{caller}', [FriendController::class, 'deductCoins']);
        // call start notification to remote
        Route::get('/call/start/notification/{callerid}/{remoteid}/{channel}', function ($callerid, $remoteid, $channel) {
            $caller = \App\Models\User::findOrFail($callerid);
            $remote = \App\Models\User::findOrFail($remoteid);

            $joinUrl =
                route('friends.call.join', ['id' => base64_encode($remoteid), 'channelname' => $channel]);
            // 🔔 Fire event to notify the remote user

            broadcast(new App\Events\IncomingCall(
                $caller,
                $remote,
                $channel,
                $joinUrl,
            ))->toOthers();
            return response()->json(['status' => 'ok']);
        })->name('.call.start.notification');
        // call cancel notification to calling your
        Route::get('/call/end/notification/{callerid}/{declinerid}', function ($callerid, $declinerid) {
            $tonotify = \App\Models\User::findOrFail($callerid);
            $decliner = \App\Models\User::findOrFail($declinerid);

            broadcast(new App\Events\CallEnded($tonotify, $decliner))->toOthers();

            return response()->json(['status' => 'ok']);
        })->name('.call.end.notification');

        // chartify chatting
        Route::get("/chatting/{id}", [FriendController::class, "chatting"])->name('chatting');

        Route::get("/chat/{id}", Chat::class)->name('chat');
    });

    Route::prefix('profile')->as('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('update', [ProfileController::class, 'update'])->name('update');
    });

    // Admin
    Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard/Home
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Users routes
        Route::prefix('user')->as('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::post('/block', [UserController::class, 'block'])->name('block');
            Route::post('/unblock', [UserController::class, 'unblock'])->name('unblock');
            Route::get('/active/status/{id}', [UserController::class, 'activeStatus'])->name('active.status');
        });

        // Messages routes
        Route::prefix('messages')->as('messages.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('/detail/{id}', [MessageController::class, 'detail'])->name('detail');
        });

        //User Images routes
        Route::prefix('images')->as('images.')->group(function () {
            Route::get('/', [ImageController::class, 'index'])->name('index');
            Route::post('/remove', [ImageController::class, 'remove'])->name('remove');
        });

        //Report User  routes
        Route::prefix('report')->as('report.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::delete('/delete/{id}', [ReportController::class, 'delete'])->name('delete');
            Route::post('/block', [ReportController::class, 'block'])->name('block');
        });

        // Package routes
        Route::prefix('package')->as('package.')->group(function () {
            Route::get('/', [PackageController::class, 'index'])->name('index');
            Route::get('/add', [PackageController::class, 'add'])->name('add');
            Route::post('/insert', [PackageController::class, 'insert'])->name('insert');
            Route::get('edit/{id}', [PackageController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [PackageController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [PackageController::class, 'delete'])->name('delete');
        });

        Route::prefix('gift')->as('gift.')->group(function () {
            Route::get('/', [GiftController::class, 'index'])->name('index');
            Route::get('/add', [GiftController::class, 'add'])->name('add');
            Route::post('/insert', [GiftController::class, 'insert'])->name('insert');
            Route::get('edit/{id}', [GiftController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [GiftController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [GiftController::class, 'delete'])->name('delete');
        });

        Route::prefix('setting')->as('setting.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/save', [SettingController::class, 'save'])->name('save');
        });
    });
});

// Broadcast::routes(['middleware' => ['auth:web']]);
