<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(User::with('rol')->get());
    }

    public function show($id)
    {
        $user = User::with('rol')->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'rol_id' => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'activo' => true
        ]);

        return response()->json($user->load('rol'), 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'string|max:100',
            'apellido' => 'string|max:100',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'rol_id' => 'exists:roles,id',
            'activo' => 'boolean'
        ]);

        $user->update(
            $request->only([
                'name',
                'apellido',
                'email',
                'rol_id',
                'activo'
            ])
        );

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return response()->json($user->load('rol'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'activo' => false
        ]);

        return response()->json([
            'message' => 'Usuario desactivado'
        ]);
    }
}