<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-orange': '#F97316',
                        'light-bg': '#FFF7ED',
                        'card-bg': '#FFFFFF',
                        'pet-orange': '#f97316', 
                        'pet-dark': '#1f2937', 
                        'pet-bg': '#fefcf6', 
                        'pet-light-gray': '#f3f4f6', 
                        'pet-border': '#e5e7eb', 
                        'pet-accent-purple': '#8b5cf6',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="font-sans bg-[#FBF3D5] min-h-screen flex">
    <nav class="fixed w-full top-0 bg-white shadow-md z-10 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="#" class="text-2xl font-extrabold text-pet-dark flex items-center">
                <i class="bi bi-paw-fill text-pet-orange mr-2"></i>FurEver
            </a>
            <div class="flex items-center space-x-6 text-gray-600 font-medium">
                <a class="hidden sm:inline text-pet-orange border-b-2 border-pet-orange font-bold pb-1" href="#">Dashboard</a>
                <a class="hidden sm:inline hover:text-pet-orange transition duration-150" href="{{ route('pets.myPets') }}">My Pets</a>
                <a class="hidden sm:inline hover:text-pet-orange transition duration-150" href="{{ route('user.applications') }}">Application List</a>
                <a class="hover:text-pet-orange transition duration-150" href="#"><i class="bi bi-bell-fill text-lg"></i></a>
                <a class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-300 hover:border-pet-orange transition duration-150 bg-orange-100 flex items-center justify-center text-sm font-bold text-pet-orange" href="#">
                    {{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
                </a>
            </div>
            <button class="sm:hidden text-gray-600 hover:text-pet-orange transition duration-150">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
    </nav>

    <main class="flex-1 p-4 md:p-8 overflow-y-auto mt-20">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Pet Inventory Dashboard</h2>
            <p class="text-gray-500">Overview of your pets and adoption status.</p>
        </header>

        <div class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between p-2 bg-card-bg rounded-xl shadow-md border border-gray-100">
            <h3 class="text-xs font-semibold text-gray-700 md:mb-0">Filter Data by Date:</h3>
            <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2 md:w-auto">
                <input type="date" id="start-date" value="{{ \Carbon\Carbon::now()->subMonths(3)->toDateString() }}"
                       class="rounded-xl text-xs p-2 border border-gray-300 focus:ring-primary-orange focus:border-primary-orange w-full sm:w-auto">
                <input type="date" id="end-date" value="{{ \Carbon\Carbon::now()->toDateString() }}"
                       class="text-xs rounded-xl p-2 border border-gray-300 focus:ring-primary-orange focus:border-primary-orange w-full sm:w-auto">
                <button onclick="filterPets(true)" class="text-xs bg-primary-orange text-white p-2 rounded-lg hover:bg-orange-700 transition duration-150 shadow-md">Apply Filter</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
            <div class="bg-card-bg p-6 rounded-xl shadow-lg border-t-4 border-primary-orange transition hover:shadow-xl">
                <p class="text-sm font-medium text-gray-500 mb-2">Total Pets</p>
                <p id="total-pets" class="text-4xl font-extrabold text-gray-800">{{ $totalPetsCount }}</p>
                <div class="flex items-center text-sm mt-3 text-orange-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M12 10a5 5 0 00-3.536 8.536m3.536-8.536V3"></path></svg>
                    All Types
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-xl shadow-lg border-t-4 border-orange-400 transition hover:shadow-xl">
                <p class="text-sm font-medium text-gray-500 mb-2">Total Dogs</p>
                <p id="total-dogs" class="text-4xl font-extrabold text-gray-800">{{ $totalDogsCount }}</p>
                <div class="flex items-center text-sm mt-3 text-orange-400">
                    <i class="bi bi-heart-fill mr-1"></i>
                    Canine Inventory
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-purple-500 transition hover:shadow-xl">
                <p class="text-sm font-medium text-gray-500 mb-2">Total Cats</p>
                <p id="total-cats" class="text-4xl font-extrabold text-purple-600">{{ $totalCatsCount }}</p>
                <div class="flex items-center text-sm mt-3 text-purple-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 2m0 0l-2-2m2 2v2M20 12h-2m-8 0H4M7 16l-1 4m12 0l-1-4M9 4h6a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2z"></path></svg>
                    Feline Inventory
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-xl shadow-lg border-t-4 border-green-500 transition hover:shadow-xl">
                <p class="text-sm font-medium text-gray-500 mb-2">Adopted (Success)</p>
                <p id="adopted-pets" class="text-4xl font-extrabold text-green-600">{{ $adoptedPetsCount }}</p>
                <div class="flex items-center text-sm mt-3 text-green-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Successfully Placed
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-xl shadow-lg border-t-4 border-blue-500 transition hover:shadow-xl">
                <p class="text-sm font-medium text-gray-500 mb-2">Available for Adoption</p>
                <p id="available-pets" class="text-4xl font-extrabold text-blue-600">{{ $availablePetsCount }}</p>
                <div class="flex items-center text-sm mt-3 text-blue-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ready for a Home
                </div>
            </div>
        </div>

        <div class="bg-card-bg p-6 rounded-xl shadow-lg border border-gray-100">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 border-b pb-4">
                <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Pet Details</h3>
                
                <div class="flex flex-wrap gap-4 w-full md:w-auto">
                    <!-- Search Bar -->
                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-2 px-2.5 pr-3 h-10 focus-within:ring-1 focus-within:ring-pet-accent-purple focus-within:border-pet-accent-purple transition">
                        <i class="bi bi-search text-gray-400 text-xs mr-1.5"></i>
                        <input type="text" id="search-bar" placeholder="Search name, breed..." class="flex-1 text-sm bg-transparent text-gray-700 placeholder-gray-400 focus:outline-none"/>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <select id="type-filter" onchange="filterPets(false)"
                                class="p-2 border border-gray-300 rounded-lg focus:ring-primary-orange focus:border-primary-orange w-32 text-sm">
                            <option value="all">All Types</option>
                            @foreach ($petTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select id="status-filter" onchange="filterPets(false)"
                                class="p-2 border border-gray-300 rounded-lg focus:ring-primary-orange focus:border-primary-orange w-32 text-sm">
                            <option value="all">All Statuses</option>
                            <option value="available">Available</option>
                            <option value="pending">Pending</option>
                            <option value="adopted">Adopted</option>
                        </select>
                    </div>

                    <!-- Breed Filter -->
                    <div>
                        <select id="breed-filter" onchange="filterPets(false)"
                                class="p-2 border border-gray-300 rounded-lg focus:ring-primary-orange focus:border-primary-orange w-32 text-sm">
                            <option value="all">All Breeds</option>
                            @foreach ($breeds as $breed)
                                <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Header (Desktop) -->
            <div class="hidden md:grid grid-cols-[60px_2.5fr_1.5fr_1.5fr_1.5fr_2fr_2fr_80px] gap-4 items-center px-4 py-3 bg-gray-50 rounded-lg mb-4 font-semibold text-sm text-gray-700">
                <div>Photo</div>
                <div>Name / Age</div>
                <div>Breed</div>
                <div>Type</div>
                <div>Status</div>
                <div>Listed Date</div>
                <div>Actions</div>
                <div>Applications</div>
            </div>

            <div id="pet-list" class="space-y-4">
                @forelse ($pets as $pet)
                    @php
                        // Get health status from health relationship
                        $health = $pet->health && $pet->health->is_vaccinated ? 'Healthy' : 'Special Needs';
                        $intakeDateFormatted = $pet->created_at->format('M d, Y');
                        
                        // Status styling
                        $statusClasses = match($pet->pet_status) {
                            'adopted' => 'text-green-700 bg-green-100 ring-green-500',
                            'pending' => 'text-yellow-700 bg-yellow-100 ring-yellow-500',
                            default => 'text-blue-700 bg-blue-100 ring-blue-500'
                        };
                        
                        // Health styling
                        $healthClasses = $health === 'Healthy' 
                            ? 'text-green-700 bg-green-50 ring-green-500' 
                            : 'text-red-700 bg-red-50 ring-red-500';
                        
                        $profilePhoto = $pet->profilePhoto?->photo_path ?? 'pets/default.png';
                        
                        $ageDisplay = $pet->age ? $pet->age . ' ' . ($pet->age_data ?? 'years') : 'Unknown';
                        
                        $petTypeName = $pet->breed->petTypes->name ?? 'Unknown';
                        $petTypeId = $pet->breed->petTypes->id ?? '';
                    @endphp

                    <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition duration-150 border border-gray-100 md:grid grid-cols-[60px_2.5fr_1.5fr_1.5fr_1.5fr_2fr_2fr_80px] gap-4 items-center pet-item"
                         data-name="{{ strtolower($pet->name) }}"
                         data-breed="{{ $pet->breed_id }}"
                         data-breedname="{{ strtolower($pet->breed->name ?? '') }}"
                         data-type="{{ $petTypeId }}"
                         data-typename="{{ strtolower($petTypeName) }}"
                         data-status="{{ $pet->pet_status }}"
                         data-dateadded="{{ $pet->created_at->toDateString() }}">
                        
                        <!-- Picture -->
                        <div class="flex-shrink-0 mb-3 md:mb-0">
                            <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                 alt="{{ $pet->name }}" 
                                 class="w-10 h-10 object-cover rounded-full" 
                                 onerror="this.src='https://via.placeholder.com/40?text={{ substr($pet->name, 0, 1) }}'">
                        </div>

                        <!-- Name / Age -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500">Name / Age</p>
                            <p class="font-semibold text-gray-900">{{ $pet->name }}</p>
                            <span class="text-xs text-gray-500">{{ $ageDisplay }}</span>
                        </div>

                        <!-- Breed -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Breed</p>
                            <p class="text-gray-600">{{ $pet->breed->name ?? 'Unknown' }}</p>
                        </div>
                        
                        <!-- Pet Type -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Type</p>
                            <span class="text-sm font-medium text-orange-600">
                                @if(strtolower($petTypeName) === 'dog')
                                    🐕 Dog
                                @elseif(strtolower($petTypeName) === 'cat')
                                    🐾 Cat
                                @else
                                    {{ $petTypeName }}
                                @endif
                            </span>
                        </div>

                        <!-- Status -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Status</p>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
                                {{ ucfirst($pet->pet_status) }}
                            </span>
                        </div>

                        
                        <!-- Intake Date -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Listed Date</p>
                            <p class="text-sm text-gray-500">{{ $intakeDateFormatted }}</p>
                        </div>

                        <!-- Actions -->
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Actions</p>
                            <a href="{{ route('pets.show', $pet->id) }}" 
                               class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                                <i class="bi bi-eye mr-1"></i> View
                            </a>
                        </div>
                        <div>
                            <p class="md:hidden text-xs font-medium text-gray-500 mt-2">Applications</p>
                            <a href="{{ route('pets.show', $pet->id) }}" 
                               class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                                <i class="bi bi-eye mr-1"></i> Applications
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 p-8">
                        No pets found in the database.
                    </div>
                @endforelse
            </div>

            <div id="no-pets-message" class="text-center text-gray-500 p-8 hidden">
                No pets match the current filters.
            </div>
        </div>
    </main>

    <script>
        const petItems = Array.from(document.querySelectorAll('.pet-item'));

        const filterPets = (isDateFilter = false) => {
            const breedFilter = document.getElementById('breed-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const searchBar = document.getElementById('search-bar');
            const searchTerm = searchBar ? searchBar.value.toLowerCase().trim() : '';

            const startDate = new Date(document.getElementById('start-date').value);
            const endDate = new Date(document.getElementById('end-date').value);
            endDate.setHours(23, 59, 59, 999);

            let visibleCount = 0;

            petItems.forEach(petItem => {
                const petName = petItem.dataset.name;
                const petBreedId = petItem.dataset.breed;
                const petBreedName = petItem.dataset.breedname;
                const petTypeId = petItem.dataset.type;
                const petTypeName = petItem.dataset.typename;
                const petStatus = petItem.dataset.status;
                const petDateAdded = new Date(petItem.dataset.dateadded);

                const matchesDate = (petDateAdded >= startDate && petDateAdded <= endDate);
                const matchesBreed = (breedFilter === 'all' || petBreedId === breedFilter);
                const matchesType = (typeFilter === 'all' || petTypeId === typeFilter);
                const matchesStatus = (statusFilter === 'all' || petStatus === statusFilter);
                const matchesSearch = (
                    searchTerm === '' ||
                    petName.includes(searchTerm) ||
                    petBreedName.includes(searchTerm) ||
                    petTypeName.includes(searchTerm)
                );

                if (matchesDate && matchesBreed && matchesType && matchesStatus && matchesSearch) {
                    petItem.style.display = 'grid';
                    visibleCount++;
                } else {
                    petItem.style.display = 'none';
                }
            });

            document.getElementById('total-pets').textContent = visibleCount;
            document.getElementById('no-pets-message').classList.toggle('hidden', visibleCount > 0);
        };

        // Add event listener for search bar
        const searchBar = document.getElementById('search-bar');
        if (searchBar) {
            searchBar.addEventListener('input', () => filterPets(false));
        }

        window.onload = () => {
            filterPets(true);
        };
    </script>
</body>
</html>