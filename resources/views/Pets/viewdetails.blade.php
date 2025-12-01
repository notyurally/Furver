<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pet->name }} - Pet Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pet-orange': '#f97316',
                        'pet-blue': '#3B82F6',
                        'pet-dark': '#212529',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
        
</head>
<body class="bg-[#FBF3D5] font-sans">
    
    <!-- Navbar -->
    <nav class="sticky top-0 z-10 w-full bg-white shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-2xl font-extrabold text-pet-dark flex items-center">
                <i class="bi bi-paw-fill text-pet-orange mr-2"></i> FurEver
            </a>
            <div class="hidden sm:flex items-center space-x-6 text-gray-600 font-medium">
                <a class="hover:text-pet-orange transition duration-150" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="text-pet-orange border-b-2 border-pet-orange pb-1" href="{{ route('pets.myPets') }}">My Pets</a>
                <a class="hidden sm:inline hover:text-pet-orange transition duration-150" href="{{ route('user.applications') }}">Application List</a>
                <a class="hover:text-pet-orange transition duration-150" href="#"><i class="bi bi-bell-fill text-lg"></i></a>
                <a class="hover:text-pet-orange transition duration-150" href="#"><i class="bi bi-gear-fill text-lg"></i></a>
                <a class="w-8 h-8 rounded-full overflow-hidden border-2 border-gray-300 hover:border-pet-orange transition duration-150 bg-pet-orange/20 flex items-center justify-center text-sm font-bold text-pet-dark" href="#">
                    {{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
                </a>
            </div>
            <button class="sm:hidden text-gray-600 hover:text-pet-orange transition duration-150">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 lg:py-12">
        <div class="max-w-6xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-2xl">
            <!-- Back Button -->
            <a href="{{ url()->previous() }}" title="Go Back" 
                class="text-3xl text-gray-500 hover:text-pet-orange transition duration-150">
                <i class="bi bi-arrow-left-circle-fill"></i>
            </a>
            
            <div class="lg:flex lg:space-x-10">
                <div class="lg:w-7/12 mt-2">
                    <!-- Main Image -->
                    <div class="w-full aspect-square md:aspect-[3/2] overflow-hidden rounded-xl shadow-2xl mb-4 bg-pet-blue flex items-center justify-center">
                        @if($pet->profilePhoto)
                            <img src="{{ asset('storage/' . $pet->profilePhoto->photo_path) }}" 
                                alt="{{ $pet->name }}" 
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-white text-xl\'>Image Unavailable</span>';">
                        @else
                            <span class="text-white text-center text-xl">No image available</span>
                        @endif
                    </div>

                    <!-- Additional Photos -->
                    <div class="grid grid-cols-3 gap-3 mb-6">
                        @php
                            $additionalPhotos = $pet->photos()->where('is_profile', false)->take(2)->get();
                            $remainingCount = $pet->photos()->where('is_profile', false)->count() - 2;
                        @endphp
                        
                        @foreach($additionalPhotos as $photo)
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                class="w-full h-32 object-cover rounded-lg shadow-md" 
                                alt="Pet photo"
                                onerror="this.src='https://placehold.co/150x120/f97316/ffffff?text=Photo'">
                        @endforeach
                        
                        @if($remainingCount > 0)
                            <div class="w-full h-32 rounded-lg shadow-inner border border-gray-300 bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-500">
                                + {{ $remainingCount }} More {{ Str::plural('Photo', $remainingCount) }}
                            </div>
                        @elseif($additionalPhotos->count() == 0)
                            <div class="col-span-3 w-full h-32 rounded-lg shadow-inner border border-gray-300 bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-500">
                                No additional photos
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <h2 class="text-2xl font-bold text-pet-dark mb-3 mt-6 border-b pb-2">
                        About {{ $pet->name }}
                    </h2>
                    <p class="text-gray-600 leading-relaxed text-base bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-inner">
                        {{ $pet->description ?? 'No description provided for this pet.' }}
                    </p>
                    
                    <!-- Health Status & Behavioral Traits -->
                    <div class="mt-8 space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-pet-dark mb-4 flex items-center">
                                <i class="bi bi-hospital-fill text-red-500 mr-3 text-2xl"></i> Health & Wellness
                            </h3>
                            <div class="text-gray-700 leading-relaxed bg-red-50 p-4 rounded-xl border border-red-200 text-sm space-y-1 shadow-md">
                                @if($pet->health)
                                    <div><strong>Vaccinated:</strong> {{ $pet->health->is_vaccinated ? 'Yes' : 'No' }}</div>
                                    @if($pet->health->is_vaccinated && $pet->health->last_vaccinated_date)
                                        <div><strong>Last Vaccination:</strong> {{ $pet->health->last_vaccinated_date->format('M d, Y') }}</div>
                                    @endif
                                    <div><strong>Spayed/Neutered:</strong> {{ $pet->health->is_spayed ? 'Yes' : 'No' }}</div>
                                    @if($pet->health->microchip_number)
                                        <div><strong>Microchip:</strong> {{ $pet->health->microchip_number }}</div>
                                    @endif
                                @else
                                    <div class="text-gray-500">No health information available.</div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-pet-dark mb-4 flex items-center">
                                <i class="bi bi-people-fill text-blue-500 mr-3 text-2xl"></i> Behavioral Traits
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @forelse($pet->behaviorTraits as $trait)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium shadow-sm">
                                        {{ $trait->trait }}
                                    </span>
                                @empty
                                    <p class="text-gray-500 text-sm">No specific behavioral traits listed.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-5/12 mt-6 lg:mt-0">
                    <!-- Pet Name and Status -->
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-4xl font-extrabold text-pet-dark leading-tight">{{ $pet->name }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-lg 
                            {{ $pet->pet_status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $pet->pet_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $pet->pet_status === 'adopted' ? 'bg-red-100 text-red-800' : '' }}">
                            <i class="bi bi-check-circle-fill mr-1"></i> {{ ucfirst($pet->pet_status) }}
                        </span>
                    </div>

                    <!-- Adoption Fee -->
                    <div class="mb-6 border-b pb-4">
                        <p class="text-sm font-medium text-gray-500 uppercase">Adoption Fee</p>
                        <span class="text-4xl font-extrabold text-pet-orange">
                            ₱{{ number_format($pet->adoption_fee, 2) }}
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-3 mb-6">
                        <a href="{{ route('pets.edit', $pet->id) }}" 
                            class="flex items-center justify-center px-6 py-3 bg-pet-orange text-white text-lg font-semibold rounded-xl shadow-lg hover:bg-orange-600 transition duration-300 transform hover:scale-[1.01]">
                            <i class="bi bi-pencil-square mr-3"></i> Edit Listing Details
                        </a>
                        <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pet listing?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="w-full flex items-center justify-center px-6 py-3 bg-red-50 text-red-700 text-lg font-semibold rounded-xl border-2 border-red-300 hover:bg-red-100 transition duration-300">
                                <i class="bi bi-trash-fill mr-3"></i> Delete Listing
                            </button>
                        </form>
                    </div>

                    <!-- Listing Metrics -->
                    <h3 class="text-xl font-bold text-pet-dark mb-4 mt-6">Listing Activity</h3>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <a href="#"class="metric-card">
                            <p class="metric-value">{{ $pet->applications->count() }}</p>
                            <p class="metric-label">Applications</p>
                        </a>
                        <div class="metric-card">
                            <p class="metric-value text-xl">{{ $pet->created_at->format('M d, Y') }}</p>
                            <p class="metric-label">Date Listed</p>
                        </div>
                    </div>

                    <!-- Pet Profile -->
                    <h3 class="text-xl font-bold text-pet-dark mb-4 border-t pt-6">Pet Profile</h3>
                    <div class="grid grid-cols-2 gap-y-5 gap-x-4">
                        
                        <div class="flex items-start">
                            <i class="bi bi-geo-alt-fill paw-icon text-pet-orange mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Location</p>
                                <p class="text-base font-semibold text-pet-dark">{{ $pet->location }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="bi bi-person-fill paw-icon mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Breed</p>
                                <p class="text-base font-semibold text-pet-dark">{{ $pet->breed->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="bi bi-clock-fill paw-icon mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Age</p>
                                <p class="text-base font-semibold text-pet-dark">
                                    @if($pet->birthdate)
                                        {{ $pet->birthdate->age }} {{ Str::plural('year', $pet->birthdate->age) }}
                                    @elseif($pet->age)
                                        {{ $pet->age }} {{ $pet->age_data ?? 'years' }}
                                    @else
                                        Not specified
                                    @endif
                                    old
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="bi bi-gender-{{ $pet->pet_sex === 'male' ? 'male' : 'female' }} paw-icon {{ $pet->pet_sex === 'male' ? 'text-blue-600' : 'text-pink-600' }} mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Gender</p>
                                <p class="text-base font-semibold text-pet-dark capitalize">{{ $pet->pet_sex }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="bi bi-arrows-expand paw-icon mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Size</p>
                                <p class="text-base font-semibold text-pet-dark capitalize">{{ str_replace('_', ' ', $pet->pet_size) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="bi bi-calendar-event paw-icon mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase">Birthdate</p>
                                <p class="text-base font-semibold text-pet-dark">
                                    {{ $pet->birthdate ? $pet->birthdate->format('M d, Y') : 'Not specified' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Listed By (Owner Info) -->
                    <h3 class="text-xl font-bold text-pet-dark mb-4 mt-8 border-t pt-6">Listed By</h3>
                    <div class="border p-4 rounded-xl bg-gray-50 shadow-md">
                        <div class="flex items-center space-x-3 mb-3 border-b pb-3">
                            <div class="w-10 h-10 rounded-full bg-pet-blue flex items-center justify-center text-white font-bold text-lg shadow-inner">
                                {{ strtoupper(substr($pet->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-pet-dark">{{ $pet->user->name ?? 'Unknown User' }}</p>
                                <p class="text-sm text-gray-500">Owner / Shelter Representative</p>
                            </div>
                        </div>
                        <div class="text-sm space-y-1">
                            <p class="flex items-center text-gray-700">
                                <i class="bi bi-envelope-fill text-pet-orange mr-2"></i> 
                                {{ $pet->user->email ?? 'Not provided' }}
                            </p>
                            @if($pet->user->phone ?? false)
                                <p class="flex items-center text-gray-700">
                                    <i class="bi bi-telephone-fill text-pet-orange mr-2"></i> 
                                    {{ $pet->user->phone }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>