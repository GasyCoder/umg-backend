<?php

use Illuminate\Support\Facades\Route;

// Public controllers
use App\Http\Controllers\Api\V1\Public\HealthPublicController;
use App\Http\Controllers\Api\V1\Public\PostPublicController;
use App\Http\Controllers\Api\V1\Public\CategoryPublicController;
use App\Http\Controllers\Api\V1\Public\TagPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentCategoryPublicController;
use App\Http\Controllers\Api\V1\Public\DocumentDownloadPublicController;
use App\Http\Controllers\Api\V1\Public\PartnerPublicController;
use App\Http\Controllers\Api\V1\Public\NewsletterPublicController;

// Admin controllers
use App\Http\Controllers\Api\V1\Admin\AuthAdminController;
use App\Http\Controllers\Api\V1\Admin\MediaAdminController;
use App\Http\Controllers\Api\V1\Admin\PostAdminController;
use App\Http\Controllers\Api\V1\Admin\DocumentAdminController;
use App\Http\Controllers\Api\V1\Admin\PartnerAdminController;

Route::prefix('v1')->group(function () {

    // PUBLIC
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

    // ADMIN AUTH
    Route::post('/auth/login', [AuthAdminController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthAdminController::class, 'me']);
        Route::post('/auth/logout', [AuthAdminController::class, 'logout']);

        // MEDIA
        Route::post('/admin/media', [MediaAdminController::class, 'store']);
        Route::delete('/admin/media/{id}', [MediaAdminController::class, 'destroy']);

        // POSTS
        Route::get('/admin/posts', [PostAdminController::class, 'index']);
        Route::post('/admin/posts', [PostAdminController::class, 'store']);
        Route::get('/admin/posts/{id}', [PostAdminController::class, 'show']);
        Route::put('/admin/posts/{id}', [PostAdminController::class, 'update']);
        Route::delete('/admin/posts/{id}', [PostAdminController::class, 'destroy']);

        Route::post('/admin/posts/{id}/submit', [PostAdminController::class, 'submit']);
        Route::post('/admin/posts/{id}/approve', [PostAdminController::class, 'approve']);
        Route::post('/admin/posts/{id}/reject', [PostAdminController::class, 'reject']);
        Route::post('/admin/posts/{id}/archive', [PostAdminController::class, 'archive']);

        // DOCUMENTS
        Route::get('/admin/documents', [DocumentAdminController::class, 'index']);
        Route::post('/admin/documents', [DocumentAdminController::class, 'store']);
        Route::get('/admin/documents/{id}', [DocumentAdminController::class, 'show']);
        Route::put('/admin/documents/{id}', [DocumentAdminController::class, 'update']);
        Route::delete('/admin/documents/{id}', [DocumentAdminController::class, 'destroy']);

        Route::post('/admin/documents/{id}/submit', [DocumentAdminController::class, 'submit']);
        Route::post('/admin/documents/{id}/approve', [DocumentAdminController::class, 'approve']);
        Route::post('/admin/documents/{id}/reject', [DocumentAdminController::class, 'reject']);
        Route::post('/admin/documents/{id}/archive', [DocumentAdminController::class, 'archive']);

        // PARTNERS
        Route::get('/admin/partners', [PartnerAdminController::class, 'index']);
        Route::post('/admin/partners', [PartnerAdminController::class, 'store']);
        Route::get('/admin/partners/{id}', [PartnerAdminController::class, 'show']);
        Route::put('/admin/partners/{id}', [PartnerAdminController::class, 'update']);
        Route::delete('/admin/partners/{id}', [PartnerAdminController::class, 'destroy']);
    });
});
