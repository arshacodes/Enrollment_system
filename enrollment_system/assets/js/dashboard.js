document.addEventListener('DOMContentLoaded', function() {
    // Dashboard-specific functionality
    
    // Animated counters for stats
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        if (!isNaN(finalValue)) {
            animateCounter(stat, 0, finalValue, 1000);
        }
    });
    
    // Refresh data functionality
    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshDashboardData();
        });
    }
    
    // Quick enrollment
    const quickEnrollBtns = document.querySelectorAll('.quick-enroll');
    quickEnrollBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const subjectId = this.dataset.subjectId;
            enrollInSubject(subjectId);
        });
    });
});

function animateCounter(element, start, end, duration) {
    const range = end - start;
    let current = start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = current;
        
        if (current == end) {
            clearInterval(timer);
        }
    }, stepTime);
}

function refreshDashboardData() {
    const refreshBtn = document.getElementById('refresh-data');
    setLoading(refreshBtn, true);
    
    fetch('api/dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            updateDashboardStats(data);
            showAlert('Dashboard data refreshed successfully!', 'success');
        })
        .catch(error => {
            showAlert('Failed to refresh data. Please try again.', 'error');
        })
        .finally(() => {
            setLoading(refreshBtn, false);
        });
}

function updateDashboardStats(data) {
    // Update stat cards with new data
    Object.keys(data).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            const oldValue = parseInt(element.textContent);
            const newValue = parseInt(data[key]);
            
            if (oldValue !== newValue) {
                animateCounter(element, oldValue, newValue, 500);
            }
        }
    });
}

function enrollInSubject(subjectId) {
    if (!confirm('Are you sure you want to enroll in this subject?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'enroll');
    formData.append('subject_id', subjectId);
    
    fetch('api/enrollment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Successfully enrolled in subject!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Enrollment failed', 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred. Please try again.', 'error');
    });
}