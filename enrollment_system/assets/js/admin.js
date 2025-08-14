document.addEventListener('DOMContentLoaded', function() {
    // Admin dashboard functionality
    
    // Bulk actions
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionBtn = document.getElementById('bulk-action');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });
    
    // Admin action buttons
    const approveButtons = document.querySelectorAll('.approve-btn');
    const rejectButtons = document.querySelectorAll('.reject-btn');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    approveButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            approveStudent(studentId);
        });
    });
    
    rejectButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            rejectStudent(studentId);
        });
    });
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const recordId = this.dataset.recordId;
            const recordType = this.dataset.recordType;
            deleteRecord(recordId, recordType);
        });
    });
    
    // Data export functionality
    const exportBtn = document.getElementById('export-data');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const format = this.dataset.format || 'csv';
            exportData(format);
        });
    }
});

function updateBulkActionButton() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActionBtn = document.getElementById('bulk-action');
    
    if (bulkActionBtn) {
        bulkActionBtn.disabled = checkedBoxes.length === 0;
        bulkActionBtn.textContent = `Actions (${checkedBoxes.length})`;
    }
}

function approveStudent(studentId) {
    if (!confirm('Are you sure you want to approve this student?')) {
        return;
    }
    
    performAdminAction('approve', studentId, 'Student approved successfully!');
}

function rejectStudent(studentId) {
    const reason = prompt('Please enter a reason for rejection (optional):');
    if (reason === null) return; // User cancelled
    
    if (!confirm('Are you sure you want to reject this student application?')) {
        return;
    }
    
    performAdminAction('reject', studentId, 'Student application rejected.', { reason });
}

function deleteRecord(recordId, recordType) {
    if (!confirm(`Are you sure you want to delete this ${recordType}? This action cannot be undone.`)) {
        return;
    }
    
    performAdminAction('delete', recordId, `${recordType} deleted successfully.`, { type: recordType });
}

function performAdminAction(action, recordId, successMessage, additionalData = {}) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('record_id', recordId);
    
    // Add additional data
    Object.keys(additionalData).forEach(key => {
        formData.append(key, additionalData[key]);
    });
    
    fetch('api/admin_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(successMessage, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Action failed', 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred. Please try again.', 'error');
    });
}

function exportData(format) {
    const exportBtn = document.getElementById('export-data');
    setLoading(exportBtn, true);
    
    fetch(`api/export.php?format=${format}`, {
        method: 'GET'
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `enrollment_data_${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showAlert('Data exported successfully!', 'success');
    })
    .catch(error => {
        showAlert('Export failed. Please try again.', 'error');
    })
    .finally(() => {
        setLoading(exportBtn, false);
    });
}

// Live search functionality
function initLiveSearch() {
    const searchInput = document.getElementById('live-search');
    const searchResults = document.getElementById('search-results');
    
    if (!searchInput || !searchResults) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performLiveSearch(query);
        }, 300);
    });
}

function performLiveSearch(query) {
    fetch(`api/search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search failed:', error);
        });
}

function displaySearchResults(results) {
    const searchResults = document.getElementById('search-results');
    
    if (results.length === 0) {
        searchResults.innerHTML = '<p class="no-results">No results found</p>';
        return;
    }
    
    const html = results.map(result => `
        <div class="search-result-item">
            <h4>${result.title}</h4>
            <p>${result.description}</p>
            <a href="${result.url}" class="btn btn-sm btn-primary">View</a>
        </div>
    `).join('');
    
    searchResults.innerHTML = html;
}

// Initialize live search when page loads
document.addEventListener('DOMContentLoaded', initLiveSearch);