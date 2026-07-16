<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (!$user->activo) {
            return response()->json(['message' => 'Usuario inactivo'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('rol')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('rol'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ], [
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.',
        ]);

        $user = User::create([
            'rol_id' => 3,
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activo' => true
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('rol')
        ], 201);
    }

    public function actualizarPerfil(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password_actual' => 'required_with:password|nullable|string',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed',
            ],
        ], [
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La nueva contraseña debe incluir mayúscula, minúscula y número.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password_actual.required_with' => 'Debe ingresar su contraseña actual para cambiarla.',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->password_actual, $user->password)) {
                return response()->json([
                    'message' => 'La contraseña actual es incorrecta.'
                ], 422);
            }

            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->save();

        return response()->json($user->load('rol'));
    }
}