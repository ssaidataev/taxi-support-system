<?php
//
//use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\SupportRequestController;
//use App\Http\Controllers\MessageController;
//use App\Models\User;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Http\Request;
//
//// Маршрут для логина, который генерирует токен для пользователя
//Route::post('/register', function (Request $request) {
//    $request->validate([
//        'email' => 'required|email|unique:users,email',
//        'password' => 'required|min:6',
//    ]);
//
//    $user = User::create([
//        'name' => 'Новое имя пользователя',
//        'email' => $request->email,
//        'password' => bcrypt($request->password),
//    ]);
//
//    return response()->json([
//        'token' => $user->createToken('YourAppName')->plainTextToken
//    ]);
//});
//
//Route::post('/login', function (Request $request) {
//    $request->validate([
//        'email' => 'required|email',
//        'password' => 'required',
//    ]);
//
//    $user = User::where('email', $request->email)->first();
//
//    // Если пользователь найден и пароль совпадает, создаем токен
//    if ($user && Hash::check($request->password, $user->password)) {
//        return response()->json([
//            'token' => $user->createToken('YourAppName')->plainTextToken
//        ]);
//    }
//
//    // Если авторизация не удалась, возвращаем ошибку
//    return response()->json(['error' => 'Unauthorized'], 401);
//});
//
//// Защищенные маршруты, доступные только для авторизованных пользователей
//Route::middleware('auth:sanctum')->group(function () {
//    // Получить все обращения текущего пользователя
//    Route::get('/support-requests', [SupportRequestController::class, 'index']);
//
//    // Создать новое обращение
//    Route::post('/support-requests', [SupportRequestController::class, 'store']);
//
//    // Получить конкретное обращение
//    Route::get('/support-requests/{id}', [SupportRequestController::class, 'show']);
//
//    // Ответить на обращение
//    Route::post('/support-requests/{supportRequestId}/messages', [SupportRequestController::class, 'addMessage']);
//
//    // Получить все сообщения по обращению
//    Route::get('/support-requests/{supportRequestId}/messages', [MessageController::class, 'index']);
//});
//
//// Защищенные маршруты для администраторов
//Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
//    // Получить все обращения
//    Route::get('/support-requests', [SupportRequestController::class, 'index']);
//
//    // Закрепить заказ за обращением
//    Route::post('/support-requests/{id}/attach-order', [SupportRequestController::class, 'attachOrder']);
//});


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\MessageController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

Route::post('/register', function (Request $request) {
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'name' => 'Новое имя пользователя',
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    return response()->json([
        'token' => $user->createToken('YourAppName')->plainTextToken
    ]);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        return response()->json([
            'token' => $user->createToken('YourAppName')->plainTextToken
        ]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/support-requests', [SupportRequestController::class, 'index']);
    Route::post('/support-requests', [SupportRequestController::class, 'store']);
    Route::get('/support-requests/{id}', [SupportRequestController::class, 'show']);
    Route::post('/support-requests/{supportRequestId}/messages', [SupportRequestController::class, 'addMessage']);
    Route::get('/support-requests/{supportRequestId}/messages', [MessageController::class, 'index']);
});

// Защищенные маршруты для администраторов
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/support-requests', [SupportRequestController::class, 'index']);
    Route::post('/support-requests/{id}/attach-order', [SupportRequestController::class, 'attachOrder']);
});
