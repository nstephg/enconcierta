<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AccountController extends Controller
{
    /**
     * Cambiar inmediatamente a otra cuenta iniciada en la sesión.
     */
    public function switchAccount(Request $request, $id)
    {
        $savedAccounts = session('saved_accounts', []);

        if (!in_array($id, $savedAccounts)) {
            return back()->with('error', 'Esta cuenta no está autorizada en la sesión actual.');
        }

        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'El melómano solicitado no existe.');
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Has cambiado a la cuenta de @' . $user->handle);
    }

    /**
     * Quitar una cuenta de la lista de cuentas recordadas.
     */
    public function removeAccount(Request $request, $id)
    {
        $savedAccounts = session('saved_accounts', []);
        $savedAccounts = array_values(array_diff($savedAccounts, [$id]));
        session(['saved_accounts' => $savedAccounts]);

        // Si se elimina la cuenta que está actualmente activa
        if (Auth::id() == $id) {
            if (!empty($savedAccounts)) {
                $nextUser = User::find($savedAccounts[0]);
                if ($nextUser) {
                    Auth::login($nextUser);
                    return redirect()->route('dashboard');
                }
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('home');
        }

        return back();
    }
}