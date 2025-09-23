<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FieldsInvestorsController;
use App\Http\Controllers\InvestorsController;
use App\Http\Controllers\PitchController;
use App\Http\Controllers\PitchTextController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StagesInvestorsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// Authentication routes
Route::prefix('auth')->group(function () {
    // تسجيل يوزر جديد (الاسم والإيميل)
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // تسجيل الدخول (بعد تأكيد الإيميل وتعيين كلمة المرور)
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // تسجيل الخروج
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:sanctum');

    // Google authentication routes
    Route::prefix('google')->group(function () {
        Route::get('/redirect', [GoogleAuthController::class, 'redirect']);
        Route::get('/callback', [GoogleAuthController::class, 'callback']);
    });
});

// التحقق من كود الإيميل
Route::post('/verify-code', function (Request $request) {
    $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'code' => ['required', 'string', 'min:4', 'max:4'],
    ]);

    $user = User::find($request->user_id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email is already verified'], 400);
    }

    // التحقق من الكود
    if ($user->verification_code !== $request->code) {
        return response()->json(['message' => 'Verification code is incorrect'], 400);
    }

    // التحقق من صلاحية الكود (60 دقيقة)
    if ($user->verification_code_sent_at->diffInMinutes(now()) > 60) {
        return response()->json(['message' => 'Verification code has expired'], 400);
    }

    try {
        // تفعيل الإيميل
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        // Log للتأكد
        Log::info('Email verified', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Email verified successfully. Please set your password.',
            'user_id' => $user->id,
            'needs_password' => true
        ]);
    } catch (\Exception $e) {
        Log::error('Email verification failed', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Email verification failed: ' . $e->getMessage()
        ], 500);
    }
});

// تعيين كلمة المرور
Route::post('/set-password', function (Request $request) {
    $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'password' => ['required', 'confirmed', Rules\Password::min(8)],
    ]);

    $user = User::find($request->user_id);

    if (!$user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Please verify your email first'], 400);
    }

    if ($user->hasPassword()) {
        return response()->json(['message' => 'Password is already set'], 400);
    }

    try {
        // تعيين كلمة المرور
        $user->password = Hash::make($request->password);
        $user->save();

        // إنشاء توكن
        $token = $user->createToken('api-token')->plainTextToken;

        // Log للتأكد
        Log::info('Password set successfully', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Password set successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Password set failed', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Failed to set password: ' . $e->getMessage()
        ], 500);
    }
});

// إعادة إرسال كود التحقق
Route::post('/resend-verification', function (Request $request) {
    $request->validate(['user_id' => ['required', 'exists:users,id']]);

    $user = User::find($request->user_id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email is already verified'], 400);
    }

    try {
        // إرسال كود جديد
        $user->sendVerificationCode();

        // Log للتأكد
        Log::info('Verification code resent', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json(['message' => 'Verification code has been sent']);
    } catch (\Exception $e) {
        Log::error('Resend verification failed', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Failed to resend verification code: ' . $e->getMessage()
        ], 500);
    }
});


Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::post('/resend-password-reset-code', function (Request $request) {
    $request->validate(['email' => 'required|email|exists:users,email']);

    $user = User::where('email', $request->email)->first();

    // إرسال كود جديد
    $user->sendPasswordResetCode();

    return response()->json(['message' => 'Password reset code has been sent to your email.']);
});

// Public routes (لا تحتاج مصادقة)
Route::get('/public/fields', [FieldController::class, 'index']);
Route::get('/public/stages', [StageController::class, 'index']);


// Route لمعالجة استجابة جوجل بشكل مباشر (بدون إعادة توجيه)
Route::get('/auth/google/direct-callback', function (Request $request) {
    $token = $request->input('token');
    $userId = $request->input('user_id');

    if (!$token || !$userId) {
        return response()->json([
            'success' => false,
            'error' => 'missing_parameters',
            'message' => 'Token and user ID are required'
        ], 400);
    }

    // هنا يمكنك إضافة أي تحقق إضافي تحتاجه
    return response()->json([
        'success' => true,
        'data' => [
            'token' => $token,
            'user_id' => $userId,
            'user_name' => $request->input('user_name'),
            'user_email' => $request->input('user_email'),
            'user_role' => $request->input('user_role')
        ],
        'message' => 'Authentication successful'
    ]);
});

// Protected routes (تحتاج مصادقة)
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });



    // Pitches routes - استخدام resource للطرق الأساسية
    Route::prefix('pitches')->group(function () {
        Route::get('/', [PitchController::class, 'index']);
        Route::get('/status/{status}', [PitchController::class, 'getByStatus']);
        Route::get('/stage/{stageId}', [StageController::class, 'get_pitches_by_stages']);

        // Admin only pitches routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [PitchController::class, 'store']);
            Route::post('/{pitch}/mark-scored', [PitchController::class, 'markAsScored']);
        });

        // Founder only pitches routes
        Route::middleware('role:founder')->group(function () {
            Route::get('/my-pitches', [PitchController::class, 'myPitches']);
        });

        // Routes للجميع
        Route::get('/{pitch}', [PitchController::class, 'show']);

        // Routes تحتاج صلاحيات (admin أو مالك المحتوى)
        Route::middleware('check.pitch.ownership')->group(function () {
            Route::put('/{pitch}', [PitchController::class, 'update']);
            Route::delete('/{pitch}', [PitchController::class, 'destroy']);
        });
    });

    // PitchText routes - استخدام resource للطرق الأساسية
    Route::prefix('pitch-texts')->group(function () {
        Route::get('/', [PitchTextController::class, 'index']);
        Route::get('/status/{status}', [PitchTextController::class, 'getByStatus']);
        Route::get('/stage/{stageId}', [StageController::class, 'get_pitchText_by_stages']);

        // Admin only pitch-text routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [PitchTextController::class, 'store']);
            Route::post('/{pitchText}/mark-scored', [PitchTextController::class, 'markAsScored']);
        });

        // Founder only pitch-text routes
        Route::middleware('role:founder')->group(function () {
            Route::get('/my-pitch-texts', [PitchTextController::class, 'myPitches']);
        });

        // Routes للجميع
        Route::get('/{pitchText}', [PitchTextController::class, 'show']);

        // Routes تحتاج صلاحيات (admin أو مالك المحتوى)
        Route::middleware('check.pitchtext.ownership')->group(function () {
            Route::put('/{pitchText}', [PitchTextController::class, 'update']);
            Route::delete('/{pitchText}', [PitchTextController::class, 'destroy']);
        });
    });

    // Scores routes - استخدام resource
    Route::apiResource('scores', ScoreController::class)->only(['index', 'show']);
    Route::get('/scores/pitch/{pitch}', [ScoreController::class, 'getScoresByPitch']);

    // Admin only scores routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/scores', [ScoreController::class, 'store']);
        Route::put('/scores/{score}', [ScoreController::class, 'update']);
        Route::delete('/scores/{score}', [ScoreController::class, 'destroy']);
    });

    // Teams routes - استخدام resource
    Route::apiResource('teams', TeamController::class)->only(['index', 'show']);

    // Founder only teams routes
    Route::middleware('role:founder')->group(function () {
        Route::post('/teams', [TeamController::class, 'store']);
        Route::put('/teams/{team}', [TeamController::class, 'update']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
    });

    // Fields routes - استخدام resource
    Route::apiResource('fields', FieldController::class)->only(['index', 'show']);

    // Admin only fields routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/fields', [FieldController::class, 'store']);
        Route::put('/fields/{field}', [FieldController::class, 'update']);
        Route::delete('/fields/{field}', [FieldController::class, 'destroy']);
    });

    // Stages routes - استخدام resource
    Route::apiResource('stages', StageController::class)->only(['index', 'show']);

    // Admin only stages routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/stages', [StageController::class, 'store']);
        Route::put('/stages/{stage}', [StageController::class, 'update']);
        Route::delete('/stages/{stage}', [StageController::class, 'destroy']);
    });

    // Investors routes - استخدام resource
    Route::apiResource('investors', InvestorsController::class)->only(['index', 'show']);

    // Investor only routes
    Route::middleware('role:investor')->group(function () {
        Route::post('/investors', [InvestorsController::class, 'store']);
        Route::put('/investors/{investor}', [InvestorsController::class, 'update']);
        Route::delete('/investors/{investor}', [InvestorsController::class, 'destroy']);
        Route::get('/investors/{investorId}/fields', [FieldsInvestorsController::class, 'get_fields_investor']);
        Route::get('/investors/{investorId}/stages', [StagesInvestorsController::class, 'get_stages_investor']);
        Route::get('/investors/opportunities/pitches', [PitchController::class, 'index']);
    });

    // FieldsInvestors routes - استخدام resource
    Route::apiResource('fields-investors', FieldsInvestorsController::class)->only(['index', 'show']);

    // Investor & Admin only routes
    Route::middleware('role:investor,admin')->group(function () {
        Route::post('/fields-investors', [FieldsInvestorsController::class, 'store']);
        Route::put('/fields-investors/{fieldsInvestors}', [FieldsInvestorsController::class, 'update']);
        Route::delete('/fields-investors/{fieldsInvestors}', [FieldsInvestorsController::class, 'destroy']);
    });

    // StagesInvestors routes - استخدام resource
    Route::apiResource('stages-investors', StagesInvestorsController::class)->only(['index', 'show']);

    // Investor & Admin only routes
    Route::middleware('role:investor,admin')->group(function () {
        Route::post('/stages-investors', [StagesInvestorsController::class, 'store']);
        Route::put('/stages-investors/{stagesInvestors}', [StagesInvestorsController::class, 'update']);
        Route::delete('/stages-investors/{stagesInvestors}', [StagesInvestorsController::class, 'destroy']);
    });

    // Feedback routes - استخدام resource
    Route::apiResource('feedbacks', FeedbackController::class)->only(['index', 'show']);

    // Admin only feedback routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/feedbacks', [FeedbackController::class, 'store']);
        Route::put('/feedbacks/{feedback}', [FeedbackController::class, 'update']);
        Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy']);
    });

    // Recommendation routes - استخدام resource
    Route::apiResource('recommendations', RecommendationController::class)->only(['index', 'show']);

    // Admin only recommendation routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/recommendations', [RecommendationController::class, 'store']);
        Route::put('/recommendations/{recommendation}', [RecommendationController::class, 'update']);
        Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy']);
    });
});

// AI routes (محمية بـ middleware مخصص)
Route::prefix('ai')->group(function () {
    Route::post('/scores/receive', [ScoreController::class, 'aiScore'])
        ->middleware('ai.token');
});

