(function () {
    const chunkSize = 5;

    class ProgressBar {
        elProgressBar;
        rowsDone = 0;

        constructor() {
            this.elProgressBar = document.getElementById('progress-bar');
        }

        update(progress) {
            this.elProgressBar.style.width = progress + '%';
            this.elProgressBar.setAttribute('aria-valuenow', progress);
        }
    }

    // Long Polling function
    function pollForProgress(progressBar, rowsDone = 0) {
        fetch(`index.php?option=com_pim&task=progressdemo.insertRows&format=raw&rowsDone=${rowsDone}&chunkSize=${chunkSize}`)
            .then(response => response.json())
            .then(data => {
                progressBar.update(data.progress);

                // Poll again if not completed
                if (data.progress < 100) {
                    setTimeout(() => pollForProgress(progressBar, data.rowsDone), 100);
                }
            })
            .catch(error => {
                // Handle errors if needed
                console.error('Error:', error);
            });
    }

    // Start polling when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        const button = document.getElementById('show-progress');

        if (button) {
            button.addEventListener('click', e => {
                pollForProgress(new ProgressBar())
            });
        } else {
            pollForProgress(new ProgressBar());
        }
    });
})();
