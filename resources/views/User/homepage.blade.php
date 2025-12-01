<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurEver Home - Adopt a Pet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        [data-lucide] {
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<nav class="bg-white shadow-lg sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="#" class="flex-shrink-0 flex items-center cursor-pointer transition-opacity hover:opacity-80">
                <i data-lucide="paw-print" class="h-8 w-8 text-amber-500"></i>
                <span class="text-2xl font-bold text-gray-900 ml-2">FurEver</span>
            </a>

            <div class="hidden sm:ml-6 sm:flex sm:space-x-5">
                <a href="#" class="flex items-center px-2 py-1.5 text-sm font-semibold transition-all duration-150 
                           text-orange-600 border-b-2 border-orange-600 -mb-0.5">Home Page</a>               
                <a href="{{ route('pets.adoptList')}}" class="text-gray-700 hover:text-orange-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Adoptable Pets</a>
            </div>

            <!-- User CTA -->
            <div class="flex items-center">
                @auth
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-orange-700 transition-colors shadow-md">
                                <i data-lucide="log-out" class="w-4 h-4 inline-block mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-orange-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-orange-700 transition-colors shadow-md">
                        <i data-lucide="user" class="w-4 h-4 inline-block mr-1"></i> Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="relative bg-orange-600 overflow-hidden pt-15 pb-24 sm:pt-24 sm:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white pb-11">
        <h1 class="mt-3 sm:text-6xl font-extrabold tracking-tight mb-4">
            Your New Best Friend is Waiting.
        </h1>
        <p class="max-w-3xl mx-auto sm:text-xl font-light mb-10 opacity-90">
            Connecting loving hearts with rescued pets. Every adoption saves a life.
        </p>

        
        <!-- Quick Search Bar -->
        <form action="" method="GET" class="mt-16 w-full max-w-2xl mx-auto bg-white p-2 rounded-xl shadow-2xl">
            <div class="flex space-x-3 items-center">
                <i data-lucide="search" class="w-6 h-6 text-gray-400 ml-2 hidden sm:block"></i>
                <input
                    type="text"
                    name="query"
                    placeholder="Search by Species, Breed, or Location"
                    class="flex-1 py-3 text-gray-800 placeholder-gray-500 focus:outline-none"
                    value="{{ request('query') }}"
                />
                <button type="submit" class="bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-orange-700 transition-colors shadow-lg">
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<main>
    <!-- Featured Pets Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-4">Meet Our Featured Friends</h2>
            <p class="text-xl text-center text-gray-600 mb-12">New arrivals, staff favorites, and pets that need a home now.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($featuredPets as $pet)
                    @php
                        // Status badge colors
                        $statusColor = match($pet->pet_status) {
                            'adopted' => 'bg-red-500',
                            'pending' => 'bg-amber-500',
                            'available' => 'bg-green-500',
                            default => 'bg-gray-500'
                        };
                        
                        // Icon based on pet type
                        $petType = $pet->breed->petTypes->name ?? 'Unknown';
                        $iconClass = strtolower($petType) === 'dog' ? 'text-amber-600' : 'text-sky-600';
                        $iconName = strtolower($petType) === 'dog' ? 'paw-print' : 'heart';
                        
                        // Get profile picture
                        $profilePhoto = $pet->photos()->where('is_profile', true)->first();
                        $imageUrl = $profilePhoto 
                            ? asset('storage/' . $profilePhoto->photo_path) 
                            : 'https://placehold.co/400x300/9CA3AF/1F2937?text=' . urlencode($pet->name);
                        
                        // Calculate age display
                        if ($pet->birthdate) {
                            $age = \Carbon\Carbon::parse($pet->birthdate)->age;
                            $ageDisplay = $age . ' year' . ($age != 1 ? 's' : '');
                        } elseif ($pet->age && $pet->age_data) {
                            $ageDisplay = $pet->age . ' ' . $pet->age_data;
                        } else {
                            $ageDisplay = 'Unknown';
                        }
                    @endphp
                    <div
                        class="bg-white rounded-xl shadow-lg overflow-hidden transform transition duration-500 hover:scale-[1.03] hover:shadow-2xl hover:border-orange-500 cursor-pointer border-t-8 border-orange-500/80"
                        onclick="window.location.href='{{ route('pets.show', $pet->id) }}'"
                    >
                        <!-- Image Section -->
                        <div class="relative h-48">
                            <img
                                src="{{ $imageUrl }}"
                                alt="Profile image of {{ $pet->name }}"
                                class="w-full h-full object-cover"
                                onerror="this.onerror=null;this.src='https://placehold.co/400x300/9CA3AF/1F2937?text={{ urlencode($pet->name) }}';"
                            />
                            <div class="absolute top-3 right-3 px-3 py-1 text-xs font-semibold text-white rounded-full shadow-md {{ $statusColor }}">
                                {{ ucfirst($pet->pet_status) }}
                            </div>
                        </div>

                        <!-- Info Section -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-xl font-bold text-gray-800 truncate">{{ $pet->name }}</h2>
                                <i data-lucide="{{ $iconName }}" class="w-5 h-5 {{ $iconClass }}"></i>
                            </div>

                            <p class="text-sm text-gray-600 flex items-center mb-1">
                                <i data-lucide="map-pin" class="w-3 h-3 mr-2 text-orange-400"></i>
                                {{ $pet->location }}
                            </p>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Breed:</span> {{ $pet->breed->name ?? 'Mixed' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Age:</span> <span class="font-semibold text-gray-700">{{ $ageDisplay }}</span>
                            </p>
                            <button
                                class="mt-3 w-full text-center text-sm font-medium text-orange-600 hover:text-orange-800 flex items-center justify-center transition-colors">
                                View Profile <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i data-lucide="inbox" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                        <p class="text-xl text-gray-600">No pets available at the moment. Check back soon!</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-12">
                <a
                    href="#"
                    class="inline-flex items-center text-lg font-semibold text-orange-600 hover:text-orange-800 transition-colors"
                >
                    View All Adoptable Pets <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Impact Statistics -->
    <div class="bg-orange-50 py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Our Impact So Far</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                @php
                    $stats = [
                        ['number' => \App\Models\Pet::where('pet_status', 'adopted')->count() . '+', 'label' => 'Pets Adopted', 'icon' => 'heart', 'color' => 'text-red-500'],
                        ['number' => '15', 'label' => 'Years of Service', 'icon' => 'globe', 'color' => 'text-orange-500'],
                        ['number' => '98%', 'label' => 'Adoption Rate', 'icon' => 'star', 'color' => 'text-amber-500']
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div class="bg-white p-8 rounded-xl shadow-xl border-b-4 border-amber-400 transform hover:scale-[1.02] transition-transform duration-300">
                        <i data-lucide="{{ $stat['icon'] }}" class="w-8 h-8 {{ $stat['color'] }} mx-auto mb-2"></i>
                        <p class="text-5xl font-extrabold text-gray-900">{{ $stat['number'] }}</p>
                        <p class="text-lg text-gray-600 mt-1">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-12">The Simple Adoption Process</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                @php
                    $steps = [
                        ['step' => 1, 'icon' => 'search', 'title' => 'Search & Find', 'description' => 'Browse our curated profiles and use filters to find the perfect match for your home and lifestyle.'],
                        ['step' => 2, 'icon' => 'briefcase', 'title' => 'Apply Online', 'description' => 'Fill out our secure, simple application form. This helps us ensure a safe and happy placement.'],
                        ['step' => 3, 'icon' => 'message-square', 'title' => 'Meet & Greet', 'description' => 'Connect with the foster or shelter staff to schedule a time to meet your potential new companion.'],
                        ['step' => 4, 'icon' => 'heart', 'title' => 'Welcome Home', 'description' => 'Finalize the adoption paperwork and begin your beautiful journey with your new family member!'],
                    ];
                @endphp
                @foreach($steps as $step)
                    <div class="flex flex-col items-center text-center p-6 border-2 border-dashed border-orange-300 rounded-xl shadow-lg bg-white transition-shadow duration-300 hover:shadow-2xl">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-600 text-white text-xl font-bold mb-4 shadow-xl">
                            {{ $step['step'] }}
                        </div>
                        <i data-lucide="{{ $step['icon'] }}" class="w-10 h-10 text-orange-600 mb-3"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-gray-600">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-20 bg-orange-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-center text-gray-900 mb-12">Happy Tails & Happy Families</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @if(isset($testimonials) && count($testimonials) > 0)
                    @foreach($testimonials as $testimonial)
                        <div class="bg-white p-8 rounded-xl shadow-xl border-l-4 border-amber-500 transform transition duration-300 hover:scale-[1.02]">
                            <blockquote class="italic text-gray-700 mb-4">
                                "{{ $testimonial['quote'] }}"
                            </blockquote>
                            <p class="font-semibold text-orange-600 border-t pt-3">
                                - {{ $testimonial['adopter'] }}, adopted {{ $testimonial['petName'] }}
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Secondary CTAs -->
    <div class="py-16 bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Can't Adopt Right Now? You Can Still Help!</h2>
            <p class="text-xl text-gray-300 mb-10">Supporting our mission means saving more lives.</p>

            <div class="flex flex-wrap justify-center gap-6">
                <a href="#" class="bg-green-500 text-white px-8 py-3 rounded-full font-semibold text-lg hover:bg-green-600 transition-colors shadow-lg flex items-center transform hover:scale-105">
                    <i data-lucide="dollar-sign" class="w-5 h-5 mr-2"></i> Donate
                </a>
                <a href="#" class="bg-amber-500 text-gray-900 px-8 py-3 rounded-full font-semibold text-lg hover:bg-amber-600 transition-colors shadow-lg flex items-center transform hover:scale-105">
                    <i data-lucide="user" class="w-5 h-5 mr-2"></i> Volunteer
                </a>
                <a href="#" class="bg-orange-500 text-white px-8 py-3 rounded-full font-semibold text-lg hover:bg-orange-600 transition-colors shadow-lg flex items-center transform hover:scale-105">
                    <i data-lucide="paw-print" class="w-5 h-5 mr-2"></i> Foster a Pet
                </a>
            </div>
        </div>
    </div>
</main>

<!-- Footer Section -->
<footer class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <!-- Logo and Contact -->
            <div>
                <div class="flex items-center mb-4">
                    <i data-lucide="paw-print" class="h-6 w-6 text-amber-500"></i>
                    <span class="text-xl font-bold ml-2">FurEver Home</span>
                </div>
                <p class="text-sm text-gray-400">
                    123 Rescue Lane, Pet City, 90210
                </p>
                <p class="text-sm text-gray-400">
                    Email: info@fureverhome.org
                </p>
            </div>

            <!-- Sitemap -->
            <div>
                <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">Adoptable Pets</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">About Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Legal</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div>
                <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Connect</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">
                        <i data-lucide="facebook" class="w-6 h-6"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-amber-500 transition-colors">
                        <i data-lucide="twitter" class="w-6 h-6"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} FurEver. All rights reserved.</p>
        </div>
    </div>
</footer>
<script>
    window.onload = function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
};
</script>
</body>
</html>