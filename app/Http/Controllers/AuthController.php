<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Log in and generate a JWT token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function login(Request $request)
{
    $input = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($input)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid Email or Password',
        ], Response::HTTP_UNAUTHORIZED);
    }

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => Auth::user(),  // Include user details in the response
    ]);
}


    /**
     * Register a new user and send a verification email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'telephone' => 'required|string',
            'statut' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'nullable|string|in:admin,user', // Rôle optionnel
            'avatar' => 'nullable|string', // Optional avatar field
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password), 'isActive' => true,'role' => $request->role ?? 'user']
        ));

        // Send verification email
        $verificationUrl = route('verify.email', ['email' => $user->email]);
        Mail::send([], [], function ($message) use ($user, $verificationUrl) {
            $message->to($user->email)
                ->subject('Email Verification')
                ->html("
                    <h2>Hi, {$user->name}! Thank you for registering on our site.</h2>
                    <p>Please verify your email to continue:</p>
                    <a href='{$verificationUrl}'>Click here to verify your email</a>
                ");
        });

        return response()->json([
            'message' => 'User successfully registered. Please verify your email.',
            'user' => $user,
        ], 201);
    }
    /**
 * Met à jour le rôle d'un utilisateur.
 *
 * @param \Illuminate\Http\Request $request
 * @param int $id
 * @return \Illuminate\Http\JsonResponse
 */
public function updateUserRole(Request $request, $id)
{
    // Vérifie si l'utilisateur existe
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'Utilisateur non trouvé',
        ], 404);
    }

    // Valide uniquement le rôle
    $validated = $request->validate([
        'role' => 'required|string|in:admin,user',
    ]);

    // Mise à jour du rôle
    $user->role = $validated['role'];
    $user->save();

    return response()->json([
        'message' => 'Rôle mis à jour avec succès',
        'user' => $user,
    ]);
}

    /**
     * Verify the user's email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        // Récupérer l'email depuis la requête GET (ex: /verify-email?email=test@example.com)
        $email = $request->query('email');
    
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Email parameter is missing',
            ], 400);
        }
    
        // Rechercher l'utilisateur par email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    
        // Si le compte est déjà activé
        if ($user->isActive) {
            return response()->json([
                'success' => true,
                'message' => 'Account already activated',
            ]);
        }
    
        // Activer le compte
        $user->isActive = true;
        $user->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Account activated successfully',
        ]);
    }
    
    /**
     * Log the user out and invalidate the token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Refresh the JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(Auth::refresh());
    }

    /**
     * Get the authenticated user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(), // ou auth()->user()
        ]);
    }
    
    /**
     * Create a new token structure for the response.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => Auth::user(),
        ]);
    }
    public function listUsers()
    {
        $users = User::all(); // Vous pouvez personnaliser cette requête pour paginer ou filtrer si nécessaire
        return response()->json([
            'users' => $users,
        ]);
    }

    // Fonction de suppression d'un utilisateur
    public function deleteUser($id)
    {
        // Trouver l'utilisateur par son ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé',
            ], Response::HTTP_NOT_FOUND);
        }

        // Si vous voulez empêcher la suppression de l'utilisateur connecté ou de l'admin, vous pouvez ajouter une vérification ici
        

        // Suppression de l'utilisateur
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ], Response::HTTP_OK);
    }
    public function addUser(Request $request)
    {
        // Valider toutes les données nécessaires
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string',
            'statut' => 'required|string',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:admin,user', // Rôle optionnel
            'avatar' => 'nullable|string', // URL ou chemin de l'avatar
            'isActive' => 'required|boolean',
        ]);
    
        // Créer un utilisateur avec tous les champs
        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'statut' => $validated['statut'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'user', // Définit "user" par défaut
            'avatar' => $validated['avatar'] ?? null,
            'isActive' => $validated['isActive'],
        ]);
    
        return response()->json([
            'message' => 'Utilisateur ajouté avec succès',
            'user' => $user
        ], 201);
    }
    public function updateUser(Request $request, $id)
{
    // Trouver l'utilisateur par ID
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'Utilisateur non trouvé',
        ], 404);
    }

    // Valider les données de la requête
    $validatedData = $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'telephone' => 'required|string',
        'statut' => 'required|string',
        'role' => 'required|string',
        'avatar' => 'nullable|string',
        'isActive' => 'required|boolean',
    ]);

    // Mettre à jour les informations de l'utilisateur
    $user->update($validatedData);

    return response()->json([
        'message' => 'Utilisateur mis à jour avec succès',
        'user' => $user,
    ]);
    
}
 // Fonction pour récupérer un utilisateur par ID
 public function getUserById($id)
 {
     // Récupérer l'utilisateur par son ID
     $user = User::find($id);

     // Si l'utilisateur n'est pas trouvé
     if (!$user) {
         return response()->json([
             'message' => 'Utilisateur non trouvé',
         ], 404);
     }

     // Retourner l'utilisateur sous forme de réponse JSON
     return response()->json([
         'user' => $user
     ]);
 }
    

    
}