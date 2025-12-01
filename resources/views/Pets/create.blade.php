<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Profile Creator - Choose Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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

<body class="bg-[#FBF3D5] antialiased">

    <div class="max-w-4xl lg:max-w-3xl xl:max-w-2xl w-[95%] md:w-[75%] lg:w-[60%] mx-auto my-10 bg-white p-6 md:p-8 shadow-lg rounded-xl border border-gray-100">
        
        <!-- Error Display -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
                <strong class="font-bold">Error:</strong>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Category Selection Screen -->
        <div id="selection-screen" class="space-y-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Start your Journey!</h1>
                <a href="{{ route('pets.myPets') }}" class="text-gray-500 hover:text-gray-800"><i class="bi bi-x-lg"></i></a>
            </div>            
            <p class="text-gray-600 mb-8">Choose a category to begin your pet's profile.</p>

            <div class="space-y-6">
                @foreach($petTypes as $type)
                <div class="category-card cursor-pointer p-6 rounded-xl bg-blue-50 hover:bg-blue-100 flex items-center space-x-6 transition-all duration-200" 
                     data-pet-type="{{ $type->id }}" 
                     data-pet-type-name="{{ $type->name }}">
                    <img src="{{ asset('images/' . strtolower($type->name) . '.png') }}" 
                         alt="{{ $type->name }}" 
                         class="w-20 h-20 rounded-lg shadow-md">
                    <span class="text-2xl font-semibold text-gray-800">{{ $type->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pet Creation Form -->
        <form class="space-y-6" method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data">
            @csrf
            <div id="form-container" class="hidden">
                <input type="hidden" id="pet_type_id" name="pet_type_id" value="">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900" id="form-title">
                        Create New <span class="text-orange-600">Pet</span> Profile
                    </h1>
                    <button type="button" id="back-to-selection" class="text-gray-500 hover:text-gray-800">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <!-- Progress Indicator -->
                <div class="flex justify-between items-start relative my-8">
                    <div class="absolute inset-0 h-1 bg-gray-200 top-5 transform -translate-y-1/2 mx-4">
                        <div id="progressBarFill" class="h-full bg-orange-600 transition-all duration-300 ease-in-out w-0"></div>
                    </div>

                    <div id="step-1" class="step-indicator relative z-10 flex flex-col items-center w-1/3 select-none">
                        <div class="step-active-circle rounded-full bg-orange-600 text-white flex items-center justify-center font-bold transition-all duration-300 shadow-md ring-4 ring-orange-200">
                            <i class="fa fa-paw text-lg"></i>
                        </div>
                        <span class="text-sm mt-2 text-center text-gray-700 font-semibold whitespace-nowrap">1. Basic Details</span>
                    </div>

                    <div id="step-2" class="step-indicator relative z-10 flex flex-col items-center w-1/3 select-none">
                        <div class="step-active-circle rounded-full bg-gray-300 text-white flex items-center justify-center font-bold transition-all duration-300 shadow-md">
                            <i class="bi bi-heart text-lg"></i>
                        </div>
                        <span class="text-sm mt-2 text-center text-gray-500 whitespace-nowrap">2. Health Status</span>
                    </div>

                    <div id="step-3" class="step-indicator relative z-10 flex flex-col items-center w-1/3 select-none">
                        <div class="step-active-circle rounded-full bg-gray-300 text-white flex items-center justify-center font-bold transition-all duration-300 shadow-md">
                            <i class="bi bi-emoji-smile text-lg"></i>
                        </div>
                        <span class="text-sm mt-2 text-center text-gray-500 whitespace-nowrap">3. Behavior & Media</span>
                    </div>
                </div>
                <hr class="my-6 border-gray-200">
                
                <div class="tab-content" id="petTabContent">                   
                    <!-- Basic Information Tab -->
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <h4 class="text-xl font-semibold mb-4 text-gray-800">Basic Information</h4>
                        <p class="text-gray-500 mb-6">Enter detailed information for the pet listing.</p> 
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">                            
                            <!-- Pet Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pet Name<span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                       id="name"
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Breed -->
                            <div>
                                <label for="breed_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Breed<span class="text-red-500">*</span>
                                </label>
                                <select class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5 bg-white" 
                                        id="breed_id"
                                        name="breed_id" 
                                        required>
                                    <option value="">Select Breed</option>
                                    @foreach($breeds as $breed)
                                        <option value="{{ $breed->id }}" {{ old('breed_id') == $breed->id ? 'selected' : '' }}>
                                            {{ $breed->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('breed_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Sex/Gender -->
                            <div>
                                <label for="pet_sex" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sex<span class="text-red-500">*</span>
                                </label>
                                <select class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5 bg-white" 
                                        id="pet_sex"
                                        name="pet_sex" 
                                        required>
                                    <option value="">Select</option>
                                    <option value="male" {{ old('pet_sex') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('pet_sex') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('pet_sex')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>                            
                            
                            <!-- Size -->
                            <div>
                                <label for="pet_size" class="block text-sm font-medium text-gray-700 mb-1">
                                    Size<span class="text-red-500">*</span>
                                </label>
                                <select class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5 bg-white" 
                                        id="pet_size"
                                        name="pet_size" 
                                        required>
                                    <option value="">Select Size</option>
                                    <option value="small" {{ old('pet_size') == 'small' ? 'selected' : '' }}>Small</option>
                                    <option value="medium" {{ old('pet_size') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="large" {{ old('pet_size') == 'large' ? 'selected' : '' }}>Large</option>
                                    <option value="extra_large" {{ old('pet_size') == 'extra_large' ? 'selected' : '' }}>Extra Large</option>
                                </select>
                                @error('pet_size')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                          <!-- Birthdate -->
                            <div>
                                <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">
                                    Birthdate
                                </label>
                                <input type="date" 
                                    class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                    id="birthdate"
                                    name="birthdate"
                                    value="{{ old('birthdate') }}"
                                    onchange="calculateAge()">
                                @error('birthdate')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Age (single input + unit) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                <div class="flex space-x-2 items-center">
                                    <input type="number"
                                        id="age"
                                        class="w-1/2 border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5"
                                        name="age"
                                        value="{{ old('age') }}"
                                        placeholder="Auto-calculated"
                                        min="0"
                                        readonly>

                                    <select id="age_data" name="age_data" class="w-1/2 border border-gray-300 rounded-lg shadow-sm p-2.5 bg-white" disabled>
                                        <option value="years" {{ old('age_data') == 'years' ? 'selected' : '' }}>Years</option>
                                        <option value="months" {{ old('age_data') == 'months' ? 'selected' : '' }}>Months</option>
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Age will be calculated automatically from birthdate</p>
                                @error('age')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Adoption Fee -->
                            <div>
                                <label for="adoption_fee" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adoption Fee<span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 text-gray-500 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">₱</span>
                                    <input type="number" 
                                           class="flex-grow border border-gray-300 rounded-r-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                           id="adoption_fee"
                                           name="adoption_fee" 
                                           step="0.01" 
                                           value="{{ old('adoption_fee') }}"
                                           required 
                                           placeholder="0.00">
                                </div>
                                @error('adoption_fee')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                    Location<span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                       id="location" 
                                       name="location" 
                                       value="{{ old('location') }}"
                                       required 
                                       placeholder="E.g., Dasmariñas, Cavite">
                                @error('location')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Tell us about this pet's personality, habits, and what makes them special...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Health Status Tab -->
                    <div class="tab-pane fade hidden" id="health" role="tabpanel">
                        <h4 class="text-xl font-semibold mb-4 text-gray-800">Health Status</h4>
                        
                        <!-- Vaccination Status -->
                        <div class="mb-5 flex items-center">
                            <input type="hidden" name="is_vaccinated" value="0">
                            <label for="is_vaccinated" class="flex items-center cursor-pointer">
                                <input class="sr-only peer" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="is_vaccinated" 
                                       name="is_vaccinated" 
                                       value="1"
                                       {{ old('is_vaccinated') ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700">Vaccination Status (Up to date)</span>
                            </label>
                        </div>
                        
                        <!-- Last Vaccination Date -->
                        <div class="mb-5" id="vaccination_date_field">
                            <label for="last_vaccinated_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Vaccination Date
                            </label>
                            <input type="date" 
                                   class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                   id="last_vaccinated_date"
                                   name="last_vaccinated_date"
                                   value="{{ old('last_vaccinated_date') }}">
                            @error('last_vaccinated_date')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Spay/Neuter Status -->
                        <div class="mb-5 flex items-center">
                            <input type="hidden" name="is_spayed" value="0">
                            <label for="is_spayed" class="flex items-center cursor-pointer">
                                <input class="sr-only peer" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="is_spayed" 
                                       name="is_spayed" 
                                       value="1"
                                       {{ old('is_spayed') ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>  
                                <span class="ms-3 text-sm font-medium text-gray-700">Spay/Neuter Status</span>
                            </label>
                        </div>
                        
                        <!-- Last Spay Date -->
                        <div class="mb-5" id="spay_date_field">
                            <label for="last_spay_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Last Spay/Neuter Date
                            </label>
                            <input type="date" 
                                   class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                   id="last_spay_date"
                                   name="last_spay_date"
                                   value="{{ old('last_spay_date') }}">
                            @error('last_spay_date')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Microchip Number -->
                        <div class="mb-5">
                            <label for="microchip_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Microchip Number
                            </label>
                            <input type="text" 
                                   class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                   id="microchip_number"
                                   name="microchip_number"
                                   value="{{ old('microchip_number') }}"
                                   placeholder="Enter microchip number if available">
                            @error('microchip_number')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Behavior & Media Tab -->
                    <div class="tab-pane fade hidden" id="behavior" role="tabpanel">
                        <h4 class="text-xl font-semibold mb-4 text-gray-800">Behavior & Media</h4>
                        
                        <!-- Behavioral Traits -->
                        <div class="mb-5">
                            <label for="behavioral_traits" class="block text-sm font-medium text-gray-700 mb-1">
                                Behavioral Traits & Temperament
                            </label>
                            <textarea class="block w-full border border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5" 
                                      id="behavioral_traits" 
                                      name="behavioral_traits" 
                                      rows="3" 
                                      placeholder="E.g., Friendly, Energetic, Good with kids, Loves to play...">{{ old('behavioral_traits') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Separate multiple traits with commas</p>
                            @error('behavioral_traits')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Profile Picture -->
                        <div class="mb-5">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">
                                Profile Picture<span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   class="block w-full text-sm text-gray-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-full file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-orange-50 file:text-orange-700
                                       hover:file:bg-orange-100 p-1 border border-gray-300 rounded-lg" 
                                   id="profile_picture" 
                                   name="profile_picture" 
                                   accept="image/*" 
                                   required>
                            @error('profile_picture')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Additional Photos -->
                        <div class="bg-gray-50 p-4 rounded-lg mt-4">
                            <h3 class="text-lg font-semibold mb-2">Additional Photos</h3>
                            <p class="text-sm text-gray-600 mb-3">Add more photos to showcase your pet</p>
                            <div id="additional_photos_preview" class="grid grid-cols-3 gap-3 mb-3"></div>
                            <label class="w-24 h-24 border-2 border-dashed border-gray-300 hover:border-orange-400 flex items-center justify-center text-3xl text-gray-400 cursor-pointer rounded-xl hover:bg-orange-50 transition-colors">
                                +
                                <input type="file" 
                                       id="additional_photos_input" 
                                       name="additional_photos[]" 
                                       accept="image/*" 
                                       multiple 
                                       class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="my-2">
                
                <!-- Validation Message -->
                <div id="validationMessage" class="text-red-700 bg-red-100 border border-red-300 p-3 rounded-lg font-bold text-center mb-4 hidden text-sm md:text-base"></div>

                <!-- Form Navigation Buttons -->
                <div class="flex justify-between pt-4 relative z-10 transition-shadow duration-300" id="form-footer">
                    <button type="button" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 transition-colors" 
                            id="prevBtn" 
                            disabled>
                        <i class="bi bi-arrow-left mr-2"></i> Previous
                    </button>
                    <button type="submit" id="finalSubmitBtn"
                            class="hidden inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        Submit
                    </button>
                    <button type="button" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 transition-colors" 
                            id="nextBtn">
                        Next <i class="bi bi-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/petform.js') }}"></script>
    <script>
        window.breedsData = @json($breeds);
    </script>
</body>
</html>