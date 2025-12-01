<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AdoptablePetsController extends Controller
{
    /**
     * Display a listing of adoptable pets
     */
    public function adoptList()
    {
        // Fetch all pets with their relationships
        $petlist = Pet::with(['breed.petTypes', 'photos'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('User.adoptList', compact('petlist'));
    }

    /**
     * Store a new adoption application
     */
    public function store(Request $request)
    {
        // Check if user is authenticated FIRST
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit an adoption application.'
            ], 401);
        }

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ], [
            'description.min' => 'Please provide at least 10 characters explaining why you want to adopt this pet.',
            'mobile_number.required' => 'Mobile number is required.',
            'address.required' => 'Address is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if pet exists and is still available
            $pet = Pet::findOrFail($request->pet_id);
            
            if ($pet->pet_status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'This pet is no longer available for adoption.'
                ], 400);
            }

            // Check if user already has a pending application for this pet
            $existingApplication = AdoptionApplication::where('user_id', Auth::id())
                ->where('pet_id', $request->pet_id)
                ->whereIn('application_status', ['Submitted', 'Approved', 'pending', 'approved'])
                ->first();

            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending application for this pet.'
                ], 400);
            }

            // Create the adoption application
            $adoption = AdoptionApplication::create([
                'user_id' => Auth::id(),
                'pet_id' => $request->pet_id,
                'mobile_number' => $request->mobile_number,
                'address' => $request->address,
                'description' => $request->description,
                'application_status' => 'Submitted', // Changed to match the frontend expectations
                'application_date' => now(),
                'admin_notes' => null,
            ]);

            // Update pet status to pending
            $pet->update(['pet_status' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => 'Your adoption application has been submitted successfully! We will contact you soon.',
                'adoption_id' => $adoption->id
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Adoption Application Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pet_id' => $request->pet_id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your application. Please try again.',
            ], 500);
        }
    }

    /**
     * Display all adoption applications for admin
     */
    public function userApplications()
    {
        // Fetch all applications with relationships
        $applications = AdoptionApplication::with(['user', 'pet'])
            ->orderBy('application_date', 'desc')
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'pet_id' => $application->pet_id,
                    'pet_name' => $application->pet->name ?? 'Unknown Pet',
                    'pet_status' => $application->pet->pet_status ?? 'unknown',
                    'user_id' => $application->user_id,
                    'user_name' => $application->user->name ?? 'Unknown User',
                    'mobile_number' => $application->mobile_number,
                    'address' => $application->address,
                    'description' => $application->description,
                    'application_date' => $application->application_date,
                    'application_status' => $application->application_status,
                    'admin_notes' => $application->admin_notes,
                ];
            });

        return view('Admin.userApplications', compact('applications'));
    }

    /**
     * Approve an adoption application
     */
    public function approve($id)
    {
        try {
            $application = AdoptionApplication::with('pet')->findOrFail($id);
            
            // Update application status
            $application->update([
                'application_status' => 'Approved'
            ]);
            
            // Update pet status to adopted
            if ($application->pet) {
                $application->pet->update([
                    'pet_status' => 'adopted'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully! The pet has been marked as adopted.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Application Approval Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject an adoption application
     */
    public function reject($id)
    {
        try {
            $application = AdoptionApplication::with('pet')->findOrFail($id);
            
            // Update application status
            $application->update([
                'application_status' => 'Rejected'
            ]);
            
            // Update pet status back to available
            if ($application->pet) {
                $application->pet->update([
                    'pet_status' => 'available'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Application rejected. The pet has been marked as available again.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Application Rejection Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application. Please try again.'
            ], 500);
        }
    }
}