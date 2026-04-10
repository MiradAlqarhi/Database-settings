use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->get('/user', function () {
    return auth()->user();
});