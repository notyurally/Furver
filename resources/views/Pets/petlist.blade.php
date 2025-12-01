<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pet Listings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'pet-orange': '#f97316', 
                        'pet-dark': '#1f2937', 
                        'pet-bg': '#fefcf6', 
                        'pet-light-gray': '#f3f4f6', 
                        'pet-border': '#e5e7eb', 
                        'pet-accent-purple': '#8b5cf6', 
                    },
                    boxShadow: {
                        'custom-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                },
            },
        }
    </script>
</head>
<body class="bg-pet-bg min-h-screen font-sans bg-[#FBF3D5]">
    
    <!-- Navigation Bar -->
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
                <button onclick="toggleDropdown()" class="w-8 h-8 rounded-full overflow-hidden border-2 border-gray-300 hover:border-pet-orange transition duration-150 bg-pet-orange/20 flex items-center justify-center text-sm font-bold text-pet-dark" href="#">
                    {{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
                </button>
            </div>
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md">
                <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">My Profile</a>

                <!-- LOGOUT FORM -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
            <button class="sm:hidden text-gray-600 hover:text-pet-orange transition duration-150">
                <i class="bi bi-list text-2xl"></i>
            </button>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <!-- Header & Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 border-gray-200 pb-2">
            <h2 class="text-3xl font-extrabold text-pet-dark mb-2 sm:mb-0">My Pet Listings</h2>

            <a href="{{ route('pets.create') }}" class="flex items-center px-4 py-2 bg-pet-orange text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 transition duration-150 focus:outline-none focus:ring-4 focus:ring-pet-orange/30">
                <i class="bi bi-plus-lg mr-2"></i> Add New Pet
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"> {{ session('success') }} </span>
            </div>
        @endif

        <!-- Filter and Search Bar -->
        <div class="p-1.5 mt-3">
           <form action="{{ route('pets.myPets') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-2 lg:space-y-0">
                <div>
                    <div class="flex w-[200px] items-center text-[11px] bg-gray-50 border border-gray-200 rounded-lg px-2.5 pr-3 h-7 focus-within:ring-1 focus-within:ring-pet-accent-purple focus-within:border-pet-accent-purple transition">
                        <i class="bi bi-search text-gray-400 text-xs mr-1.5"></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name, breed..."
                            class="flex-1 bg-transparent text-gray-700 placeholder-gray-400 focus:outline-none text-[11px]"
                        />
                    </div>
                </div>
                <div class="flex items-center space-x-2">

                    <div class="lg:w-28">
                        <select name="pet_type" class="w-full text-[11px] bg-gray-50 border border-gray-200 text-gray-700 rounded-lg px-2 h-7 shadow-sm focus:ring-1 focus:ring-pet-accent-purple focus:border-pet-accent-purple transition">
                            <option value="">All Types</option>
                            @foreach($petTypes as $petType)
                                <option value="{{ $petType->id }}" {{ request('type') == $petType->id ? 'selected' : '' }}>
                                    {{ $petType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:w-28">
                        <select name="status" class="w-full text-[11px] bg-gray-50 border border-gray-200 text-gray-700 rounded-lg px-2 h-7 shadow-sm focus:ring-1 focus:ring-pet-accent-purple focus:border-pet-accent-purple transition">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="adopted" {{ request('status') == 'adopted' ? 'selected' : '' }}>Adopted</option>
                        </select>
                    </div>

                    <a href="{{ route('pets.myPets') }}"
                    class="flex items-center justify-center text-gray-600 hover:text-gray-800 text-[11px] px-3 h-7 rounded-lg border border-gray-200 bg-gray-50 transition font-medium">
                        Clear
                    </a>

                    <button type="submit"
                        class="flex items-center justify-center px-3 h-7 bg-pet-accent-purple text-white text-[11px] font-semibold rounded-lg shadow hover:bg-violet-600 transition">
                        <i class="bi bi-funnel mr-1 text-xs"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <hr>
        <!-- Pet Listings Grid -->
        <div id="pet-listings-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-4 pb-12">
            @forelse($pets as $petItem)
            @php
                // Fallbacks & styling helpers
                $status = $petItem->pet_status ?? 'available';
                $isAvailable = strtolower($status) === 'available';
                $statusColor = $isAvailable ? 'bg-green-500' : (strtolower($status) === 'adopted' ? 'bg-red-500' : 'bg-yellow-500');
                $statusIcon = $isAvailable ? 'bi-check-circle-fill' : (strtolower($status) === 'adopted' ? 'bi-house-heart-fill' : 'bi-clock-fill');
                $genderIcon = strtolower($petItem->pet_sex) === 'male' ? 'bi-gender-male' : 'bi-gender-female';
                $genderClass = strtolower($petItem->pet_sex) === 'male' ? 'text-blue-500' : 'text-pink-500';
                $applicationsCount = $petItem->applications()->count() ?? 0;
                
                // Get profile image
                $profileImage = 'https://placehold.co/400x176/FBBF24/FFF?text=No+Image';
                
                if ($petItem->profilePhoto && $petItem->profilePhoto->photo_path) {
                    $profileImage = asset('storage/' . $petItem->profilePhoto->photo_path);
                } elseif ($petItem->photos && $petItem->photos->count() > 0) {
                    $firstPhoto = $petItem->photos->first();
                    if ($firstPhoto && $firstPhoto->photo_path) {
                        $profileImage = asset('storage/' . $firstPhoto->photo_path);
                    }
                }
                
                $breedName = $petItem->breed ? $petItem->breed->name : 'N/A';
            @endphp

            <article class="bg-white rounded-xl overflow-hidden shadow-card border border-gray-100">
                <div class="relative group">
                    <a href="{{ route('pets.show', $petItem->id) }}" class="block">
                        <img src="{{ $profileImage }}" alt="{{ $petItem->name }} profile" class="w-full h-44 object-cover object-center group-hover:opacity-90 transition duration-300" onerror="this.onerror=null;this.src='https://placehold.co/400x176/FBBF24/FFF?text=Image+Unavailable';">
                    </a>

                    <span class="absolute top-3 right-3 {{ $statusColor }} text-white text-xs font-semibold rounded-full px-3 py-1 shadow-md flex items-center space-x-1">
                        <i class="bi {{ $statusIcon }} mr-1"></i>
                        <span>{{ ucfirst($status) }}</span>
                    </span>

                    @if(!empty($petItem->location))
                        <div class="absolute bottom-3 left-3 bg-black bg-opacity-60 text-white text-xs font-bold rounded-full px-3 py-1 shadow-md">
                            <i class="bi bi-geo-alt-fill mr-1"></i> {{ $petItem->location }}
                        </div>
                    @endif
                </div>

                <div class=" sm:p-5">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-lg font-extrabold text-gray-900">{{ $petItem->name }}</p>
                        <div class="text-s font-bold text-pet-orange">₱{{ number_format($petItem->adoption_fee ?? 0, 2) }}</div>
                    </div>

                    <span class="inline-block bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1 rounded-full mb-3">Breed: {{ $breedName }}</span>

                    <div class="text-sm text-gray-500 mb-4 flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="bi bi-calendar-fill mr-1"></i>
                            @if($petItem->age && $petItem->age_data)
                                {{ $petItem->age }} {{ $petItem->age_data }}
                            @elseif($petItem->birthdate)
                                {{ \Carbon\Carbon::parse($petItem->birthdate)->age }} years
                            @else
                                Unknown
                            @endif
                        </span>
                        <span class="flex items-center">
                            <i class="bi {{ $genderIcon }} mr-1 {{ $genderClass }}"></i> {{ ucfirst($petItem->pet_sex ?? 'Unspecified') }}
                        </span>
                    </div>

                    <!-- Applications Metric -->
                    <div class="mb-2">
                        <a href="{{ route('pets.show', $petItem->id) }}" class="flex justify-center items-center p-2 bg-gray-50 rounded-lg text-sm font-medium hover:bg-gray-100 transition duration-200 text-gray-700">
                            <i class="bi bi-person-check-fill mr-1"></i>
                            {{ $applicationsCount }} {{ \Illuminate\Support\Str::plural('Application', $applicationsCount) }}
                        </a>
                    </div>

                    <hr>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-1 mt-2">
                        <a href="{{ route('pets.show', $petItem->id) }}" class="btn btn-sm rounded-lg flex-grow-1 fw-bold transition duration-200 text-white shadow-sm" style="background-color: #f97316; border-color: #f97316;">
                                View Details
                        </a>
                        <a href="{{ route('pets.edit', $petItem->id) }}" class="btn btn-sm btn-outline-primary rounded-lg transition duration-200" title="Edit Listing">
                                <i class="bi bi-pencil"></i>
                            </a>
                        <form id="delete-form-{{ $petItem->id }}" action="{{ route('pets.destroy', $petItem->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showDeleteModal('delete-form-{{ $petItem->id }}')" class="btn btn-sm btn-outline-danger rounded-lg" title="Remove Listing">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-100">
                <i class="bi bi-exclamation-triangle-fill text-6xl text-gray-300"></i>
                <p class="mt-4 text-xl text-gray-600 font-semibold">No pets listed yet.</p>
                <p class="text-gray-400 mt-2">Click "Add New Pet" to create your first listing.</p>
            </div>
        @endforelse
        </div>

        <!-- Optionally: Pagination -->
        @if(method_exists($pets, 'links'))
            <div class="mt-6">
                {{ $pets->links() }}
            </div>
        @endif
    </main>

    <!-- DELETE CONFIRMATION MODAL -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900 bg-opacity-60">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl sm:w-full sm:max-w-lg mx-4">
            <div class="p-6 sm:p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900">Delete Pet Listing</h3>
                        <p class="mt-2 text-sm text-gray-500">Are you sure you want to delete this pet listing? This action cannot be undone.</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl">
                <button type="button" id="confirm-delete-btn" onclick="confirmDelete()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Delete Listing</button>
                <button type="button" onclick="hideDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let deleteFormId = null;

        function showDeleteModal(formId) {
            deleteFormId = formId;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function confirmDelete() {
            if (!deleteFormId) return;

            try {
                const form = document.getElementById(deleteFormId);
                if (!form) {
                    console.error('Delete form not found:', deleteFormId);
                    return;
                }

                // Build form data to send (includes _method and _token)
                const formData = new FormData(form);

                // Use fetch to POST the form (with method spoofing via _method)
                const url = form.action;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                if (resp.ok) {
                    // Close modal then reload or redirect
                    hideDeleteModal();
                    // If JSON returned with redirect, follow it
                    const ct = resp.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        const data = await resp.json();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                    }
                    // Default: reload page to reflect deletion
                    window.location.reload();
                } else {
                    console.error('Delete failed', resp.status, await resp.text());
                    hideDeleteModal();
                    alert('Failed to delete pet. Server returned: ' + resp.status);
                }
            } catch (err) {
                console.error('Delete error', err);
                hideDeleteModal();
                alert('An error occurred while deleting the pet. Check console for details.');
            }
        }

        // Close modal when clicking outside
        document.getElementById('delete-modal').addEventListener('click', function (e) {
            if (e.target === this) hideDeleteModal();
        });

        // ESC key closes modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') hideDeleteModal();
        });

        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('hidden');
        }

    </script>
</body>
</html>