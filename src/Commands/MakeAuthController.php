<?php
namespace Safia\ArtisanCommand\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeAuthController extends Command
{
    protected $signature = 'make:auth-controller';
    protected $description = 'Crée un contrôleur Auth avec des Form Requests';

    public function handle()
    {
        $this->info("🚀 Création du contrôleur et des Form Requests...");

        $this->createController();
        $this->createFormRequests();

        $this->info("✅ AuthController et Form Requests générés avec succès !");
    }

    protected function createController()
    {
        $controllerPath = app_path('Http/Controllers/AuthController.php');

        if (File::exists($controllerPath)) {
            $this->error("❌ AuthController existe déjà !");
            return;
        }

        $controllerTemplate = <<<EOT
        <?php

        namespace App\Http\Controllers;

        use App\Http\Requests\RegisterRequest;
        use App\Http\Requests\LoginRequest;
        use App\Http\Requests\LogoutRequest;
        use Illuminate\Support\Facades\Auth;
        use App\Models\User;
        use Illuminate\Support\Facades\Hash;

        class AuthController extends Controller
        {
            public function register(RegisterRequest \$request)
            {
                \$user = User::create([
                    'name' => \$request->name,
                    'email' => \$request->email,
                    'password' => Hash::make(\$request->password),
                ]);

                return response()->json(['message' => 'Inscription réussie', 'user' => \$user]);
            }

            public function login(LoginRequest \$request)
            {
                if (Auth::attempt(\$request->only('email', 'password'))) {
                    return response()->json(['message' => 'Connexion réussie']);
                }

                return response()->json(['message' => 'Échec de la connexion'], 401);
            }

            public function logout(LogoutRequest \$request)
            {
                Auth::logout();
                return response()->json(['message' => 'Déconnexion réussie']);
            }
        }
        EOT;

        File::put($controllerPath, $controllerTemplate);
        $this->info("✅ AuthController créé !");
    }

    protected function createFormRequests()
    {
        $requests = [
            'RegisterRequest' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ],
            'LoginRequest' => [
                'email' => 'required|email',
                'password' => 'required'
            ],
            'LogoutRequest' => []
        ];

        foreach ($requests as $name => $rules) {
            $requestPath = app_path("Http/Requests/{$name}.php");

            if (File::exists($requestPath)) {
                $this->error("❌ {$name} existe déjà !");
                continue;
            }

            $rulesArray = '';
            foreach ($rules as $field => $rule) {
                $rulesArray .= "'$field' => '$rule',\n            ";
            }

            $requestTemplate = <<<EOT
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class $name extends FormRequest
            {
                public function authorize()
                {
                    return true;
                }

                public function rules()
                {
                    return [
                        $rulesArray
                    ];
                }
            }
            EOT;

            File::put($requestPath, $requestTemplate);
            $this->info("✅ {$name} créé !");
        }
    }
}
