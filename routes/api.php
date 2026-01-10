<?php

use App\Http\Controllers\Api\V1\Admin\AuthAdminController;

// PUBLIC controllers
use App\Http\Controllers\Api\V1\Admin\CategoryAdminController;
use App\Http\Controllers\Api\V1\Admin\DocumentAdminController;
use App\Http\Controllers\Api\V1\Admin\DocumentCategoryAdminController;
use App\Http\Controllers\Api\V1\Admin\MediaAdminController;
use App\Http\Controllers\Api\V1\Admin\NewsletterCampaignAdminController;
use App\Http\Controllers\Api\V1\Admin\NewsletterSubscriberAdminController;
use App\Http\Controllers\Api\V1\Admin\PartnerAdminController;
use App\Http\Controllers\Api\V1\Admin\PostAdminController;
use App\Http\Controllers\Api\V1\Admin\TagAdminController;

// ADMIN controllers
use App\Http\Controllers\Api\V1\Public\CategoryPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentCategoryPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentDownloadPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentPublicController;
use App\Http\Controllers\Api\V1\Public\HealthPublicController;
use App\Http\Controllers\Api\V1\Public\NewsletterPublicController;
use App\Http\Controllers\Api\V1\Public\PartnerPublicController;
use App\Http\Controllers\Api\V1\Public\PostPublicController;
use App\Http\Controllers\Api\V1\Public\TagPublicController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PUBLIC
    |--------------------------------------------------------------------------
    */
    Route::get('/health', HealthPublicController::class);

    Route::get('/posts', [PostPublicController::class, 'index']);
    Route::get('/posts/{slug}', [PostPublicController::class, 'show']);

    Route::get('/categories', [CategoryPublicController::class, 'index']);
    Route::get('/tags', [TagPublicController::class, 'index']);

    Route::get('/documents', [DocumentPublicController::class, 'index']);
    Route::get('/documents/{slug}', [DocumentPublicController::class, 'show']);
    Route::get('/document-categories', [DocumentCategoryPublicController::class, 'index']);
    Route::get('/documents/{id}/download', DocumentDownloadPublicController::class);

    Route::get('/partners', [PartnerPublicController::class, 'index']);

    Route::post('/newsletter/subscribe', [NewsletterPublicController::class, 'subscribe'])
        ->middleware('throttle:newsletter');
    Route::post('/newsletter/unsubscribe', [NewsletterPublicController::class, 'unsubscribe'])
        ->middleware('throttle:newsletter');

    /*
    |--------------------------------------------------------------------------
    | ADMIN AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/auth/login', [AuthAdminController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me', [AuthAdminController::class, 'me']);
        Route::post('/auth/logout', [AuthAdminController::class, 'logout']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN: MEDIA
        |--------------------------------------------------------------------------
        */
        Route::post('/admin/media', [MediaAdminController::class, 'store']);
        Route::delete('/admin/media/{id}', [MediaAdminController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN: POSTS
        |--------------------------------------------------------------------------
        */
        Route::get('/admin/posts', [PostAdminController::class, 'index']);
        Route::post('/admin/posts', [PostAdminController::class, 'store']);
        Route::get('/admin/posts/{id}', [PostAdminController::class, 'show']);
        Route::put('/admin/posts/{id}', [PostAdminController::class, 'update']);
        Route::delete('/admin/posts/{id}', [PostAdminController::class, 'destroy']);

        Route::post('/admin/posts/{id}/submit', [PostAdminController::class, 'submit']);
        Route::post('/admin/posts/{id}/approve', [PostAdminController::class, 'approve']);
        Route::post('/admin/posts/{id}/reject', [PostAdminController::class, 'reject']);
        Route::post('/admin/posts/{id}/archive', [PostAdminController::class, 'archive']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN: DOCUMENTS
        |--------------------------------------------------------------------------
        */
        Route::get('/admin/documents', [DocumentAdminController::class, 'index']);
        Route::post('/admin/documents', [DocumentAdminController::class, 'store']);
        Route::get('/admin/documents/{id}', [DocumentAdminController::class, 'show']);
        Route::put('/admin/documents/{id}', [DocumentAdminController::class, 'update']);
        Route::delete('/admin/documents/{id}', [DocumentAdminController::class, 'destroy']);

        Route::post('/admin/documents/{id}/submit', [DocumentAdminController::class, 'submit']);
        Route::post('/admin/documents/{id}/approve', [DocumentAdminController::class, 'approve']);
        Route::post('/admin/documents/{id}/reject', [DocumentAdminController::class, 'reject']);
        Route::post('/admin/documents/{id}/archive', [DocumentAdminController::class, 'archive']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN: TAXONOMIES (Categories / Tags / Document Categories)
        |--------------------------------------------------------------------------
        */
        Route::get('/admin/categories', [CategoryAdminController::class, 'index']);
        Route::post('/admin/categories', [CategoryAdminController::class, 'store']);
        Route::put('/admin/categories/{id}', [CategoryAdminController::class, 'update']);
        Route::delete('/admin/categories/{id}', [CategoryAdminController::class, 'destroy']);

        Route::get('/admin/tags', [TagAdminController::class, 'index']);
        Route::post('/admin/tags', [TagAdminController::class, 'store']);
        Route::put('/admin/tags/{id}', [TagAdminController::class, 'update']);
        Route::delete('/admin/tags/{id}', [TagAdminController::class, 'destroy']);

        Route::get('/admin/document-categories', [DocumentCategoryAdminController::class, 'index']);
        Route::post('/admin/document-categories', [DocumentCategoryAdminController::class, 'store']);
        Route::put('/admin/document-categories/{id}', [DocumentCategoryAdminController::class, 'update']);
        Route::delete('/admin/document-categories/{id}', [DocumentCategoryAdminController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN: PARTNERS
        |--------------------------------------------------------------------------
        */
        Route::get('/admin/partners', [PartnerAdminController::class, 'index']);
        Route::post('/admin/partners', [PartnerAdminController::class, 'store']);
        Route::get('/admin/partners/{id}', [PartnerAdminController::class, 'show']);
        Route::put('/admin/partners/{id}', [PartnerAdminController::class, 'update']);
        Route::delete('/admin/partners/{id}', [PartnerAdminController::class, 'destroy']);


        /*
        |--------------------------------------------------------------------------
        | ADMIN: NEWSLETTER
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/newsletter/subscribers', [NewsletterSubscriberAdminController::class, 'index']);
        Route::post('/admin/newsletter/subscribers', [NewsletterSubscriberAdminController::class, 'store']);
        Route::put('/admin/newsletter/subscribers/{id}', [NewsletterSubscriberAdminController::class, 'update']);
        Route::delete('/admin/newsletter/subscribers/{id}', [NewsletterSubscriberAdminController::class, 'destroy']);
        
        Route::get('/admin/newsletter/campaigns', [NewsletterCampaignAdminController::class, 'index']);
        Route::get('/admin/newsletter/campaigns/{id}', [NewsletterCampaignAdminController::class, 'show']);
        Route::post('/admin/newsletter/campaigns', [NewsletterCampaignAdminController::class, 'store']);
        Route::post('/admin/newsletter/campaigns/{id}/send', [NewsletterCampaignAdminController::class, 'send']);

    });
});
