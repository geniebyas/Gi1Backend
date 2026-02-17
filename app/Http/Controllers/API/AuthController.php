<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Symfony\Component\Process\Process;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        //validation
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
            return response()->json($response, 400);
        }

        //create user
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('Gi1InfoApp')->accessToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User Registered Successfully'
        ];
        return $response()->json($response, 200);
    }

    public function login(Request $req)
    {
        if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            $user = Auth::user();

            // $success['token'] = $user->createToken('Gi1InfoApp')->accessToken;
            $success['name'] = $user->name;

            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'User Login Successfully'
            ];
            return $response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => "Unauthorised"
            ];
            return $response()->json($response);
        }
    }

    public function gitDeploy(Request $request)
    {
        // ✅ 1. Verify GitHub signature
        $signature = $request->header('X-Hub-Signature-256');
        $secret = env('GITHUB_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($hash, $signature)) {
            Log::warning('Git Deploy: Invalid signature attempt.');
            abort(403, $hash);
        }

        // ✅ 2. Determine environment
        $environment = app()->environment(); // e.g., 'local', 'production', 'staging'
        $isDev = in_array($environment, ['local', 'development', 'staging']);

        // ✅ 3. Build commands
        $basePath = escapeshellarg(base_path());

        $composerCommand = $isDev
            ? 'composer install -o' // include dev dependencies
            : 'composer install --no-dev -o'; // production mode

        $commands = [
            "cd $basePath",
            // "git fetch --all",
            // "git reset --hard origin/main",
            "git pull origin main",
            $composerCommand,
            "php artisan config:clear",
            "php artisan cache:clear",
            "php artisan optimize:clear",
            // "php artisan migrate --force",  // optional if you use migrations
            "php artisan optimize",
        ];

        // ✅ 4. Execute deployment
        $process = new Process(['bash', '-c', implode(' && ', $commands)]);
        $process->setTimeout(300); // 5 minutes max
        $process->run();

        $output = $process->getOutput() . $process->getErrorOutput();
        Log::info("Git Deploy ({$environment}) Output:\n" . $output);

        // ✅ 5. Return JSON response
        return response()->json([
            'message' => "Deployment completed successfully for [$environment] environment.",
            'output'  => $output,
        ]);
    }

    public function adminLogin(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
            return response()->json($response);
        }
        $admin = User::where('email', $request->email)->first();
        if (!$admin || !password_verify($request->password, $admin->password)) {
            $response = [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
            return response()->json($response);
        }
        $adminUid = env('ADMIN_UID') ?? 'user_693a97bbce0eb';
        if ($admin->uid === $adminUid) {
            $response = [
                'success' => true,
                'message' => 'Admin Login Successful',
                'user' => [
                    'name' => 'Administrator',
                    'email' => 'info@gi1superverse.com',
                    'uid' => $adminUid,
                    'is_admin' => true
                ]
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Unauthorized'
            ];
            return response()->json($response, 401);
        }
    }
}
