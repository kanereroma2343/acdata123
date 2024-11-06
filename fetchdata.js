document.addEventListener('DOMContentLoaded', function() {
    const apiUrl = 'https://raw.githubusercontent.com/kanereroma2343/acdata/refs/heads/main/acdata.json'; // Your raw data URL
    const dataContainer = document.getElementById('dataContainer'); // The container where data will be displayed

    // Fetch the data from the raw GitHub link
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Check the data format in the console for debugging

            // If the data is an array, iterate over it and display each item
            if (data && Array.isArray(data)) {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.textContent = JSON.stringify(item, null, 2); // Display the item as a nicely formatted JSON string
                    dataContainer.appendChild(div); // Append to the container in HTML
                });
            } else {
                console.error('Data format is incorrect');
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
});
