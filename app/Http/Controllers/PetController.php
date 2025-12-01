<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Breed;
use App\Models\Location;
use App\Models\PetTypes;
use App\Models\PetPhoto;
use App\Models\PetHealth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    /* ----------------------------------------------------------
     | LIST PETS (ALL USERS)
     ---------------------------------------------------------- */
    public function index(Request $request)
    {
        $pets = Pet::with(['breed.petTypes', 'profilePhoto', 'health'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // Statistics
        $totalPetsCount = $pets->count();
        $availablePetsCount = $pets->where('pet_status', 'available')->count();
        $adoptedPetsCount = $pets->where('pet_status', 'adopted')->count();
        
        // Count dogs
        $totalDogsCount = $pets->filter(function($pets) {
            return $pets->breed && $pets->breed->petTypes && 
                   strtolower($pets->breed->petTypes->name) === 'dog';
        })->count();

        $totalCatsCount = $pets->filter(function($pets) {
            return $pets->breed && $pets->breed->petTypes && 
                   strtolower($pets->breed->petTypes->name) === 'cat';
        })->count();

        // Get all pet types and breeds for filters
        $petTypes = PetTypes::all();
        $breeds = Breed::all();

        return view('Pets.dashboard', compact(
            'pets',
            'totalPetsCount',
            'availablePetsCount',
            'adoptedPetsCount',
            'totalDogsCount',
            'totalCatsCount',
            'petTypes',
            'breeds'
        ));
    }

    /* ----------------------------------------------------------
     | MANAGE MY PETS
     ---------------------------------------------------------- */
    public function myPets(Request $request)
    {
        $query = Pet::with(['breed.petTypes','profilePhoto'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('name','LIKE','%'.$request->search.'%');
        }
        
        if ($request->filled('pet_type')) {
            $query->whereHas('breed', function($q) use ($request) {
                $q->where('pet_types_id', $request->pet_type);
            });
        }

        if ($request->filled('status')) {
            $query->where('pet_status', $request->status);
        }

        $pets = $query->paginate(12);

        return view('Pets.petlist', [
            'pets' => $pets,
            'petTypes' => PetTypes::all(),
        ]);
    }

    /* ----------------------------------------------------------
     | SHOW CREATE FORM
     ---------------------------------------------------------- */
    public function create()
    {
        return view('pets.create', [
            'petTypes' => PetTypes::all(),
            'breeds' => Breed::all(),
        ]);
    }

    /* ----------------------------------------------------------
     | STORE NEW PET
     ---------------------------------------------------------- */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pet_type_id' => 'required|exists:pet_types,id',
            'breed_id' => 'required|exists:breeds,id',
            'pet_sex' => 'required|in:male,female',
            'pet_size' => 'required|in:small,medium,large,extra_large',
            'birthdate' => 'nullable|date',
            'age' => 'nullable|integer|min:0',
            'age_data' => 'nullable|in:years,months',
            'description' => 'nullable|string',
            'adoption_fee' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:5048',
            'additional_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'behavioral_traits' => 'nullable|string',
            'is_vaccinated' => 'nullable|boolean',
            'last_vaccinated_date' => 'nullable|date',
            'is_spayed' => 'nullable|boolean',
            'last_spay_date' => 'nullable|date',
            'microchip_number' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            /* CREATE PET */
            $pet = Pet::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'breed_id' => $validated['breed_id'],
                'pet_sex' => $validated['pet_sex'],
                'pet_size' => $validated['pet_size'],
                'birthdate' => $validated['birthdate'] ?? null,
                'age' => $validated['age'] ?? null,
                'age_data' => $validated['age_data'] ?? null,
                'description' => $validated['description'] ?? null,
                'adoption_fee' => $validated['adoption_fee'],
                'location' => $validated['location'],
                'pet_status' => 'available',
                'behavioral_traits' => $validated['behavioral_traits'] ?? null,
            ]);

            /* SAVE PROFILE PICTURE */
            if ($request->hasFile('profile_picture')) {
                $profilePath = $request->file('profile_picture')->store('pets/profile', 'public');
                PetPhoto::create([
                    'pet_id' => $pet->id,
                    'photo_path' => $profilePath,
                    'is_profile' => true,
                ]);
            }

            /* SAVE ADDITIONAL PHOTOS */
            if ($request->hasFile('additional_photos')) {
                foreach ($request->file('additional_photos') as $photo) {
                    $path = $photo->store('pets/additional', 'public');
                    PetPhoto::create([
                        'pet_id' => $pet->id,
                        'photo_path' => $path,
                        'is_profile' => false,
                    ]);
                }
            }

            /* SAVE HEALTH DATA */
            PetHealth::create([
                'pet_id' => $pet->id,
                'is_vaccinated' => $request->boolean('is_vaccinated'),
                'last_vaccinated_date' => $validated['last_vaccinated_date'] ?? null,
                'is_spayed' => $request->boolean('is_spayed'),
                'last_spay_date' => $validated['last_spay_date'] ?? null,
                'microchip_number' => $validated['microchip_number'] ?? null,
            ]);

            /* SAVE BEHAVIORAL TRAITS */
            if (!empty($validated['behavioral_traits'])) {
                $traits = array_map('trim', explode(',', $validated['behavioral_traits']));
                foreach ($traits as $trait) {
                    if (!empty($trait)) {
                        \App\Models\PetBehaviorTraits::create([
                            'pet_id' => $pet->id,
                            'trait' => $trait,
                            'notes' => null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('pets.myPets')->with('success', 'Pet listed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Pet creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create pet: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | SHOW PET DETAILS
     ---------------------------------------------------------- */
    public function show(Pet $pet)
    {
        return view('Pets/viewdetails', [
            'pet' => $pet,
        ]);
    }

    /* ----------------------------------------------------------
     | SHOW EDIT FORM
     ---------------------------------------------------------- */
    public function edit(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) abort(403);
        
        $pet->load(['health', 'photos', 'behaviorTraits', 'breed.petTypes']);
            
        $behaviorTraitsString = $pet->behaviorTraits->pluck('trait')->implode(',');
        $pet->behavioral_traits = $behaviorTraitsString;
        
        return view('Pets.editInfo', [
            'pet' => $pet,
            'petTypes' => PetTypes::all(),
            'breeds' => Breed::all(),
        ]);
    }

    /* ----------------------------------------------------------
     | UPDATE PET
     ---------------------------------------------------------- */
      public function update(Request $request, Pet $pet)
    {
        // Authorization check
        if ($pet->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pet_type_id' => 'required|exists:pet_types,id',
            'breed_id' => 'required|exists:breeds,id',
            'pet_sex' => 'required|in:male,female',
            'pet_size' => 'required|in:small,medium,large,extra_large',
            'birthdate' => 'nullable|date',
            'age' => 'nullable|integer|min:0',
            'age_data' => 'nullable|in:years,months',
            'description' => 'nullable|string',
            'adoption_fee' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'additional_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'behavioral_traits' => 'nullable|string',
            'is_vaccinated' => 'nullable|boolean',
            'last_vaccinated_date' => 'nullable|date',
            'is_spayed' => 'nullable|boolean',
            'last_spay_date' => 'nullable|date',
            'microchip_number' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            /* UPDATE PET */
            $pet->update([
                'name' => $validated['name'],
                'pet_type_id' => $validated['pet_type_id'],
                'breed_id' => $validated['breed_id'],
                'pet_sex' => $validated['pet_sex'],
                'pet_size' => $validated['pet_size'],
                'birthdate' => $validated['birthdate'] ?? null,
                'age' => $validated['age'] ?? null,
                'age_data' => $validated['age_data'] ?? null,
                'description' => $validated['description'] ?? null,
                'adoption_fee' => $validated['adoption_fee'],
                'location' => $validated['location'],
                'behavioral_traits' => $validated['behavioral_traits'] ?? null,
            ]);

            /* UPDATE PROFILE PICTURE */
            if ($request->hasFile('profile_picture')) {
                // Delete old profile photo
                $oldProfile = $pet->photos()->where('is_profile', true)->first();
                if ($oldProfile) {
                    if (Storage::disk('public')->exists($oldProfile->photo_path)) {
                        Storage::disk('public')->delete($oldProfile->photo_path);
                    }
                    $oldProfile->delete();
                }

                // Save new profile photo
                $profilePath = $request->file('profile_picture')->store('pets/profile', 'public');
                PetPhoto::create([
                    'pet_id' => $pet->id,
                    'photo_path' => $profilePath,
                    'is_profile' => true,
                ]);
            }

            /* DELETE SELECTED PHOTOS */
            if ($request->filled('deleted_photos')) {
                $deletedIds = explode(',', $request->deleted_photos);
                foreach ($deletedIds as $photoId) {
                    $photo = PetPhoto::where('pet_id', $pet->id)
                                    ->where('id', $photoId)
                                    ->where('is_profile', false)
                                    ->first();
                    if ($photo) {
                        if (Storage::disk('public')->exists($photo->photo_path)) {
                            Storage::disk('public')->delete($photo->photo_path);
                        }
                        $photo->delete();
                    }
                }
            }

            /* ADD NEW ADDITIONAL PHOTOS */
            if ($request->hasFile('additional_photos')) {
                foreach ($request->file('additional_photos') as $photo) {
                    $path = $photo->store('pets/additional', 'public');
                    PetPhoto::create([
                        'pet_id' => $pet->id,
                        'photo_path' => $path,
                        'is_profile' => false,
                    ]);
                }
            }

            /* UPDATE HEALTH DATA */
            PetHealth::updateOrCreate(
                ['pet_id' => $pet->id],
                [
                    'is_vaccinated' => $request->boolean('is_vaccinated'),
                    'last_vaccinated_date' => $validated['last_vaccinated_date'] ?? null,
                    'is_spayed' => $request->boolean('is_spayed'),
                    'last_spay_date' => $validated['last_spay_date'] ?? null,
                    'microchip_number' => $validated['microchip_number'] ?? null,
                ]
            );

            /* UPDATE BEHAVIORAL TRAITS */
            // Delete existing traits
            \App\Models\PetBehaviorTraits::where('pet_id', $pet->id)->delete();
            
            // Create new traits
            if (!empty($validated['behavioral_traits'])) {
                $traits = array_map('trim', explode(',', $validated['behavioral_traits']));
                foreach ($traits as $trait) {
                    if (!empty($trait)) {
                        \App\Models\PetBehaviorTraits::create([
                            'pet_id' => $pet->id,
                            'trait' => $trait,
                            'notes' => null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('pets.myPets')->with('success', 'Pet updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Pet update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update pet: ' . $e->getMessage());
        }
    }

    /* ----------------------------------------------------------
     | DELETE PET + ALL RELATED DATA
     ---------------------------------------------------------- */
    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) abort(403);

        DB::beginTransaction();

        try {
            // Delete all photos
            foreach ($pet->photos as $photo) {
                if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                    Storage::disk('public')->delete($photo->photo_path);
                }
                $photo->delete();
            }

            // Delete health record
            if ($pet->health) {
                $pet->health()->delete();
            }

            // Delete behavioral traits
            \App\Models\PetBehaviorTraits::where('pet_id', $pet->id)->delete();

            // Delete pet
            $pet->delete();

            DB::commit();
            return redirect()->route('pets.myPets')->with('success', 'Pet listing deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

}