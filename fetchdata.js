// fetchData.js
document.addEventListener("DOMContentLoaded", function () {
  const url = "https://raw.githubusercontent.com/kanereroma2343/acdata/refs/heads/main/acdata.json";  // Replace with your actual JSON file URL

  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log(data); // This logs the fetched JSON data to the console
      // You can now use 'data' to dynamically display or process it on your webpage.
    })
    .catch(error => {
      console.error('There has been a problem with your fetch operation:', error);
    });
});
