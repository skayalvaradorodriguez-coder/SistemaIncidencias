<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(
            User::with('rol')->get()
        );
    }

    public function show($id)
    {
        $user = User::with('rol')->findOrFail($id);

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate(

            [
                'name' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|max:50',
                'rol_id' => 'required|exists:roles,id',
                'activo' => 'required|boolean'
            ],

            [
                'name.required' => 'El nombre es obligatorio.',
                'apellido.required' => 'El apellido es obligatorio.',

                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'Ingrese un correo electrónico válido.',
                'email.unique' => 'Este correo ya está registrado.',

                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',

                'rol_id.required' => 'Debe seleccionar un rol.',
                'rol_id.exists' => 'El rol seleccionado no existe.',

                'activo.required' => 'Debe seleccionar un estado.'
            ]

        );

        $user = User::create([

            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'activo' => $request->activo

        ]);

        return response()->json(
            $user->load('rol'),
            201
        );
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate(

            [
                'name' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6|max:50',
                'rol_id' => 'required|exists:roles,id',
                'activo' => 'required|boolean'
            ],

            [
                'name.required' => 'El nombre es obligatorio.',
                'apellido.required' => 'El apellido es obligatorio.',

                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'Ingrese un correo electrónico válido.',
                'email.unique' => 'Este correo ya está registrado.',

                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',

                'rol_id.required' => 'Debe seleccionar un rol.',
                'rol_id.exists' => 'El rol seleccionado no existe.',

                'activo.required' => 'Debe seleccionar un estado.'
            ]

        );

        $user->update([

            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'activo' => $request->activo

        ]);

        if ($request->filled('password')) {

            $user->password = Hash::make($request->password);
            $user->save();

        }

        return response()->json(
            $user->load('rol')
        );
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'activo' => false
        ]);

        return response()->json([
            'message' => 'Usuario desactivado correctamente.'
        ]);
    }
}