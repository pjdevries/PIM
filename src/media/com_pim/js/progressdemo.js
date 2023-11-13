(function () {
    const chunkSize = 5;
    let rowsDone = 0;

// Update progress bar on message received
    function updateProgressBar(progress) {
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
    }

    // Long Polling function
    function pollForProgress() {
        fetch(`index.php?option=com_pim&task=progressdemo.insertRows&format=raw&rowsDone=${rowsDone}&chunkSize=${chunkSize}`)
            .then(response => response.json())
            .then(data => {
                updateProgressBar(data.progress);
                rowsDone = data.rowsDone;

                // Poll again if not completed
                if (rowsDone < data.totalRows) {
                    setTimeout(pollForProgress, 100);
                }
            })
            .catch(error => {
                // Handle errors if needed
                console.error('Error:', error);
            });
    }

    // Start polling when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        debugger;
        pollForProgress();
    });
})();
