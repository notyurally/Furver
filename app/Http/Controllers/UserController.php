<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pet;
use App\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function homepage()
    {
        // Get featured pets (available status, latest 8 pets)
        $featuredPets = Pet::with(['breed.petTypes', 'photos'])
            ->where('pet_status', 'available')
            ->latest()
            ->get();

        return view('User.homepage', compact('featuredPets'));
    }

    public function adoptlist()
    {
        $petlist = Pet::with(['breed.petTypes', 'photos'])
            ->where('pet_status', 'available')
            ->latest()
            ->get();

        $totalPetsCount = $petlist->count();
        return view('User.adoptlist', compact('petlist'));

        // Apply filters if present in request
        if ($request->filled('species') && $request->species !== 'All') {
            $petlisteHas('breed.petTypes', function($q) use ($request) {
                $q->where('name', $request->species);
            });
        }

        if ($request->filled('breed') && $request->breed !== 'All') {
            $petliste('breed_id', $request->breed);
        }

        if ($request->filled('size') && $request->size !== 'All') {
            $petliste('pet_size', $request->size);
        }

        if ($request->filled('location')) {
            $petliste('location', 'LIKE', '%' . $request->location . '%');
        }

        $petTypes = PetTypes::all();

         $breeds = Breed::all();

        return view('User.adoptList', compact('petlist', 'petTypes', 'breeds'));
    }
}
