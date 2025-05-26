<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function () {
    // Register a new user
    Route::post('/register', [UserController::class, 'register'])
        ->name('register');

    // Login a user
    Route::post('/login', [UserController::class, 'login'])
        ->name('login');


    Route::middleware('auth:sanctum')->group(function (){
        // Logout user
        Route::post('/logout', [UserController::class, 'logout'])
            ->name('logout');

        // Get user's info
        Route::get('/me', [UserController::class, 'show'])
            ->name('me');

        // Get the list of all users
        Route::get('/', [UserController::class, 'index'])
            ->name('index');

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('index');
    });

});

Route::prefix('posts')->name('posts.')->group(function () {

    // Search in posts
    Route::get('/search', SearchController::class)
        ->name('search');

    // Authenticated user routes
    Route::middleware('auth:sanctum')->group(function () {
        // Schedule a post publication
        Route::post('/{post}/schedule', ScheduleController::class)
            ->middleware(['can:schedule,post', 'successRateLimit'])
            ->name('schedule');

        // Get a list of  all posts
        Route::get('/', [PostController::class, 'index'])
            ->name('index');

        // Download an export of all the posts
        Route::get('/export', ExportController::class)
            ->middleware('admin')
            ->name('export');


        // Create a post
        Route::post('/', [PostController::class, 'store'])
            ->name('store');

        // Update a post
        Route::put('/{post}', [PostController::class, 'update'])
            ->middleware('can:update,post')
            ->name('update');

        // Delete a post
        Route::delete('/{post}', [PostController::class, 'destroy'])
            ->middleware('can:delete,post')
            ->name('destroy');

        Route::prefix('{post}/comments')->name('comments.')->group(function () {

            // Leave a comment on a post
            Route::post('/', [CommentController::class, 'store'])
                ->name('store');

            // Get a comment with its replies
            Route::get('/{comment}', [CommentController::class, 'show'])
                ->name('show');

            // Update a specific comment
            Route::put('/{comment}', [CommentController::class, 'update'])
                ->middleware('can:update,comment')
                ->name('update');

            // Delete a specific comment
            Route::delete('/{comment}', [CommentController::class, 'destroy'])
                ->middleware('can:delete,comment')
                ->name('destroy');

            // Reply to a comment on a post
            Route::post('/{comment}/replies', [CommentController::class, 'storeReply'])
                ->name('store');
        });
    });

    // Get a list of  all posts
    Route::get('/', [PostController::class, 'index'])
        ->name('index');

    // Get a specific post
    Route::get('/{post}', [PostController::class, 'show'])
        ->middleware(['can:view,post'])
        ->name('show');
});

Route::prefix('likes')->name('likes.')
    ->middleware('auth:sanctum')->group(function () {
    // Toggle like
    Route::post('/{type}/{id}/toggle', [LikeController::class, 'toggleLike'])
        ->name('toggle');

    // Get the list of users who liked a post
    Route::get('/{type}/{id}/users', [LikeController::class, 'usersList'])
        ->name('users');
});


Route::prefix('reports')->name('reports.')
    ->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Get a list of all reports
    Route::get('/', [ReportController::class, 'index'])
        ->name('index');

    // Download a report
    Route::get('/{filename}', [ReportController::class, 'download'])
        ->name('download');
});

Route::get('/announcements/blog-index-header', AnnouncementController::class);
Route::get('tags', TagController::class);
