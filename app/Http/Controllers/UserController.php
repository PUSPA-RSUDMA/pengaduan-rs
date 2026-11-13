<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                return redirect()->route('login');
            }
            if (auth()->user()->role !== 'admin' && !auth()->user()->hasPermission('users', 'view')) {
                abort(403, 'ANDA TIDAK PUNYA AKSES KE HALAMAN INI.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::latest()->get();
        $availableMenus = User::availableMenus();
        $units = UnitDestination::orderBy('name', 'asc')->get();

        return view('users.index', compact('users', 'availableMenus', 'units'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('users', 'create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah user.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'role'        => 'required|string',
            'unit_name'   => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email salah.',
            'email.unique'      => 'Email ini sudah dipakai user lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 8 karakter.',
        ]);

        $permissions = $this->sanitizePermissions($request->input('permissions', []), $request->role);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'unit_name'   => $request->unit_name,
            'permissions' => $permissions,
        ]);

        return redirect()->route('users.index')->with('success', 'User baru beserta hak akses berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermission('users', 'edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data user.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'role'        => 'required|string',
            'unit_name'   => 'nullable|string|max:255',
            'password'    => 'nullable|min:8',
            'permissions' => 'nullable|array',
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email ini sudah dipakai user lain.',
            'password.min'   => 'Password baru minimal harus 8 karakter.',
        ]);

        $permissions = $this->sanitizePermissions($request->input('permissions', []), $request->role);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'unit_name'   => $request->unit_name,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $pesan = 'Data user, password, dan hak akses berhasil diperbarui!';
        } else {
            $pesan = 'Data user dan hak akses berhasil diperbarui!';
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', $pesan);
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasPermission('users', 'delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus user.');
        }

        if (auth()->user()->id == $user->id) {
            return redirect()->back()->withErrors(['Anda tidak bisa menghapus akun sendiri!']);
        }

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Rapikan struktur array permissions yang dikirim dari form.
     * Jika suatu menu memiliki create/edit/delete, otomatis pastikan 'view' juga aktif.
     */
    private function sanitizePermissions(array $rawPermissions, string $role): ?array
    {
        if ($role === 'admin') {
            return null; // Admin selalu full akses
        }

        $cleaned = [];
        $validMenus = [];
        foreach (User::availableMenus() as $group => $menus) {
            foreach ($menus as $key => $meta) {
                $validMenus[$key] = $meta['actions'];
            }
        }

        foreach ($rawPermissions as $menuKey => $actions) {
            if (!isset($validMenus[$menuKey]) || !is_array($actions)) {
                continue;
            }

            $allowedActions = array_values(array_intersect($actions, $validMenus[$menuKey]));
            if (!empty($allowedActions)) {
                // Jika punya akses create/edit/delete, pastikan 'view' selalu disertakan
                if (!in_array('view', $allowedActions, true) && in_array('view', $validMenus[$menuKey], true)) {
                    array_unshift($allowedActions, 'view');
                }
                $cleaned[$menuKey] = array_values(array_unique($allowedActions));
            }
        }

        return $cleaned;
    }
}
