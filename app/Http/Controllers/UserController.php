<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerificationMail;

class UserController extends Controller
{
    private function redirectToFrontLogin()
    {
        return redirect()->to(route('index') . '#login');
    }

    private function getSessionUser(Request $request)
    {
        $sessionUser = $request->session()->get('user');

        if (!$sessionUser) {
            return null;
        }

        $user = User::find($sessionUser->id);

        if ($user && $user->role === 'rider') {
            $user->update(['last_seen' => now()]);
        }

        return $user;
    }

    // login page

    public function showLoginForm()
    {
        return view('index');
    }
    // login

    public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', '')))
        ]);

        try {
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'
                ],
                'password' => 'required',
            ], [
                'email.regex' => 'Please enter a valid Gmail address (@gmail.com).'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->redirectToFrontLogin()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_form', 'loginForm');
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->redirectToFrontLogin()
                    ->with('error', 'Invalid email or password.')
                    ->withInput()
                    ->with('active_form', 'loginForm');
            }

            $request->session()->put('user', $user);

            if ($user->role === 'rider') {
                $user->update([
                    'status' => 'Online',
                    'last_seen' => now()
                ]);
            }

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'instructor' => redirect()->route('instructor.dashboard'),
                'rider' => redirect()->route('rider.dashboard'),
                default => redirect()->route('customer.dashboard'),
            };
        } catch (\Exception $e) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Login encountered an issue. Please try again later.')
                ->withInput()
                ->with('active_form', 'loginForm');
        }
    }
    // logout

    public function logout(Request $request)
    {
        // Auto-set rider status to Offline on logout
        $user = $this->getSessionUser($request);
        if ($user && $user->role === 'rider') {
            $user->update([
                'status' => 'Offline',
                'last_seen' => now()
            ]);
        }

        $request->session()->forget('user');

        return redirect()->route('index');
    }
    // dashb

    public function adminDashboard()
    {
        return view('admin');
    }

    public function riderDashboard()
    {
        return view('riders');
    }

    public function riderDelivery()
    {
        return view('rider.delivery');
    }

    public function customerDashboard()
    {
        return view('customer');
    }

    // rider adm
    public function storeRider(Request $request)
    {
        $admin = $this->getSessionUser($request);

        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'            => 'required|max:255',
            'email'           => 'required|email|unique:user,email',
            'password'        => 'required|min:6',
            'phone'           => 'nullable|max:50',
            'vehicle'         => 'nullable|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $imageName   = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/profiles'), $imageName);
            $profilePath = 'images/profiles/' . $imageName;
        }

        try {
            // Save rider in the database
            $rider = User::create([
                'name'            => $data['name'],
                'email'           => strtolower(trim($data['email'])),
                'phone'           => $data['phone'] ?? null,
                'password'        => Hash::make($data['password']),
                'role'            => 'rider',
                'address'         => $request->riderAddress ?? null,
                'vehicle'         => $data['vehicle'] ?? null,
                'profile_picture' => $profilePath,
            ]);

            // Generate Rider ID for display
            $riderId = 'R-' . str_pad($rider->id, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'ok' => true,
                'rider' => [
                    'id'              => $riderId,
                    'real_id'         => $rider->id,
                    'name'            => $rider->name,
                    'email'           => $rider->email,
                    'tempPassword'    => $data['password'],
                    'phone'           => $rider->phone,
                    'status'          => $rider->status ?? 'Available',
                    'vehicle'         => $data['vehicle'] ?? '',
                    'profile_picture' => $profilePath ? asset($profilePath) : null,
                    'last'            => 'Newly added',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create rider. Check connection and retry.'], 500);
        }
    }

    public function listRiders(Request $request)
    {
        $admin = $this->getSessionUser($request);
        if (!$admin || $admin->role !== 'admin') {
            return response()->json([], 403);
        }

        $riders = User::where('role', 'rider')->get()->map(function ($r) {
            $rawStatus = $r->status ?? 'Offline';
            $lastSeenHuman = null;

            if ($r->last_seen) {
                $lastSeenHuman = \Carbon\Carbon::parse($r->last_seen)->diffForHumans();
            }

            return [
                'id'              => 'R-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                'real_id'         => $r->id,
                'name'            => $r->name,
                'email'           => $r->email,
                'phone'           => $r->phone,
                'status'          => $rawStatus,
                'last_seen_human' => $lastSeenHuman,
                'address'         => $r->address ?? '',
                'vehicle'         => $r->vehicle ?? '',
                'profile_picture' => $r->profile_picture ? asset($r->profile_picture) : null,
                'last'            => $r->last_seen ? \Carbon\Carbon::parse($r->last_seen)->format('M d, Y h:i A') : 'N/A',
            ];
        });

        return response()->json($riders);
    }

    public function updateRider(Request $request, $id)
    {
        $admin = $this->getSessionUser($request);
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rider = User::where('role', 'rider')->where('id', $id)->first();
        if (!$rider) {
            return response()->json(['message' => 'Rider not found'], 404);
        }

        $data = $request->validate([
            'name'            => 'required|max:255',
            'phone'           => 'nullable|max:50',
            'address'         => 'nullable|max:500',
            'vehicle'         => 'nullable|max:100',
            'status'          => 'nullable|string|in:Online,Offline,On Delivery',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $updates = [
            'name'    => $data['name'],
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'vehicle' => $data['vehicle'] ?? null,
            'status'  => $data['status'] ?? $rider->status,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $imageName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/profiles'), $imageName);
            $updates['profile_picture'] = 'images/profiles/' . $imageName;
        }
        // If no new file, keep existing — do NOT overwrite with null

        try {
            $rider->update($updates);
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update rider.'], 500);
        }
    }

    // cus prf page
    public function customerProfile(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login first.');
        }

        return view('customer.profile', compact('user'));
    }

    // updt prf

    public function updateCustomerProfile(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login again.');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:user,email,' . $user->id,
            'phone' => 'nullable|max:50',
            'address' => 'nullable|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $imageName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/profiles'), $imageName);
            $validated['profile_picture'] = 'images/profiles/' . $imageName;
        }

        $user->update($validated);

        $request->session()->put('user', $user);

        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully.');
    }

    // rider prf
    public function riderProfile(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user || $user->role !== 'rider') {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login as a rider.');
        }

        return view('rider.profile', compact('user'));
    }

    // rider prof upt
    public function updateRiderProfile(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user || $user->role !== 'rider') {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login as a rider.');
        }

        // Validate rider-specific fields
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'emergency' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $imageName = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/profiles'), $imageName);
            $validated['profile_picture'] = 'images/profiles/' . $imageName;
        }

        // Update user and refresh session
        $user->update($validated);
        $request->session()->put('user', $user);

        return redirect()->route('rider.profile')
            ->with('success', 'Profile updated successfully.');
    }

    // rider prof upt pass
    public function updateRiderPassword(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user || $user->role !== 'rider') {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login as a rider.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('rider.profile')
                ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('rider.profile')
            ->with('success', 'Password updated successfully.');
    }

    // upt pass

    public function updateCustomerPassword(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please login again.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('customer.profile')
                ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('customer.profile')
            ->with('success', 'Password updated successfully.');
    }

    // reg
    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', '')))
        ]);

        try {
            $data = $request->validate([
                'name' => 'required|max:255',
                'email' => [
                    'required',
                    'email',
                    'unique:user,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'
                ],
                'password' => 'required|min:6|confirmed',
            ], [
                'email.regex' => 'Registration is restricted to Gmail accounts (@gmail.com) only.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->redirectToFrontLogin()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_form', 'registerForm');
        }

        try {
            $code = rand(100000, 999999);
            
            $request->session()->put('pending_user', [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
                'verification_code' => $code,
                'expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($data['email'])->send(new VerificationMail($code));

            return $this->redirectToFrontLogin()
                ->with('success', 'A verification code has been sent to your Gmail. Please enter it to complete registration.')
                ->with('active_form', 'verifyOTP');
        } catch (\Exception $e) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Failed to send verification code. Please check your connection.')
                ->withInput()
                ->with('active_form', 'registerForm');
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:6',
            ], [
                'otp.digits' => 'The verification code must be exactly 6 digits with no letters or special characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->redirectToFrontLogin()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_form', 'verifyOTP');
        }

        $pending = $request->session()->get('pending_user');

        if (!$pending) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Session expired. Please register again.')
                ->with('active_form', 'registerForm');
        }

        if (now()->gt($pending['expires_at'])) {
            $request->session()->forget('pending_user');
            return $this->redirectToFrontLogin()
                ->with('error', 'Verification code expired. Please register again.')
                ->with('active_form', 'registerForm');
        }

        if ($request->otp !== (string)$pending['verification_code']) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Invalid verification code. Please try again.')
                ->with('active_form', 'verifyOTP');
        }

        try {
            $user = User::create([
                'name' => $pending['name'],
                'email' => $pending['email'],
                'password' => $pending['password'],
                'role' => 'customer',
            ]);

            $request->session()->forget('pending_user');
            $request->session()->put('user', $user);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Email verified successfully! Welcome to Krusty Krab.');
        } catch (\Exception $e) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Failed to create account. Please try again.')
                ->with('active_form', 'verifyOTP');
        }
    }

    public function resendOTP(Request $request)
    {
        $pending = $request->session()->get('pending_user');

        if (!$pending) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Please register first.')
                ->with('active_form', 'registerForm');
        }

        try {
            $code = rand(100000, 999999);
            $pending['verification_code'] = $code;
            $pending['expires_at'] = now()->addMinutes(10);
            $request->session()->put('pending_user', $pending);

            Mail::to($pending['email'])->send(new VerificationMail($code));

            return $this->redirectToFrontLogin()
                ->with('success', 'New verification code sent to your Gmail.')
                ->with('active_form', 'verifyOTP');
        } catch (\Exception $e) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Failed to resend code. Please try again.')
                ->with('active_form', 'verifyOTP');
        }
    }
    // forg pass

    public function forgotpassPost(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', '')))
        ]);

        try {
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'exists:user,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'
                ],
            ], [
                'email.regex' => 'Please enter a valid Gmail address (@gmail.com).'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->redirectToFrontLogin()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_form', 'forgotPass');
        }

        $user = User::where('email', $request->email)->first();

        // Generate a 6-digit numeric code
        $code = rand(100000, 999999);

        try {
            $user->update([
                'password_reset_token' => $code,
                'token_expires_at' => now()->addMinutes(15), // Shorter expiry for OTP
            ]);

            Mail::to($user->email)->send(new ResetPasswordMail($code));

            // Store email in session for the next step
            $request->session()->put('reset_email', $user->email);

            return $this->redirectToFrontLogin()
                ->with('success', 'A verification code has been sent to your email.')
                ->with('active_form', 'verifyResetOTP');

        } catch (\Exception $e) {
            if ($user) {
                $user->update(['password_reset_token' => null, 'token_expires_at' => null]);
            }
            return $this->redirectToFrontLogin()
                ->with('error', 'Failed to send email. Please check your internet connection.')
                ->withInput()
                ->with('active_form', 'forgotPass');
        }
    }

    public function verifyResetOTP(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|digits:6',
            ], [
                'otp.digits' => 'The verification code must be exactly 6 digits with no letters or special characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->redirectToFrontLogin()
                ->withErrors($e->validator)
                ->withInput()
                ->with('active_form', 'verifyResetOTP');
        }

        $email = $request->session()->get('reset_email');

        if (!$email) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Session expired. Please try again.')
                ->with('active_form', 'forgotPass');
        }

        $user = User::where('email', $email)
            ->where('password_reset_token', $request->otp)
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Invalid or expired verification code.')
                ->with('active_form', 'verifyResetOTP');
        }

        // Code is valid, move to password reset form
        return $this->redirectToFrontLogin()
            ->with('success', 'Code verified. You can now reset your password.')
            ->with('active_form', 'resetPasswordForm');
    }


    // res pass

    public function resetPassword(Request $request)
    {
        $email = $request->session()->get('reset_email');

        if (!$email) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Session expired. Please try again.')
                ->with('active_form', 'forgotPass');
        }

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->redirectToFrontLogin()
                ->with('error', 'User not found.');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
                'token_expires_at' => null,
            ]);

            $request->session()->forget('reset_email');

            return $this->redirectToFrontLogin()
                ->with('success', 'Password successfully reset! You can now login.')
                ->with('active_form', 'loginForm');
        } catch (\Exception $e) {
            return $this->redirectToFrontLogin()
                ->with('error', 'Failed to save new password. Please retry.')
                ->with('active_form', 'resetPasswordForm');
        }
    }

    public function manageRidersPage(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user || $user->role !== 'admin') {
            return redirect()->route('index')
                ->with('error', 'Please login as admin.');
        }

        return view('admin.manage-riders');
    }

    public function destroyRider(Request $request, $id)
    {
        $admin = $this->getSessionUser($request);
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $rider = User::where('role', 'rider')->where('id', $id)->first();
            if (!$rider) {
                return response()->json(['message' => 'Rider not found'], 404);
            }

            $rider->delete();
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete rider.'], 500);
        }
    }
    public function manageCustomersPage(Request $request)
    {
        $user = $this->getSessionUser($request);

        if (!$user || $user->role !== 'admin') {
            return redirect()->route('index')
                ->with('error', 'Please login as admin.');
        }

        return view('admin.manage-customers');
    }

    public function listCustomers(Request $request)
    {
        $admin = $this->getSessionUser($request);
        if (!$admin || $admin->role !== 'admin') {
            return response()->json([], 403);
        }

        $sort = $request->query('sort', 'newest');

        $query = User::where('role', 'customer');

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $customers = $query->get()->map(function ($c) {
            $latestOrder = \Illuminate\Support\Facades\DB::table('orders')
                ->where('customer_id', $c->id)
                ->latest('created_at')
                ->first();

            return [
                'id' => 'C-' . str_pad($c->id, 3, '0', STR_PAD_LEFT),
                'real_id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone ?? 'N/A',
                'profile_picture' => $c->profile_picture ? asset($c->profile_picture) : null,
                'latest_order' => $latestOrder ? \Carbon\Carbon::parse($latestOrder->created_at)->format('M d, Y h:i A') : 'No orders yet',
                'total_orders' => \Illuminate\Support\Facades\DB::table('orders')->where('customer_id', $c->id)->count(),
                'joined_date' => $c->created_at->format('M d, Y'),
            ];
        });

        return response()->json($customers);
    }

    public function updateRiderStatus(Request $request)
    {
        $user = $this->getSessionUser($request);
        if (!$user || $user->role !== 'rider') {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'status' => 'required|string|in:Online,Offline'
        ]);

        $user->update(['status' => $request->status]);
        $request->session()->put('user', $user);

        return response()->json(['success' => true, 'status' => $user->status]);
    }
}
