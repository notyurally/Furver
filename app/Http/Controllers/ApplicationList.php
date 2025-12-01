<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdoptionApplicationController extends Controller
{
    /**
     * Display the admin applications dashboard (Blade view).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all applications with pet and user relationships
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

        return view('Pets.applications', compact('applications'));
    }

    /**
     * Display the adoptable pets listing page (User view).
     *
     * @return \Illuminate\Http\Response
     */
    public function adoptList()
    {
        // Get all available and pending pets with their relationships
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

        DB::beginTransaction();
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

            DB::commit();

            // Return JSON response for AJAX handling
            return response()->json([
                'success' => true,
                'message' => 'Your adoption application has been submitted successfully!',
                'application_id' => $application->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Application submission failed: " . $e->getMessage());
            
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
        DB::beginTransaction();
        try {
            $application = AdoptionApplication::findOrFail($id);
            $pet = $application->pet;
            
            // Update application status to Approved
            $application->update(['application_status' => 'Approved']);
            
            // Update pet status to 'adopted'
            if ($pet) {
                $pet->update(['pet_status' => 'adopted']);
            }

            // Reject all other pending applications for this pet
            AdoptionApplication::where('pet_id', $application->pet_id)
                ->where('id', '!=', $application->id)
                ->where('application_status', 'Submitted')
                ->update(['application_status' => 'Rejected']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully! Pet is now adopted.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Approval failed: " . $e->getMessage());
            
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
        DB::beginTransaction();
        try {
            $application = AdoptionApplication::findOrFail($id);
            $pet = $application->pet;
            
            // Update application status to Rejected
            $application->update(['application_status' => 'Rejected']);
            
            // Check if there are other pending applications for this pet
            $hasPendingApplications = AdoptionApplication::where('pet_id', $application->pet_id)
                ->where('application_status', 'Submitted')
                ->exists();

            // Update pet status back to 'available' only if no other pending applications
            if ($pet && !$hasPendingApplications) {
                $pet->update(['pet_status' => 'available']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Rejection failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application.'
            ], 500);
        }
    }

    /**
     * Get all applications data as JSON (for admin dashboard AJAX).
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