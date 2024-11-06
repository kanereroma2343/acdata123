document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = 'https://raw.githubusercontent.com/kanereroma2343/acdata/refs/heads/main/acdata.json'; // Replace with your actual GitHub raw URL

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            displayData(data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
});

function displayData(data) {
    const dataContainer = document.getElementById('dataContainer');
    data.forEach(item => {
        const div = document.createElement('div');
        div.textContent = JSON.stringify(item); // Modify this line to display data in a more readable format
        dataContainer.appendChild(div);
    });
}
