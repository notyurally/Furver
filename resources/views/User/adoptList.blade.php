<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Adoptable Pets - FurEver</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        [data-lucide] {
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .pet-card-hover:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 10px 20px rgba(160, 82, 45, 0.15);
        }
        .filter-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23d97706' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.5em 1.5em;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white shadow-lg sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('homepage') }}" class="flex-shrink-0 flex items-center cursor-pointer transition-opacity hover:opacity-80">
                <i data-lucide="paw-print" class="h-8 w-8 text-amber-500"></i>
                <span class="text-2xl font-bold text-gray-900 ml-2">FurEver</span>
            </a>

            <div class="hidden sm:ml-6 sm:flex sm:space-x-5">
                <a href="{{ route('homepage')}}" class="text-gray-700 hover:text-orange-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Home Page</a>               
                <a href="#" class="flex items-center px-2 py-1.5 text-sm font-semibold transition-all duration-150 
                           text-orange-600 border-b-2 border-orange-600 -mb-0.5">Adoptable Pets</a>
            </div>

            <div class="flex items-center">
                @auth
                    <span class="text-gray-700 text-sm mr-3">Hi, {{ Auth::user()->name }}!</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-orange-600 px-4 py-2 text-sm font-medium transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4 inline-block mr-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-600 px-4 py-2 text-sm font-medium transition-colors">
                        <i data-lucide="user" class="w-4 h-4 inline-block mr-1"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900 mb-8">
            Find & adopt
        </h1>
        
        <!-- Filters -->
        <div class="mb-6 p-6 bg-white rounded-2xl shadow-xl border border-orange-50">
            <div class="flex flex-wrap items-end gap-4 lg:gap-6">
                
                <div class="flex-1 min-w-[200px] sm:min-w-[250px] w-full sm:w-auto">
                    <label for="search-bar" class="block text-sm font-semibold text-gray-700 mb-2">Search Name or Location</label>
                    <div class="relative">
                        <input type="text" id="search-bar" placeholder="Name, Breed, or Location..." 
                               oninput="filterPets()"
                               class="w-full p-3 pl-10 border border-orange-200 rounded-xl bg-white focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-sm h-[48px]">
                        <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-orange-400"></i>
                    </div>
                </div>

                <div class="min-w-[150px] w-full sm:w-auto flex-1">
                    <label for="type-filter" class="block text-sm font-semibold text-gray-700 mb-2">Animal Type</label>
                    <select id="type-filter" onchange="filterPets()" class="filter-select w-full p-3 border border-orange-200 rounded-xl bg-white focus:ring-orange-400 focus:border-orange-400 transition duration-150 shadow-sm h-[48px]">
                        <option value="all">All Types</option>
                        <option value="DOG">Dog</option>
                        <option value="CAT">Cat</option>
                        <option value="RAB">Rabbit</option>
                    </select>
                </div>

                <div class="min-w-[150px] w-full sm:w-auto flex-1">
                    <label for="breed-filter" class="block text-sm font-semibold text-gray-700 mb-2">Breed Type</label>
                    <select id="breed-filter" onchange="filterPets()" class="filter-select w-full p-3 border border-orange-200 rounded-xl bg-white focus:ring-orange-400 focus:border-orange-400 transition duration-150 shadow-sm h-[48px]">
                        <option value="all">All Breeds</option>
                        <option value="TMIX">Terrier Mix</option>
                        <option value="LAB">Labrador</option>
                        <option value="CAL">Calico</option>
                        <option value="DSH">Domestic Shorthair</option>
                        <option value="LH">Lionhead</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[150px] w-full sm:w-auto">
                    <label for="size-filter" class="block text-sm font-semibold text-gray-700 mb-2">Size</label>
                    <select id="size-filter" onchange="filterPets()" class="filter-select w-full p-3 border border-orange-200 rounded-xl bg-white focus:ring-orange-400 focus:border-orange-400 transition duration-150 shadow-sm h-[48px]">
                        <option value="All">All Sizes</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                        <option value="Extra Large">Extra Large</option>
                    </select>
                </div>

                <button onclick="filterPets()" class="w-full sm:w-auto h-[48px] px-8 bg-amber-600 text-white rounded-xl font-bold hover:bg-amber-700 transition duration-150 shadow-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                    Find a friend
                </button>
            </div>
        </div>
        
        <div id="reset-container" class="mt-4 hidden md:block text-right">
            <button onclick="clearFilters()" class="text-sm font-medium text-gray-500 hover:text-red-500 transition-colors flex items-center ml-auto">
                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1"></i> Reset Filters
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        <span class="font-semibold">Success!</span> {{ session('success') }}
    </div>
    @endif

    <div>
        <h2 id="pet-count-header" class="text-2xl font-bold text-gray-800 mb-6">Showing <span id="total-pets">{{ $petlist->count() }}</span> Adoptable Pets</h2>
        
        <div id="pet-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse ($petlist as $pet)
                @php
                    $profilePhoto = $pet->photos()->where('is_profile', true)->first();
                    $petIcon = match($pet->breed->petTypes->name ?? 'Unknown') {
                        'Dog' => 'dog',
                        'Cat' => 'cat',
                        'Rabbit' => 'rabbit',
                        'Bird' => 'bird',
                        default => 'heart'
                    };
                    $statusColor = match($pet->pet_status) {
                        'available' => 'bg-green-500',
                        'pending' => 'bg-amber-500',
                        'adopted' => 'bg-gray-500',
                        default => 'bg-gray-500'
                    };
                    
                    $speciesCode = $pet->breed->petTypes->code ?? 'UNK';
                    $breedCode = $pet->breed->code ?? 'UNK';
                    $sizeData = $pet->pet_size ?? 'Unknown';
                    $petName = strtolower($pet->name);
                    $locationData = strtolower($pet->location ?? '');
                @endphp

                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden transform transition duration-300 pet-card-hover cursor-pointer border-t-4 border-orange-100 pet-card pet-item" 
                    data-id="{{ $pet->id }}"
                    data-species="{{ $speciesCode }}" 
                    data-breed="{{ $breedCode }}"
                    data-size="{{ $sizeData }}"
                    data-name="{{ $petName }}"
                    data-location="{{ $locationData }}">
                    
                    <div class="relative h-48 bg-gray-200">
                        @if($profilePhoto)
                            <img src="{{ asset('storage/' . $profilePhoto->photo_path) }}" 
                                alt="Profile image of {{ $pet->name }}" 
                                class="w-full h-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                onerror="this.src='https://placehold.co/400x300/D1D5DB/6B7280?text={{ urlencode($pet->name) }}';">
                        @else
                            <img src="https://placehold.co/400x300/D1D5DB/6B7280?text={{ urlencode($pet->name) }}" 
                                alt="Profile image of {{ $pet->name }}" 
                                class="w-full h-full object-cover">
                        @endif
                        
                        <div class="absolute top-3 right-3 px-3 py-1 text-xs font-bold text-white rounded-full shadow-lg {{ $statusColor }} transform transition duration-300">
                            {{ ucfirst($pet->pet_status) }}
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h2 class="text-xl font-extrabold text-gray-900 truncate">{{ $pet->name }}</h2>
                            <span class="text-xs font-bold text-orange-700 bg-orange-100 px-3 py-1 rounded-full whitespace-nowrap shadow-md">
                                ${{ number_format($pet->adoption_fee, 2) }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-600 space-y-2 mb-4">
                            <p class="flex items-center">
                                <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-orange-400"></i>
                                {{ $pet->location ?? 'Location not specified' }}
                            </p>
                            <p class="flex items-center">
                                <i data-lucide="clock" class="w-4 h-4 mr-2 text-orange-400"></i>
                                {{ $pet->age }} {{ $pet->age_data ?? 'years' }} old
                            </p>
                            <p class="flex items-center">
                                <i data-lucide="{{ $petIcon }}" class="w-4 h-4 mr-2 text-orange-400"></i>
                                {{ $pet->breed->name ?? 'Mixed' }} • {{ $pet->breed->petTypes->name ?? 'Unknown' }}
                            </p>
                        </div>

                        <p class="text-sm text-gray-500 h-12 overflow-hidden line-clamp-2 mb-4">
                            {{ Str::limit($pet->description, 80) }}
                        </p>
                        
                        <div class="mt-2 flex space-x-2">
                            @if($pet->pet_status === 'available')
                                @auth
                                    <button onclick="showAdoptionModal({{ $pet->id }}, '{{ addslashes($pet->name) }}')"
                                        class="flex-1 py-1 px-3 bg-orange-600 text-white font-semibold rounded-md shadow-md hover:bg-amber-700 transition duration-200 text-xs text-center">
                                        <i data-lucide="heart" class="w-3 h-3 inline-block mr-1"></i> Adopt
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" 
                                        class="flex-1 py-1 px-3 bg-orange-600 text-white font-semibold rounded-md shadow-md hover:bg-amber-700 transition duration-200 text-xs text-center">
                                        <i data-lucide="heart" class="w-3 h-3 inline-block mr-1"></i> Login to Adopt
                                    </a>
                                @endauth
                            @else
                                <button disabled
                                    class="flex-1 py-1 px-3 bg-gray-400 text-white font-semibold rounded-md shadow-md cursor-not-allowed text-xs text-center">
                                    <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i> {{ ucfirst($pet->pet_status) }}
                                </button>
                            @endif
                            <a href="#" class="flex-1 py-1 px-3 bg-white text-amber-600 border border-orange-600 font-semibold rounded-md shadow-sm hover:bg-orange-50 transition duration-200 text-xs text-center">
                                <i data-lucide="eye" class="w-3 h-3 inline-block mr-1"></i> View More
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-xl text-gray-500 p-12 bg-white rounded-2xl shadow-xl mt-8 border-t-4 border-amber-400">
                    <i data-lucide="search-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p>No furry friends available at the moment. Please check back later!</p>
                </div>
            @endforelse
        </div>

        <div id="no-pets-message" class="hidden text-center text-xl text-gray-500 p-12 bg-white rounded-2xl shadow-xl mt-8 border-t-4 border-amber-400">
            <i data-lucide="search-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p>No furry friends match your criteria. Please adjust your filters!</p>
        </div>
    </div>
</main>

<!-- Adoption Application Modal (Popup Form) -->
@auth
<div id="adoption-modal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all max-h-[90vh] overflow-y-auto relative">
        <button type="button" onclick="hideModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        
        <h3 class="text-3xl font-extrabold text-gray-900 mb-4 flex items-center border-b pb-3">
            <i data-lucide="home" class="w-7 h-7 mr-3 text-amber-600"></i> 
            Apply to Adopt <span class="font-bold text-amber-700 ml-2" id="pet-name-display"></span>
        </h3>
        
        <form id="adoption-form" onsubmit="submitAdoptionForm(event)">
            @csrf
            
            <input type="hidden" id="pet-id-input" name="pet_id" value="">
            
            <p class="text-gray-600 mb-6 text-sm">
                Please provide your contact information and tell us why you'd be a perfect home.
            </p>

            <div class="mb-4">
                <label for="mobile-number" class="block text-sm font-medium text-gray-700 mb-2">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input type="tel" id="mobile-number" name="mobile_number" required 
                        placeholder="e.g., 555-123-4567"
                        class="w-full p-3 border border-orange-200 rounded-lg bg-white focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-sm">
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Address <span class="text-red-500">*</span>
                </label>
                <input type="text" id="address" name="address" required 
                        placeholder="Street Address, City, Postal Code"
                        class="w-full p-3 border border-orange-200 rounded-lg bg-white focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-sm">
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Why do you want to adopt this pet? <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" required rows="4" 
                            placeholder="Tell us about your home, family, and experience with pets."
                            class="w-full p-3 border border-orange-200 rounded-lg bg-white focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-sm"></textarea>
            </div>
            
            <div id="form-message" class="mb-4 p-3 rounded-lg hidden">
                <span id="form-message-text"></span>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="hideModal()" class="py-2 px-4 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-150">
                    Cancel
                </button>
                <button type="submit" id="submit-btn" class="py-2 px-6 bg-amber-600 text-white font-semibold rounded-lg shadow-lg hover:bg-amber-700 transition duration-150 flex items-center">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
@endauth

<footer class="bg-gray-800 text-white py-12 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-amber-100">
        <p>&copy; 2025 FurEver. All rights reserved.</p>
    </div>
</footer>

<script>
    let petItems = [];
    let currentPetId = null;

    function collectPetItems() {
        petItems = Array.from(document.querySelectorAll('.pet-item'));
    }

    function filterPets() {
        const typeFilter = document.getElementById('type-filter').value;
        const breedFilter = document.getElementById('breed-filter').value;
        const sizeFilter = document.getElementById('size-filter').value;
        const searchTerm = document.getElementById('search-bar').value.toLowerCase().trim();
        const noPetsMessage = document.getElementById('no-pets-message');
        const resetContainer = document.getElementById('reset-container');
        const petCountHeader = document.getElementById('total-pets');
        let visibleCount = 0;
        let activeFilters = false;

        if (typeFilter !== 'all' || breedFilter !== 'all' || sizeFilter !== 'All' || searchTerm !== '') {
            activeFilters = true;
        }

        petItems.forEach(card => {
            const petSpecies = card.getAttribute('data-species');
            const petBreed = card.getAttribute('data-breed');
            const petSize = card.getAttribute('data-size');
            const petName = card.getAttribute('data-name');
            const petLocation = card.getAttribute('data-location');

            const matchesType = (typeFilter === 'all' || petSpecies === typeFilter);
            const matchesBreed = (breedFilter === 'all' || petBreed === breedFilter);
            const matchesSize = (sizeFilter === 'All' || petSize === sizeFilter);
            const matchesSearch = (
                searchTerm === '' ||
                petName.includes(searchTerm) ||
                petLocation.includes(searchTerm)
            );

            const isVisible = matchesType && matchesBreed && matchesSize && matchesSearch;
            card.classList.toggle('hidden', !isVisible);
            
            if (isVisible) visibleCount++;
        });

        petCountHeader.textContent = visibleCount;
        noPetsMessage.classList.toggle('hidden', visibleCount > 0);
        if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function clearFilters() {
    document.getElementById('type-filter').value = 'all';
    document.getElementById('breed-filter').value = 'all';
    document.getElementById('size-filter').value = 'All';
    document.getElementById('search-bar').value = '';
    filterPets(); 
}

function showAdoptionModal(petId, petName) {
    currentPetId = petId;
    document.getElementById('pet-id-input').value = petId;
    document.getElementById('pet-name-display').textContent = petName;
    document.getElementById('adoption-form').reset();
    document.getElementById('pet-id-input').value = petId;
    document.getElementById('form-message').classList.add('hidden');
    document.getElementById('adoption-modal').classList.remove('hidden');
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function hideModal() {
    document.getElementById('adoption-modal').classList.add('hidden');
    currentPetId = null;
}

async function submitAdoptionForm(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submit-btn');
    const messageDiv = document.getElementById('form-message');
    const messageText = document.getElementById('form-message-text');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Submitting...';
    
    try {
        const response = await fetch('{{ route("adoption.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.className = 'mb-4 p-3 rounded-lg bg-green-100 border border-green-400 text-green-700';
            messageText.textContent = data.message;
            messageDiv.classList.remove('hidden');
            
            const petCard = document.querySelector(`[data-id="${currentPetId}"]`);
            if (petCard) {
                const statusBadge = petCard.querySelector('.absolute.top-3.right-3');
                if (statusBadge) {
                    statusBadge.classList.remove('bg-green-500');
                    statusBadge.classList.add('bg-amber-500');
                    statusBadge.textContent = 'Pending';
                }
                
                const adoptBtn = petCard.querySelector('button[onclick*="showAdoptionModal"]');
                if (adoptBtn) {
                    adoptBtn.outerHTML = `
                        <button disabled class="flex-1 py-1 px-3 bg-gray-400 text-white font-semibold rounded-md shadow-md cursor-not-allowed text-xs text-center">
                            <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i> Pending
                        </button>
                    `;
                }
            }
            
            setTimeout(() => {
                hideModal();
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 2000);
            
        } else {
            messageDiv.className = 'mb-4 p-3 rounded-lg bg-red-100 border border-red-400 text-red-700';
            messageText.textContent = data.message || 'Failed to submit application.';
            messageDiv.classList.remove('hidden');
        }
        
    } catch (error) {
        console.error('Error:', error);
        messageDiv.className = 'mb-4 p-3 rounded-lg bg-red-100 border border-red-400 text-red-700';
        messageText.textContent = 'An error occurred. Please try again.';
        messageDiv.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i data-lucide="send" class="w-4 h-4 mr-2"></i> Submit Application';
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

window.onload = function() {
    collectPetItems();
    filterPets();
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
};
</script>
</body>
</html>