document.addEventListener('DOMContentLoaded', () => {
    const timerElement = document.getElementById('exam-timer');
    const examForm = document.getElementById('exam-form');
    
    if (timerElement && examForm) {
        let totalSeconds = parseInt(timerElement.getAttribute('data-seconds'), 10);
        
        function updateTimerDisplay() {
            if (totalSeconds <= 0) {
                timerElement.textContent = "00:00";
                timerElement.classList.add('warning');
                // Auto submit
                alert("Time is up! Submitting your exam automatically.");
                // Submit without the confirm dialog from onclick
                const submitBtn = examForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.removeAttribute('onclick'); // remove confirm dialog
                }
                examForm.submit();
                return;
            }
            
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            
            let display = "";
            if (hours > 0) {
                display += (hours < 10 ? "0" : "") + hours + ":";
            }
            display += (minutes < 10 ? "0" : "") + minutes + ":";
            display += (seconds < 10 ? "0" : "") + seconds;
            
            timerElement.textContent = display;
            
            // Add warning class if less than a minute left
            if (totalSeconds < 60) {
                timerElement.classList.add('warning');
            } else {
                timerElement.classList.remove('warning');
            }
            
            totalSeconds--;
        }
        
        // Initial call
        updateTimerDisplay();
        
        // Update every second
        setInterval(updateTimerDisplay, 1000);
    }
});
