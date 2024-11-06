document.addEventListener("DOMContentLoaded", function () {
    const url = "https://raw.githubusercontent.com/kanereroma2343/acdata/refs/heads/main/acdata.json";
    const tableBody = document.getElementById("tableBody");
    const provinceSelect = document.getElementById("provinceSelect");
    const searchInput = document.getElementById("searchInput");
    let allData = [];

    // Fetch and initialize data
    async function fetchData() {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            
            allData = await response.json();
            populateProvinceDropdown();
            displayData(allData);
        } catch (error) {
            console.error('Error fetching data:', error);
            tableBody.innerHTML = '<tr><td colspan="8">Error loading data. Please try again later.</td></tr>';
        }
    }

    // Populate province dropdown
    function populateProvinceDropdown() {
        const provinces = [...new Set(allData.map(item => item.Province))].sort();
        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province;
            option.textContent = province;
            provinceSelect.appendChild(option);
        });
    }

    // Display data in table
    function displayData(data) {
        tableBody.innerHTML = data.map(item => `
            <tr>
                <td>${item.Province || ''}</td>
                <td>${item.AssessmentCenter || ''}</td>
                <td>${item.CenterManager || ''}</td>
                <td>${item.Sector || ''}</td>
                <td>${item.Qualification || ''}</td>
                <td>${item.AccreditationNumber || ''}</td>
                <td>${item.DateOfAccreditation || ''}</td>
                <td>${item.Validity || ''}</td>
            </tr>
        `).join('');
    }

    // Search functionality
    function handleSearch(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.toLowerCase();
        const selectedProvince = provinceSelect.value;

        const filteredData = allData.filter(item => {
            const matchesProvince = !selectedProvince || item.Province === selectedProvince;
            const matchesSearch = !searchTerm || 
                Object.values(item).some(value => 
                    String(value).toLowerCase().includes(searchTerm)
                );
            return matchesProvince && matchesSearch;
        });

        displayData(filteredData);
    }

    // Event listeners
    document.getElementById('searchForm').addEventListener('submit', handleSearch);
    provinceSelect.addEventListener('change', handleSearch);
    searchInput.addEventListener('input', handleSearch);

    // Initial data fetch
    fetchData();
});
