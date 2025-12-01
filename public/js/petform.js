// public/js/petform.js

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const selectionScreen = document.getElementById('selection-screen');
    const formContainer = document.getElementById('form-container');
    const petTypeInput = document.getElementById('pet_type_id');
    const formTitle = document.getElementById('form-title');
    const backToSelectionBtn = document.getElementById('back-to-selection');
    const breedSelect = document.getElementById('breed_id');

    const tabPanes = document.querySelectorAll('#petTabContent .tab-pane');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    const form = document.querySelector('form');

    const stepIndicators = [
        document.getElementById('step-1'),
        document.getElementById('step-2'),
        document.getElementById('step-3')
    ];
    const progressBarFill = document.getElementById('progressBarFill');
    const validationMessage = document.getElementById('validationMessage');

    // Health fields
    const isVaccinatedCheckbox = document.getElementById('is_vaccinated');
    const vaccinationDateField = document.getElementById('vaccination_date_field');
    const isSpayedCheckbox = document.getElementById('is_spayed');
    const spayDateField = document.getElementById('spay_date_field');

    // State
    let currentTab = 0;
    let allBreeds = window.breedsData || [];

    const addInput = document.getElementById("additional_photos_input");
    const previewContainer = document.getElementById("additional_photos_preview");

    let dataTransfer = new DataTransfer();

    // ===========================
    // Category Selection
    // ===========================
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', () => {
            const petTypeId = card.getAttribute('data-pet-type');
            const petTypeName = card.getAttribute('data-pet-type-name');
            
            petTypeInput.value = petTypeId;
            formTitle.innerHTML = `Create New <span class="text-orange-600">${petTypeName}</span> Profile`;

            // Filter breeds by pet type
            filterBreedsByPetType(petTypeId);

            selectionScreen.classList.add('hidden');
            formContainer.classList.remove('hidden');

            currentTab = 0;
            updateTab();
        });
    });

    // ===========================
    // Breed Filtering
    // ===========================
    function filterBreedsByPetType(petTypeId) {
        breedSelect.innerHTML = '<option value="">Select Breed</option>';
        
        const filteredBreeds = allBreeds.filter(breed => breed.pet_types_id == petTypeId);
        
        filteredBreeds.forEach(breed => {
            const option = document.createElement('option');
            option.value = breed.id;
            option.textContent = breed.name;
            breedSelect.appendChild(option);
        });
    }

    // ===========================
    // Back to Selection
    // ===========================
    if (backToSelectionBtn) {
        backToSelectionBtn.addEventListener('click', (e) => {
            e.preventDefault();
            formContainer.classList.add('hidden');
            selectionScreen.classList.remove('hidden');
            petTypeInput.value = '';
            form.reset();
            
            // Clear DataTransfer and preview
            dataTransfer = new DataTransfer();
            if (addInput) addInput.files = dataTransfer.files;
            if (previewContainer) previewContainer.innerHTML = '';
            
            // Reset to first tab
            currentTab = 0;
            updateTab();
        });
    }

    // ===========================
    // Tab Navigation
    // ===========================
    function updateTab() {
        tabPanes.forEach((pane, i) => {
            pane.classList.toggle('hidden', i !== currentTab);
            if (i === currentTab) {
                pane.classList.add('show', 'active');
            } else {
                pane.classList.remove('show', 'active');
            }
        });

        stepIndicators.forEach((step, i) => {
            const circle = step.querySelector('div');
            const label = step.querySelector('span');

            circle.classList.remove('bg-orange-600', 'bg-green-500', 'bg-gray-300', 'ring-4', 'ring-orange-200');
            label.classList.remove('text-orange-600', 'font-bold', 'text-gray-500', 'text-gray-700');

            if (i < currentTab) {
                // Completed step
                circle.classList.add('bg-green-500');
                circle.innerHTML = '<i class="bi bi-check-lg text-xl"></i>';
                label.classList.add('text-gray-700');
            } else if (i === currentTab) {
                // Current step
                circle.classList.add('bg-orange-600', 'ring-4', 'ring-orange-200');
                label.classList.add('text-orange-600', 'font-bold');
                
                // Set appropriate icon
                if (i === 0) circle.innerHTML = '<i class="fa fa-paw text-lg"></i>';
                if (i === 1) circle.innerHTML = '<i class="bi bi-heart text-lg"></i>';
                if (i === 2) circle.innerHTML = '<i class="bi bi-emoji-smile text-lg"></i>';
            } else {
                // Future step
                circle.classList.add('bg-gray-300');
                label.classList.add('text-gray-500');
                
                // Set appropriate icon
                if (i === 0) circle.innerHTML = '<i class="fa fa-paw text-lg"></i>';
                if (i === 1) circle.innerHTML = '<i class="bi bi-heart text-lg"></i>';
                if (i === 2) circle.innerHTML = '<i class="bi bi-emoji-smile text-lg"></i>';
            }
        });

        // Update progress bar
        const widths = ['0%', '50%', '100%'];
        progressBarFill.style.width = widths[currentTab];

        // Update buttons visibility
        prevBtn.disabled = currentTab === 0;
        
        if (currentTab === 2) {
            // Last step - show submit button, hide next
            nextBtn.classList.add('hidden');
            finalSubmitBtn.classList.remove('hidden');
        } else {
            // Other steps - show next button, hide submit
            nextBtn.classList.remove('hidden');
            finalSubmitBtn.classList.add('hidden');
        }
    }

    // ===========================
    // Form Validation
    // ===========================
    function validateTab() {
        const currentPane = tabPanes[currentTab];
        const requiredInputs = currentPane.querySelectorAll('[required]');
        let isValid = true;
        let errorMessage = '';

        for (let input of requiredInputs) {
            // File input validation
            if (input.type === 'file' && input.files.length === 0) {
                errorMessage = `Please upload ${input.name === 'profile_picture' ? 'a profile picture' : 'required files'}.`;
                isValid = false;
                break;
            }
            
            // Select validation
            if (input.tagName === 'SELECT' && (!input.value || input.value === '')) {
                errorMessage = `Please select ${input.labels[0]?.textContent.replace('*', '').trim() || 'an option'}.`;
                isValid = false;
                break;
            }
            
            // Text/number input validation
            if (input.type !== 'file' && input.type !== 'checkbox' && input.value.trim() === '') {
                errorMessage = `Please fill out ${input.labels[0]?.textContent.replace('*', '').trim() || 'all required fields'}.`;
                isValid = false;
                break;
            }
        }

        if (!isValid) {
            showValidation(errorMessage);
            return false;
        }

        hideValidation();
        return true;
    }

    function showValidation(msg) {
        validationMessage.textContent = msg;
        validationMessage.classList.remove('hidden');
        
        // Scroll to validation message
        validationMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideValidation() {
        validationMessage.classList.add('hidden');
    }

    // ===========================
    // Next/Previous Buttons
    // ===========================
    nextBtn.addEventListener('click', () => {
        if (!validateTab()) return;

        if (currentTab < 2) {
            currentTab++;
            updateTab();
            
            // Scroll to top of form
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentTab > 0) {
            currentTab--;
            updateTab();
            hideValidation(); // Hide validation when going back
            
            // Scroll to top of form
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // ===========================
// Auto-Calculate Age from Birthdate
// ===========================
    window.calculateAge = function() {
        const birthdateInput = document.getElementById('birthdate');
        const ageInput = document.getElementById('age');
        const ageDataSelect = document.getElementById('age_data');
        
        if (!birthdateInput.value) {
            ageInput.value = '';
            ageDataSelect.disabled = true;
            return;
        }
        
        const birthdate = new Date(birthdateInput.value);
        const today = new Date();
        
        // Calculate difference in months
        let months = (today.getFullYear() - birthdate.getFullYear()) * 12;
        months -= birthdate.getMonth();
        months += today.getMonth();
        
        // Adjust if the day hasn't occurred yet this month
        if (today.getDate() < birthdate.getDate()) {
            months--;
        }
        
        // Determine whether to show in years or months
        if (months >= 12) {
            // Show in years
            const years = Math.floor(months / 12);
            ageInput.value = years;
            ageDataSelect.value = 'years';
        } else {
            // Show in months
            ageInput.value = months;
            ageDataSelect.value = 'months';
        }
        
        // Enable the select
        ageDataSelect.disabled = false;
    };

    // Calculate age on page load if birthdate exists
    document.addEventListener('DOMContentLoaded', () => {
        // ... your existing code ...
        
        const birthdateInput = document.getElementById('birthdate');
        if (birthdateInput && birthdateInput.value) {
            calculateAge();
        }
    });

    // ===========================
    // Final Submit Button
    // ===========================
    if (finalSubmitBtn) {
        finalSubmitBtn.addEventListener('click', (e) => {
            if (!validateTab()) {
                e.preventDefault();
                return;
            }
            // If validation passes, form will submit normally
        });
    }

    // ===========================
    // Health Status Toggle Fields
    // ===========================
    if (isVaccinatedCheckbox && vaccinationDateField) {
        function toggleVaccinationDate() {
            if (isVaccinatedCheckbox.checked) {
                vaccinationDateField.style.display = 'block';
            } else {
                vaccinationDateField.style.display = 'none';
                const dateInput = document.getElementById('last_vaccinated_date');
                if (dateInput) dateInput.value = '';
            }
        }
        
        isVaccinatedCheckbox.addEventListener('change', toggleVaccinationDate);
        toggleVaccinationDate(); // Initial state
    }

    if (isSpayedCheckbox && spayDateField) {
        function toggleSpayDate() {
            if (isSpayedCheckbox.checked) {
                spayDateField.style.display = 'block';
            } else {
                spayDateField.style.display = 'none';
                const dateInput = document.getElementById('last_spay_date');
                if (dateInput) dateInput.value = '';
            }
        }
        
        isSpayedCheckbox.addEventListener('change', toggleSpayDate);
        toggleSpayDate(); // Initial state
    }

    // ===========================
    // Additional Photos Upload
    // ===========================
    if (addInput && previewContainer) {
        addInput.addEventListener("change", function () {
            // Add new files to DataTransfer
            for (let file of addInput.files) {
                dataTransfer.items.add(file);
            }

            // Assign back to the input
            addInput.files = dataTransfer.files;

            updatePreviews();
        });
    }

    // ===========================
    // Update Preview Images
    // ===========================
    function updatePreviews() {
        previewContainer.innerHTML = "";

        Array.from(addInput.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = document.createElement("div");
                wrapper.className = "relative w-24 h-24";

                const img = document.createElement("img");
                img.src = e.target.result;
                img.className = "w-full h-full object-cover rounded-lg border-2 border-gray-200";

                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "×";
                removeBtn.type = "button";
                removeBtn.className =
                    "absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700 transition-colors";

                removeBtn.addEventListener("click", function () {
                    // Create new DataTransfer without the removed file
                    const newDataTransfer = new DataTransfer();
                    Array.from(addInput.files).forEach((f, i) => {
                        if (i !== index) {
                            newDataTransfer.items.add(f);
                        }
                    });

                    // Update global dataTransfer and input files
                    dataTransfer = newDataTransfer;
                    addInput.files = dataTransfer.files;

                    // Refresh UI
                    updatePreviews();
                });

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        });
    }

    // ===========================
    // Profile Picture Preview
    // ===========================
    const profilePictureInput = document.getElementById('profile_picture');
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                console.log('Profile picture selected:', file.name);
                // Optional: Add visual preview here if needed
            }
        });
    }


    // Initialize the form state
    updateTab();
});