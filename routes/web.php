<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PowerStructureController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserTestController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PublicMapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Публичные маршруты
Route::get('/', function () {
    return view('pages.home', ['navbarTheme' => 'navbar-light']);
})->name('home');

Route::get('/about', function () {
    return view('pages.about', ['navbarTheme' => 'navbar-light']);
})->name('about');

Route::get('/contacts', function () {
    return view('pages.contacts', ['navbarTheme' => 'navbar-light']);
})->name('contacts');

Route::get('/maps', [PublicMapController::class, 'index'])->name('maps');

// Маршруты аутентификации
Route::middleware('guest')->group(function () {
    // Вход
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Регистрация
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Восстановление пароля
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Редирект с /home или /dashboard на /admin/stats для авторизованных пользователей
Route::redirect('/home', '/admin/stats');
Route::redirect('/dashboard', '/admin/stats');

// Маршруты админки
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/map', [AdminController::class, 'map'])->name('map');
    });
    Route::resource('admin/tests', TestController::class, ['as' => 'admin']);
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
});

// Маршрут для выхода
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout')->middleware('auth');

// Маршруты для структур власти
Route::prefix('power')->name('power.')->group(function () {
    Route::get('/people', [PowerStructureController::class, 'people'])->name('people');
    Route::get('/president', [PowerStructureController::class, 'president'])->name('president');
    Route::get('/assembly', [PowerStructureController::class, 'assembly'])->name('assembly');
    Route::get('/executive', [PowerStructureController::class, 'executive'])->name('executive');
    Route::get('/legislative', [PowerStructureController::class, 'legislative'])->name('legislative');
    Route::get('/judicial', [PowerStructureController::class, 'judicial'])->name('judicial');
    Route::get('/representatives', [PowerStructureController::class, 'representatives'])->name('representatives');
    Route::get('/council', [PowerStructureController::class, 'council'])->name('council');
    Route::get('/constitutional', [PowerStructureController::class, 'constitutional'])->name('constitutional');
    Route::get('/supreme', [PowerStructureController::class, 'supreme'])->name('supreme');
});

Route::get('/test-email', function () {
    try {
        Mail::raw('Тест отправки почты ' . now(), function($message) {
            $message->to('Lermalplay@gmail.com')
                   ->subject('Тест отправки ' . now());
        });

        return 'Письмо отправлено (проверьте логи)';
    } catch (\Exception $e) {
        return 'Ошибка отправки: ' . $e->getMessage();
    }
})->name('test.email');

// Маршруты пользователя
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [UserController::class, 'settings'])->name('settings');
    Route::post('/settings/update', [UserController::class, 'updateSettings'])->name('settings.update');
    Route::get('/tests', [UserTestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test}', [UserTestController::class, 'show'])->name('tests.show');
    Route::post('/tests/{test}/submit', [UserTestController::class, 'submit'])->name('tests.submit');
    Route::post('/tests/{test}/rate', [UserTestController::class, 'rate'])->name('tests.rate');
});

// Маршруты аутентификации
Auth::routes();

// Маршруты для учителей и администраторов
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/map', [TeacherController::class, 'map'])->name('map');
});

// API маршруты для работы с картой
Route::middleware(['auth', 'role:teacher'])->prefix('api')->group(function () {
    Route::get('/markers', [MapController::class, 'getMarkers']);
    Route::post('/markers', [MapController::class, 'storeMarker']);
    Route::put('/markers/{marker}', [MapController::class, 'updateMarker']);
    Route::delete('/markers/{marker}', [MapController::class, 'deleteMarker']);

    Route::get('/routes/{route}', [RouteController::class, 'show']);
    Route::post('/routes', [RouteController::class, 'store']);
    Route::delete('/routes/{route}', [RouteController::class, 'destroy']);
});

// Маршруты для верификации email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    Auth::logout(); // Выходим из системы, чтобы пользователь мог заново войти

    return view('auth.verified', [
        'navbarTheme' => 'navbar-light'
    ]);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Ссылка для подтверждения отправлена!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth'])->group(function () {
    Route::post('/routes/{route}/vote', [PublicMapController::class, 'vote'])->name('routes.vote');
    Route::post('/routes/{route}/start-voting', [PublicMapController::class, 'startVoting'])
        ->middleware('can:start_route_voting')
        ->name('routes.start-voting');
});
