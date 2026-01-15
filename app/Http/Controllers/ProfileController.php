<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * View user profile (with restrictions for customers)
     */
    public function view(Request $request, $id): View|RedirectResponse
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        // Customer cannot view other customer profiles
        if ($currentUser->isCustomer() && $user->isCustomer() && $currentUser->id !== $user->id) {
            return Redirect::back()->with('error', 'Anda tidak dapat melihat profil customer lain.');
        }

        // Get additional data based on role
        $data = ['user' => $user];
        
        if ($user->isMechanic()) {
            $data['reviews'] = $user->mechanicReviews()->with('customer', 'booking')->latest()->paginate(10);
            $data['avgRating'] = $user->mechanicReviews()->avg('rating_mechanic') ?? 0;
            $data['totalReviews'] = $user->mechanicReviews()->count();
            $data['totalBookings'] = $user->assignedBookings()->count();
            $data['totalEarnings'] = $user->salaries()->where('status', 'paid')->sum('total_amount');
        }

        return view('profile.view', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update basic info
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && file_exists(public_path('storage/photos/' . $user->photo))) {
                unlink(public_path('storage/photos/' . $user->photo));
            }
            
            $photo = $request->file('photo');
            $filename = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('storage/photos'), $filename);
            $user->photo = $filename;
        }

        // Update phone if provided
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
