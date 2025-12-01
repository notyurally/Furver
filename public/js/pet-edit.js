// pet-edit.js

console.log('Pet edit JS loaded!');

// --- Custom Confirmation Modal Logic ---
const deleteModal = document.getElementById('delete-modal');
const openDeleteBtn = document.getElementById('open-delete-modal-btn');
const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
const deleteForm = document.getElementById('delete-form');

const openDeleteModal = () => {
    deleteModal.classList.remove('hidden');
};

const closeDeleteModal = () => {
    deleteModal.classList.add('hidden');
};

// Click outside to close modal
if (deleteModal) {
    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
}

if (openDeleteBtn) {
    openDeleteBtn.addEventListener('click', openDeleteModal);
}

if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', () => {
        deleteForm.submit();
    });
}

// --- Behavioral Traits Tag Management Logic ---
const traitsContainer = document.getElementById('traits-container');
const newTraitInput = document.getElementById('new-trait-input');
const traitsHiddenInput = document.getElementById('traits-input');

// Function to render all trait tags based on the hidden input value
const renderTraits = () => {
    if (!traitsContainer || !traitsHiddenInput || !newTraitInput) {
        console.error('Traits elements not found');
        return;
    }

    // Clear existing tags (but keep the hidden input and new trait input)
    const inputElements = [traitsHiddenInput, newTraitInput];
    traitsContainer.innerHTML = '';
    inputElements.forEach(input => traitsContainer.appendChild(input));

    const traits = traitsHiddenInput.value.split(',').filter(t => t.trim() !== '');

    traits.forEach(trait => {
        const tagElement = document.createElement('span');
        tagElement.className = 'trait-tag px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium shadow-sm flex items-center';
        tagElement.dataset.trait = trait.trim();
        tagElement.innerHTML = `
            ${trait.trim()}
            <button type="button" class="ml-2 text-blue-600 hover:text-blue-900 leading-none remove-trait-btn" aria-label="Remove trait">&times;</button>
        `;
        traitsContainer.insertBefore(tagElement, newTraitInput);
    });

    attachRemoveListeners();
};

// Function to remove a trait
const removeTrait = (traitToRemove) => {
    let traits = traitsHiddenInput.value.split(',').map(t => t.trim()).filter(t => t !== '');
    traits = traits.filter(trait => trait !== traitToRemove);
    traitsHiddenInput.value = traits.join(',');
    renderTraits();
};

// Function to add a trait
const addTrait = () => {
    const newTrait = newTraitInput.value.trim();
    if (newTrait && !traitsHiddenInput.value.split(',').map(t => t.trim()).includes(newTrait)) {
        let traits = traitsHiddenInput.value.split(',').map(t => t.trim()).filter(t => t !== '');
        traits.push(newTrait);
        traitsHiddenInput.value = traits.join(',');
        newTraitInput.value = '';
        renderTraits();
    }
};

// Event listeners for traits
if (newTraitInput) {
    newTraitInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addTrait();
        }
    });

    newTraitInput.addEventListener('blur', addTrait);
}

// Event delegation for removing traits
const attachRemoveListeners = () => {
    traitsContainer.querySelectorAll('.remove-trait-btn').forEach(button => {
        button.onclick = (e) => {
            const tag = e.target.closest('.trait-tag');
            if (tag) {
                removeTrait(tag.dataset.trait);
            }
        };
    });
};

// --- Image Preview Logic (for new profile photo upload) ---
const profilePhotoInput = document.getElementById('profile_photo');
const profilePreview = document.getElementById('profile-preview');

if (profilePhotoInput && profilePreview) {
    profilePhotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
}

// --- Additional Photos Management ---
let deletedPhotoIds = [];
let newPhotosDataTransfer = new DataTransfer();

// Delete existing photo from server
window.deleteExistingPhoto = function(photoId) {
    console.log('Attempting to delete photo:', photoId);
    
    if (confirm('Are you sure you want to delete this photo?')) {
        // Add to deleted list
        deletedPhotoIds.push(photoId);
        document.getElementById('deleted_photos').value = deletedPhotoIds.join(',');
        
        console.log('Deleted photos list:', deletedPhotoIds);
        
        // Remove from UI
        const photoElement = document.querySelector(`[data-photo-id="${photoId}"]`);
        if (photoElement) {
            photoElement.remove();
            console.log('Photo removed from UI');
        } else {
            console.error('Photo element not found for ID:', photoId);
        }
    }
};

// Preview newly added photos
window.previewNewPhotos = function(event) {
    console.log('Preview new photos called');
    const files = event.target.files;
    console.log('Files selected:', files.length);
    
    const previewContainer = document.getElementById('new-photos-preview');
    if (!previewContainer) {
        console.error('Preview container not found!');
        return;
    }
    
    // Add new files to DataTransfer
    for (let file of files) {
        newPhotosDataTransfer.items.add(file);
    }
    
    // Update the input with all files
    document.getElementById('additional_photos_input').files = newPhotosDataTransfer.files;
    
    console.log('Total files in DataTransfer:', newPhotosDataTransfer.files.length);
    
    // Render all previews
    renderNewPhotoPreviews();
};

// Render preview of new photos
function renderNewPhotoPreviews() {
    console.log('Rendering new photo previews');
    const previewContainer = document.getElementById('new-photos-preview');
    if (!previewContainer) {
        console.error('Preview container not found!');
        return;
    }
    
    previewContainer.innerHTML = '';
    
    const files = Array.from(newPhotosDataTransfer.files);
    console.log('Files to preview:', files.length);
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'relative group';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-32 object-cover rounded-lg shadow-md';
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '<i class="bi bi-trash-fill text-sm"></i>';
            removeBtn.className = 'absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-700 shadow-lg';
            
            removeBtn.addEventListener('click', function() {
                console.log('Removing photo at index:', index);
                removeNewPhoto(index);
            });
            
            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            previewContainer.appendChild(wrapper);
            
            console.log('Preview added for file:', file.name);
        };
        reader.readAsDataURL(file);
    });
}

// Remove a newly added photo before submission
function removeNewPhoto(index) {
    console.log('Removing new photo at index:', index);
    const newDataTransfer = new DataTransfer();
    const files = Array.from(newPhotosDataTransfer.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            newDataTransfer.items.add(file);
        }
    });
    
    newPhotosDataTransfer = newDataTransfer;
    document.getElementById('additional_photos_input').files = newPhotosDataTransfer.files;
    
    console.log('Files remaining:', newPhotosDataTransfer.files.length);
    
    renderNewPhotoPreviews();
}

// Initial render and setup
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded');
    renderTraits();
    
    // Check if all required elements exist
    console.log('Profile photo input:', profilePhotoInput ? 'Found' : 'NOT FOUND');
    console.log('Additional photos input:', document.getElementById('additional_photos_input') ? 'Found' : 'NOT FOUND');
    console.log('New photos preview:', document.getElementById('new-photos-preview') ? 'Found' : 'NOT FOUND');
    console.log('Deleted photos input:', document.getElementById('deleted_photos') ? 'Found' : 'NOT FOUND');
});