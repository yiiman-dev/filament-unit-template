<?php
use Filament\Facades\Filament;
use Filament\Http\Controllers\Auth\EmailVerificationController;
use Filament\Http\Controllers\Auth\LogoutController;
use Filament\Http\Controllers\RedirectToHomeController;
use Filament\Http\Controllers\RedirectToTenantController;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Units\Sessions\My\Middlewares\MySessionMiddleware;
