<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet - {{ $pet->name ?? 'Pet' }}</title>
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
    <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-sm">
            <h3 class="text-xl font-bold text-red-600 mb-3">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this pet listing? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">Cancel</button>
                <button id="confirm-delete-btn" type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150">Delete</button>
            </div>
        </div>
    </div>

    <nav class="sticky top-0 z-10 w-full bg-white shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="#" class="text-2xl font-extrabold text-pet-dark flex items-center">
                <i class="bi bi-paw-fill text-pet-orange mr-2"></i> FurEver
            </a>
            <div class="hidden sm:flex items-center space-x-6 text-gray-600 font-medium">
                <a class="hover:text-pet-orange transition duration-150" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="text-pet-orange border-b-2 border-pet-orange pb-1" href="{{ route('pets.myPets') }}">My Pets</a>
                <span class="w-8 h-8 rounded-full border-2 border-pet-orange bg-pet-orange/20 flex items-center justify-center text-sm font-bold text-pet-dark">
                    UN
                </span>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 lg:py-12">
        <form
            action="{{ route('pets.update', $pet->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="max-w-6xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-2xl">
            @csrf
            @method('PUT')

            <input type="hidden" name="pet_type_id" value="{{ old('pet_type_id', $pet->breed->pet_type_id ?? 1) }}">
            <div class="flex justify-between items-center mb-6">
                <a href="{{ url()->previous() }}" title="Go Back" class="text-3xl text-gray-500 hover:text-pet-orange transition duration-150">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                </a>
                <h1 class="text-3xl font-extrabold text-pet-dark text-center flex-grow">
                    Editing: {{ $pet->name }}
                </h1>
                <div></div>
            </div>

            <div class="lg:flex lg:space-x-10">
                <div class="lg:w-7/12 mt-2">
                    <div class="mb-8 p-4 border rounded-xl bg-gray-50">
                        <h2 class="text-2xl font-bold text-pet-dark mb-4 border-b pb-2">Photo Gallery</h2>
                    <!-- Profile Photo Section -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Profile Photo</label>
                        <div class="w-full aspect-square md:aspect-[3/2] overflow-hidden rounded-xl shadow-xl mb-3 border-2 border-pet-orange/50 relative">
                            <img id="profile-preview" src="{{ $pet->profile_photo_url }}" alt="Current Pet Profile" class="w-full h-full object-cover">
                        </div>
                        <input type="file" name="profile_picture" id="profile_photo" accept="image/*" 
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pet-orange file:text-white hover:file:bg-orange-600">
                        @error('profile_picture')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Photos Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Photos</label>
                        
                        <!-- Existing Photos Grid -->
                        <div id="additional-photos-container" class="grid grid-cols-3 gap-3 mb-3">
                            @foreach ($pet->additionalPhotos ?? [] as $photo)
                                <div class="relative group" data-photo-id="{{ $photo->id }}">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                        class="w-full h-32 object-cover rounded-lg shadow-md" 
                                        alt="Pet photo {{ $photo->id }}">
                                    
                                    <!-- Delete Button Overlay -->
                                    <button type="button" 
                                            onclick="deleteExistingPhoto({{ $photo->id }})"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-700 shadow-lg">
                                        <i class="bi bi-trash-fill text-sm"></i>
                                    </button>
                                </div>
                            @endforeach

                            <!-- Add More Photos Button -->
                            <div class="w-full h-32 rounded-lg shadow-inner border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-sm font-medium text-gray-500 cursor-pointer hover:bg-gray-100 hover:border-pet-orange transition-all" 
                                onclick="document.getElementById('additional_photos_input').click()">
                                <div class="text-center">
                                    <i class="bi bi-plus-circle-fill text-3xl text-pet-orange mb-1"></i>
                                    <p class="text-xs">Add Photos</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden File Input -->
                        <input type="file" 
                            name="additional_photos[]" 
                            id="additional_photos_input" 
                            accept="image/*" 
                            multiple 
                            class="hidden"
                            onchange="previewNewPhotos(event)">
                        
                        <!-- Preview Container for New Photos -->
                        <div id="new-photos-preview" class="grid grid-cols-3 gap-3 mt-3"></div>
                        
                        <!-- Hidden input to track deleted photos -->
                        <input type="hidden" name="deleted_photos" id="deleted_photos" value="">
                        
                        @error('additional_photos.*')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                    <h2 class="text-2xl font-bold text-pet-dark mb-3 mt-6 border-b pb-2">
                        About {{ $pet->name }}
                    </h2>
                    <textarea
                        name="description"
                        rows="6"
                        class="w-full text-gray-600 leading-relaxed text-base bg-white p-4 rounded-xl border border-gray-300 shadow-inner focus:ring-pet-orange focus:border-pet-orange"
                        placeholder="Provide a detailed description of the pet's personality and history."
                    >{{ old('description', $pet->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-pet-dark mb-4 flex items-center">
                                <i class="bi bi-hospital-fill text-red-500 mr-3 text-2xl"></i> Health & Wellness
                            </h3>
                            <div class="grid grid-cols-1 gap-4 text-sm bg-red-50 p-4 rounded-xl border border-red-200 shadow-md">
                                
                                <div class="col-span-1 py-2 border-b flex items-center justify-between">
                                    <label for="is_vaccinated" class="text-gray-700 font-semibold">Vaccinated</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_vaccinated" id="is_vaccinated" value="1" {{ old('is_vaccinated', $pet->health->is_vaccinated ?? false) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div class="col-span-1 py-2 border-b">
                                    <label for="last_vaccinated_date" class="block text-gray-700 font-semibold mb-1">Last Vaccination Date</label>
                                    <input type="date" name="last_vaccinated_date" id="last_vaccinated_date" value="{{ old('last_vaccinated_date', $pet->health?->formatted_last_vaccinated_date) ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-pet-orange focus:border-pet-orange">
                                </div>

                                <div class="col-span-1 py-2 border-b flex items-center justify-between">
                                    <label for="is_spayed" class="text-gray-700 font-semibold">Spayed/Neutered</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="is_spayed" id="is_spayed" value="1" {{ old('is_spayed', $pet->health->is_spayed ?? false) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div class="col-span-1 py-2 border-b">
                                    <label for="last_spay_date" class="block text-gray-700 font-semibold mb-1">Spay/Neuter Date</label>
                                    <input type="date" name="last_spay_date" id="last_spay_date" value="{{ old('last_spay_date', $pet->health?->formatted_last_spay_date) ?? '' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-pet-orange focus:border-pet-orange">
                                </div>

                                <div class="col-span-1 py-2">
                                    <label for="microchip_number" class="block text-gray-700 font-semibold mb-1">Microchip Number</label>
                                    <input type="text" name="microchip_number" id="microchip_number" value="{{ old('microchip_number', optional($pet->health)->microchip_number ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-pet-orange focus:border-pet-orange" placeholder="e.g., 981000000123456">
                                </div>

                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-pet-dark mb-4 flex items-center">
                                <i class="bi bi-people-fill text-blue-500 mr-3 text-2xl"></i> Behavioral Traits
                            </h3>
                            <div id="traits-container" class="flex flex-wrap gap-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                <input type="hidden" name="behavioral_traits" id="traits-input" value="{{ old('behavioral_traits', $pet->behavioral_traits) }}">
                                <input type="text" id="new-trait-input" placeholder="Add trait..."
                                    class="flex-1 min-w-[100px] px-3 py-1 border border-gray-300 rounded-full text-sm focus:ring-pet-orange focus:border-pet-orange">
                            </div>
                            @error('behavioral_traits')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="lg:w-5/12 mt-6 lg:mt-0">
                    <div class="mb-4">
                        <label for="pet_name" class="block text-sm font-medium text-gray-500 uppercase mb-1">Pet Name</label>
                        <input type="text" id="pet_name" name="name" value="{{ old('name', $pet->name) }}" required
                            class="w-full text-4xl font-extrabold text-pet-dark leading-tight border-b-2 border-gray-200 focus:border-pet-orange p-1 focus:outline-none">
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 uppercase mb-1">Status (Read-only)</label>
                        <div class="px-3 py-2 border border-gray-300 rounded-xl bg-gray-100 text-lg font-semibold text-gray-600 capitalize">
                            {{ $pet->pet_status }}
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pet status is managed automatically by the system</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="adoption_fee" class="block text-sm font-medium text-gray-500 uppercase mb-1">Adoption Fee (₱)</label>
                        <input type="number" name="adoption_fee" id="adoption_fee" value="{{ old('adoption_fee', $pet->adoption_fee) }}" required min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm bg-white text-lg font-semibold focus:ring-pet-orange focus:border-pet-orange">
                        @error('adoption_fee')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col space-y-3 mb-6">
                        <button type="submit"
                            class="w-full flex items-center justify-center px-6 py-3 bg-pet-blue text-white text-lg font-semibold rounded-xl border-2 border-pet-blue hover:bg-blue-600 transition duration-300">
                            <i class="bi bi-save-fill mr-3"></i> Save Changes
                        </button>
                        <a href="{{ route('pets.destroy', $pet->id) }}" id="open-delete-modal-btn"
                            class="w-full flex items-center justify-center px-6 py-3 bg-red-50 text-red-700 text-lg font-semibold rounded-xl border-2 border-red-300 hover:bg-red-100 transition duration-300">
                            <i class="bi bi-trash-fill mr-3"></i> Delete Listing
                        </a>
                    </div>

                    <h3 class="text-xl font-bold text-pet-dark mb-4 mt-6 border-t pt-6">Listing Activity (Read-only)</h3>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="metric-card">
                            <p class="metric-value">{{ $pet->applications_count }}</p>
                            <p class="metric-label">Applications</p>
                        </div>
                        <div class="metric-card">
                            <p class="metric-value text-xl">{{ \Carbon\Carbon::parse($pet->created_at)->format('M d, Y') }}</p>
                            <p class="metric-label">Date Listed</p>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-pet-dark mb-4 mt-6 border-t pt-6">Pet Profile Details</h3>
                    <div class="grid grid-cols-1 gap-y-5 gap-x-4">

                        @php $location = old('location', $pet->location); @endphp
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-500 uppercase">Location</label>
                            <div class="flex items-center mt-1">
                                <i class="bi bi-geo-alt-fill paw-icon text-pet-orange mr-3"></i>
                                <input type="text" name="location" id="location" value="{{ $location }}" required
                                    class="w-full text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange" placeholder="e.g., Makati City, Metro Manila">
                            </div>
                            @error('location')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @php $breed_id = old('breed_id', $pet->breed_id); @endphp
                        <div>
                            <label for="breed_id" class="block text-sm font-medium text-gray-500 uppercase">Breed</label>
                            <div class="flex items-center mt-1">
                                <i class="bi bi-person-fill paw-icon mr-3"></i>
                                <select name="breed_id" id="breed_id" required
                                    class="w-full text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange">
                                   @foreach ($breeds as $breed)
                                        <option value="{{ $breed->id }}" {{ $breed_id == $breed->id ? 'selected' : '' }}>
                                            {{ $breed->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('breed_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-500 uppercase">Age</label>
                            <div class="flex items-center mt-1 space-x-3">
                                <i class="bi bi-calendar-event paw-icon"></i>
                                <input type="number" name="age" id="age" value="{{ old('age', $pet->age) }}" min="0"
                                    class="w-1/2 text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange">
                                
                                <select name="age_data" id="age_data"
                                    class="w-1/2 text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange">
                                    <option value="months" {{ old('age_data', $pet->age_data) == 'months' ? 'selected' : '' }}>Months</option>
                                    <option value="years" {{ old('age_data', $pet->age_data) == 'years' ? 'selected' : '' }}>Years</option>
                                </select>
                            </div>
                            @error('age')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="birthdate" class="block text-sm font-medium text-gray-500 uppercase">Birthdate (Optional)</label>
                            <div class="flex items-center mt-1">
                                <i class="bi bi-cake2 paw-icon mr-3"></i>
                                <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $pet->formatted_birthdate) }}"
                                    class="w-full text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange">
                            </div>
                            @error('birthdate')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @php $pet_sex = old('pet_sex', $pet->pet_sex); @endphp
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase mb-1">Gender</p>
                            <div class="flex space-x-4 mt-1">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="pet_sex" value="male" class="form-radio text-blue-600 focus:ring-blue-500" {{ $pet_sex == 'male' ? 'checked' : '' }}>
                                    <span class="ml-2 text-base font-semibold text-pet-dark">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="pet_sex" value="female" class="form-radio text-pink-600 focus:ring-pink-500" {{ $pet_sex == 'female' ? 'checked' : '' }}>
                                    <span class="ml-2 text-base font-semibold text-pet-dark">Female</span>
                                </label>
                            </div>
                            @error('pet_sex')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @php $pet_size = old('pet_size', $pet->pet_size); @endphp
                        <div>
                            <label for="pet_size" class="block text-sm font-medium text-gray-500 uppercase">Size</label>
                            <div class="flex items-center mt-1">
                                <i class="bi bi-rulers paw-icon mr-3"></i>
                                <select name="pet_size" id="pet_size" required
                                    class="w-full text-base font-semibold text-pet-dark border border-gray-300 rounded-lg p-2 focus:ring-pet-orange focus:border-pet-orange">
                                    <option value="small" {{ $pet_size == 'small' ? 'selected' : '' }}>Small</option>
                                    <option value="medium" {{ $pet_size == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="large" {{ $pet_size == 'large' ? 'selected' : '' }}>Large</option>
                                    <option value="extra_large" {{ $pet_size == 'extra_large' ? 'selected' : '' }}>Extra Large</option>
                                </select>
                            </div>
                            @error('pet_size')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/pet-edit.js') }}"></script>
</body>
</html>