<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mainpage;
use App\Http\Controllers\UserManagement;
use App\Http\Controllers\Tasks;
use App\Http\Controllers\AccountSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// Routes pour le controller "Mainpage"

Route::get('/dashboard', [Mainpage::class, 'dashboard'])->middleware('auth');

Route::get('/workspace/{id}', [Mainpage::class, 'accessWorkspace']);

Route::post('/createworkspace', [Mainpage::class, 'createWorkspace']);

Route::post('/deleteworkspace/{id}', [Mainpage::class, 'deleteWorkspace']);

Route::get('/editworkspace/{id}', [Mainpage::class, 'editWorkspace']);

Route::post('/posteditworkspace/{id}', [Mainpage::class, 'postEditWorkspace']);



// Routes pour le controller "UserManagement"

Route::get('/managemembers/{id}', [UserManagement::class, 'manageMembers']);

Route::post('/managemembers/{id}', [UserManagement::class, 'postManageMembers']);

Route::post('/addusertoworkspace/{id}', [UserManagement::class, 'addUserToWorkspace']);

Route::post('/removeuserfromworkspace/{id}/{listeduserid}', [UserManagement::class, 'removeUserFromWorkspace']);

Route::post('/transferownership/{id}/{listeduserid}', [UserManagement::class, 'transferOwnership']);

Route::post('/changerole/{id}/{listeduserid}', [UserManagement::class, 'changeRole']);



// Routes pour le controller "Tasks"

Route::post('/createtask/{workspaceid}', [Tasks::class, 'createTask']);

Route::post('/deletetask/{id}', [Tasks::class, 'deleteTask']);

Route::get('/edittask/{id}', [Tasks::class, 'editTask']);

Route::post('/edittask/{id}', [Tasks::class, 'postEditTask']);

Route::post('/changetaskstatus/{id}', [Tasks::class, 'changeTaskStatus']);



// Routes pour le controller "Settings"

Route::get('/settings', [AccountSettings::class, 'accountSettings']);

// Routes géré(s) par Laravel Fortify

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
 
            $user->save();
 
            //event(new PasswordReset($user));
        }
    );
 
    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// ->middleware(['password.confirm']);