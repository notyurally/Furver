<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - FurEver</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Status pill colors */
        .status-pill-Submitted { background-color: #fef3c7; color: #d97706; }
        .status-pill-Approved { background-color: #d1fae5; color: #059669; }
        .status-pill-Rejected { background-color: #fee2e2; color: #dc2626; }
        
        /* Pet status colors */
        .pet-status-adopted { color: #059669; font-weight: bold; }
        .pet-status-pending { color: #d97706; font-weight: bold; }
        .pet-status-available { color: #3b82f6; font-weight: bold; }

        /* Modal overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hidden-modal { display: none; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pet-orange': '#F97316', 
                        'pet-dark': '#1f2937', 
                        'custom-bg': '#FBF3D5', 
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-custom-bg min-h-screen flex flex-col">
    
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
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900 mb-8 flex items-center">
            <i data-lucide="clipboard-list" class="h-10 w-10 text-pet-orange mr-4"></i>
            Adoption Applications
        </h1>

        <!-- Success/Error Messages -->
        <div id="message-container" class="mb-6 hidden">
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg hidden">
                <span class="font-semibold">Success!</span> <span id="success-text"></span>
            </div>
            <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg hidden">
                <span class="font-semibold">Error!</span> <span id="error-text"></span>
            </div>
        </div>

        <div id="loading-indicator" class="text-center p-12 text-gray-500">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin mx-auto mb-4"></i>
            <p>Loading application data...</p>
        </div>

        <div id="application-table-container" class="bg-white rounded-2xl shadow-xl overflow-x-auto border border-gray-200 hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet & User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="application-list-body" class="bg-white divide-y divide-gray-200">
                    <!-- Applications will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div id="no-applications-message" class="hidden text-center text-xl text-gray-500 p-12 mt-8 border-t-4 border-pet-orange bg-white rounded-xl shadow-lg">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p>No adoption applications at this time.</p>
        </div>
    </main>

    <!-- Details Modal -->
    <div id="details-modal" class="modal-overlay hidden-modal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-xl transform transition-all relative max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="hideDetailsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            <h3 class="text-3xl font-extrabold text-gray-900 mb-4 border-b pb-3">Application Details</h3>
            <div id="modal-details-content" class="text-gray-700 space-y-3 pr-2">
                <!-- Content filled by JavaScript -->
            </div>
            <div class="flex justify-end pt-4 mt-4 border-t">
                <button type="button" onclick="hideDetailsModal()" class="py-2 px-4 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-150">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirm-action-modal" class="modal-overlay hidden-modal" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-sm transform transition-all text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                <i data-lucide="alert-triangle" class="h-6 w-6 text-indigo-600"></i>
            </div>
            <h3 id="modal-confirm-title" class="text-xl font-bold text-gray-900 mb-2">Confirm Action</h3>
            <p id="modal-confirm-message" class="text-sm text-gray-500 mb-6">Are you sure you want to proceed?</p>
            
            <div class="flex justify-center space-x-3">
                <button id="confirm-cancel-btn" type="button" class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition duration-150 shadow-sm">
                    Cancel
                </button>
                <button id="confirm-proceed-btn" type="button" class="flex-1 py-2 px-4 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                    Proceed
                </button>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-400">
            <p>&copy; 2025 FurEver Admin Panel. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Load applications data from Laravel (server-side rendering)
        let allApplications = @json($applications);

        // Helper Functions
        function formatDateTime(isoString) {
            if (!isoString) return 'N/A';
            const date = new Date(isoString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function showMessage(type, text) {
            const container = document.getElementById('message-container');
            const successMsg = document.getElementById('success-message');
            const errorMsg = document.getElementById('error-message');
            
            successMsg.classList.add('hidden');
            errorMsg.classList.add('hidden');
            
            if (type === 'success') {
                document.getElementById('success-text').textContent = text;
                successMsg.classList.remove('hidden');
            } else {
                document.getElementById('error-text').textContent = text;
                errorMsg.classList.remove('hidden');
            }
            
            container.classList.remove('hidden');
            setTimeout(() => container.classList.add('hidden'), 5000);
        }

        // Modal Functions
        let confirmResolver = null;
        
        function showConfirmModal(title, message) {
            return new Promise(resolve => {
                confirmResolver = resolve;
                document.getElementById('modal-confirm-title').textContent = title;
                document.getElementById('modal-confirm-message').textContent = message;
                document.getElementById('confirm-action-modal').classList.remove('hidden-modal');
                lucide.createIcons();
            });
        }

        function hideConfirmModal(result) {
            document.getElementById('confirm-action-modal').classList.add('hidden-modal');
            if (confirmResolver) {
                confirmResolver(result);
                confirmResolver = null;
            }
        }

        function showDetailsModal(applicationId) {
            const application = allApplications.find(app => app.id === applicationId);
            if (!application) return;
            
            const petStatusClass = `pet-status-${application.pet_status.toLowerCase()}`;
            const content = document.getElementById('modal-details-content');
            
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Application ID</p>
                            <p class="font-semibold">${application.id}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="status-pill-${application.application_status.replace(/\s/g, '')} px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">${application.application_status}</span>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-1">Pet Information</p>
                        <p><strong>Pet ID:</strong> <span class="text-pet-orange font-bold">${application.pet_id}</span></p>
                        <p><strong>Pet Name:</strong> ${application.pet_name}</p>
                        <p><strong>Pet Status:</strong> <span class="${petStatusClass} uppercase">${application.pet_status}</span></p>
                    </div>
                    
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-1">Applicant Information</p>
                        <p><strong>User ID:</strong> ${application.user_id}</p>
                        <p><strong>Name:</strong> ${application.user_name}</p>
                        <p><strong>Mobile:</strong> <a href="tel:${application.mobile_number}" class="text-indigo-600 hover:underline">${application.mobile_number}</a></p>
                        <p><strong>Address:</strong> ${application.address}</p>
                    </div>
                    
                    <div class="border-t pt-4">
                        <p class="font-bold mb-2 text-lg">Adoption Rationale</p>
                        <p class="whitespace-pre-wrap italic text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">${application.description}</p>
                    </div>
                    
                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500">Submitted: ${formatDateTime(application.application_date)}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('details-modal').classList.remove('hidden-modal');
            lucide.createIcons();
        }

        window.hideDetailsModal = function() {
            document.getElementById('details-modal').classList.add('hidden-modal');
        }

        // Application Management Functions
        async function handleApproval(applicationId) {
            const application = allApplications.find(app => app.id === applicationId);
            if (!application) return;
            
            const confirmed = await showConfirmModal(
                'Approve Adoption Application',
                `Are you sure you want to approve the application for ${application.pet_name} (${application.pet_id})? This will mark the pet as ADOPTED.`
            );

            if (!confirmed) return;
            
            try {
                const response = await fetch(`/admin/applications/${applicationId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update local data
                    application.application_status = 'Approved';
                    application.pet_status = 'adopted';
                    
                    populateApplicationList();
                    showMessage('success', data.message);
                } else {
                    showMessage('error', data.message);
                }
            } catch (error) {
                showMessage('error', 'Failed to approve application. Please try again.');
                console.error('Error:', error);
            }
        }

        async function handleReject(applicationId) {
            const application = allApplications.find(app => app.id === applicationId);
            if (!application) return;

            const confirmed = await showConfirmModal(
                'Reject Adoption Application',
                `Are you sure you want to reject the application for ${application.pet_name} (${application.pet_id})? The pet will be marked as AVAILABLE again.`
            );

            if (!confirmed) return;
            
            try {
                const response = await fetch(`/admin/applications/${applicationId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update local data
                    application.application_status = 'Rejected';
                    application.pet_status = 'available';
                    
                    populateApplicationList();
                    showMessage('success', data.message);
                } else {
                    showMessage('error', data.message);
                }
            } catch (error) {
                showMessage('error', 'Failed to reject application. Please try again.');
                console.error('Error:', error);
            }
        }

        // Render Applications Table
        function populateApplicationList() {
            const tbody = document.getElementById('application-list-body');
            const noAppsMessage = document.getElementById('no-applications-message');
            const tableContainer = document.getElementById('application-table-container');
            
            tbody.innerHTML = '';

            if (allApplications.length === 0) {
                noAppsMessage.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                return;
            }

            noAppsMessage.classList.add('hidden');
            tableContainer.classList.remove('hidden');

            allApplications.sort((a, b) => new Date(b.application_date) - new Date(a.application_date));

            allApplications.forEach(app => {
                const statusClass = `status-pill-${app.application_status.replace(/\s/g, '')}`;
                
                let actionButtons = '';
                if (app.application_status === 'Submitted') {
                    actionButtons = `
                        <button onclick="handleApproval(${app.id})" 
                            class="px-3 py-1 text-xs font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 shadow-md mr-2">
                            <i data-lucide="check" class="w-3 h-3 inline-block mr-1"></i> Approve
                        </button>
                        <button onclick="handleReject(${app.id})" 
                            class="px-3 py-1 text-xs font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
                            <i data-lucide="x" class="w-3 h-3 inline-block mr-1"></i> Reject
                        </button>
                    `;
                } else {
                    actionButtons = '<span class="text-gray-400 text-xs italic">No Action Needed</span>';
                }

                const row = `
                    <tr class="hover:bg-orange-50 transition duration-100">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">${app.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <p class="font-bold text-gray-900">${app.pet_name} <span class="text-pet-orange">(${app.pet_id})</span></p>
                            <p class="text-xs text-gray-500">${app.user_name} (${app.user_id})</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <p class="truncate w-40">${app.address.split(',')[0]}</p>
                            <button onclick="showDetailsModal(${app.id})" class="text-indigo-500 hover:text-indigo-700 text-xs mt-1 font-medium">View Details</button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDateTime(app.application_date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="${statusClass} px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm">${app.application_status}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            ${actionButtons}
                        </td>
                    </tr>
                `;
                
                tbody.innerHTML += row;
            });

            lucide.createIcons();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('confirm-proceed-btn').onclick = () => hideConfirmModal(true);
            document.getElementById('confirm-cancel-btn').onclick = () => hideConfirmModal(false);
            
            // Simulate loading then show data
            setTimeout(() => {
                document.getElementById('loading-indicator').classList.add('hidden');
                populateApplicationList();
            }, 500);
        });
    </script>
</body>
</html>