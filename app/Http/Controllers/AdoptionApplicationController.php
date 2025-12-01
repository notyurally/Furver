<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Http\Requests\StoreAdoptionApplicationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pet;

class AdoptionApplicationController extends Controller
{
    /**
     * Display a listing of all adoption applications (Admin view).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // For admin panel - get all applications with related pet and user data
        $applications = AdoptionApplication::with(['pet', 'user'])
            ->orderBy('application_date', 'desc')
            ->get();

        return view('Pets.application', compact('applications'));
    }

    /**
     * Display the adoptable pets listing page (User view).
     *
     * @return \Illuminate\Http\Response
     */
    public function adoptList()
    {
        // Get all available pets with their relationships
        $petlist = Pet::with(['breed.petTypes', 'photos'])
            ->whereIn('pet_status', ['available', 'pending'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('User.adoptList', compact('petlist'));
    }

    /**
     * Store a newly created adoption application in storage.
     * This handles the AJAX/form submission from the popup modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'mobile_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'required|string|max:1000',
        ]);

        try {
            // Create the adoption application
            $application = AdoptionApplication::create([
                'user_id' => Auth::id(),
                'pet_id' => $validated['pet_id'],
                'mobile_number' => $validated['mobile_number'],
                'address' => $validated['address'],
                'description' => $validated['description'],
                'application_date' => Carbon::now(),
                'application_status' => 'Submitted',
            ]);

            // Update pet status to 'pending'
            Pet::where('id', $validated['pet_id'])->update(['pet_status' => 'pending']);

            // Return JSON response for AJAX handling
            return response()->json([
                'success' => true,
                'message' => 'Your adoption application has been submitted successfully!',
                'application_id' => $application->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application. Please try again.'
            ], 500);
        }
    }

    /**
     * Approve an adoption application (Admin only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve($id)
    {
        try {
            $application = AdoptionApplication::findOrFail($id);
            
            // Update application status
            $application->update(['application_status' => 'Approved']);
            
            // Update pet status to 'adopted'
            Pet::where('id', $application->pet_id)->update(['pet_status' => 'adopted']);

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application.'
            ], 500);
        }
    }

    /**
     * Reject an adoption application (Admin only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject($id)
    {
        try {
            $application = AdoptionApplication::findOrFail($id);
            
            // Update application status
            $application->update(['application_status' => 'Rejected']);
            
            // Update pet status back to 'available'
            Pet::where('id', $application->pet_id)->update(['pet_status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application.'
            ], 500);
        }
    }

    /**
     * Get all applications data as JSON (for admin dashboard).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData()
    {
        $applications = AdoptionApplication::with(['pet', 'user'])
            ->orderBy('application_date', 'desc')
            ->get()
            ->map(function($app) {
                return [
                    'id' => $app->id,
                    'pet_id' => $app->pet_id,
                    'pet_name' => $app->pet->name ?? 'Unknown',
                    'pet_status' => $app->pet->pet_status ?? 'unknown',
                    'user_id' => $app->user_id,
                    'user_name' => $app->user->name ?? 'Unknown',
                    'mobile_number' => $app->mobile_number,
                    'address' => $app->address,
                    'description' => $app->description,
                    'application_date' => $app->application_date,
                    'application_status' => $app->application_status,
                ];
            });

        return response()->json($applications);
    }
}