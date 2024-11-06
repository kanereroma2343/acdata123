let currentPage = 1;
const itemsPerPage = 10;
let allData = [];
let filteredData = [];

async function fetchData() {
    try {
        const response = await fetch('data.json'); // Updated path
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        allData = await response.json();
        filteredData = [...allData];
        return allData;

    } catch (error) {
        console.error('Error fetching data:', error);
        return [];
    }
}

function displayData(page) {
    const tableBody = document.getElementById('tableBody');
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedData = filteredData.slice(start, end);
    
    if (!paginatedData || paginatedData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
        return;
    }

    tableBody.innerHTML = paginatedData.map(item => `
        <tr>
            <td>${item.province || ''}</td>
            <td>${item.assessment_center || ''}</td>
            <td>${item.center_manager || ''}</td>
            <td>${item.sector || ''}</td>
            <td>${item.qualification_title || ''}</td>
            <td>${item.accreditation_number || ''}</td>
            <td>${item.date_accredited || ''}</td>
            <td>${item.valid_until || ''}</td>
        </tr>
    `).join('');
}

function setupPagination() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    // Add previous button
    const prevButton = document.createElement('button');
    prevButton.innerText = '←';
    prevButton.classList.add('pagination-button');
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updateDisplay();
        }
    });
    pagination.appendChild(prevButton);
    
    // Add page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 || 
            i === totalPages || 
            (i >= currentPage - 2 && i <= currentPage + 2)
        ) {
            const button = document.createElement('button');
            button.innerText = i;
            button.classList.add('pagination-button');
            if (i === currentPage) button.classList.add('active');
            
            button.addEventListener('click', () => {
                currentPage = i;
                updateDisplay();
            });
            
            pagination.appendChild(button);
        } else if (
            i === currentPage - 3 || 
            i === currentPage + 3
        ) {
            const ellipsis = document.createElement('span');
            ellipsis.innerText = '...';
            ellipsis.classList.add('pagination-ellipsis');
            pagination.appendChild(ellipsis);
        }
    }
    
    // Add next button
    const nextButton = document.createElement('button');
    nextButton.innerText = '→';
    nextButton.classList.add('pagination-button');
    nextButton.disabled = currentPage === totalPages;
    nextButton.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            updateDisplay();
        }
    });
    pagination.appendChild(nextButton);
}

function populateProvinces() {
    const provinces = [...new Set(allData.map(item => item.province))].sort();
    const provinceSelect = document.getElementById('provinceSelect');
    provinceSelect.innerHTML = '<option value="">All Provinces</option>';
    
    provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        provinceSelect.appendChild(option);
    });
}

function updateDisplay() {
    displayData(currentPage);
    setupPagination();
}

function handleSearch(e) {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedProvince = document.getElementById('provinceSelect').value;
    
    filteredData = allData.filter(item => {
        const matchesSearch = !searchTerm || 
            item.qualification_title.toLowerCase().includes(searchTerm) ||
            item.assessment_center.toLowerCase().includes(searchTerm);
            
        const matchesProvince = !selectedProvince || 
            item.province === selectedProvince;
            
        return matchesSearch && matchesProvince;
    });
    
    currentPage = 1;
    updateDisplay();
}

// Initialize everything when the page loads
async function initialize() {
    await fetchData();
    populateProvinces();
    updateDisplay();
    
    // Add event listeners
    document.getElementById('searchForm').addEventListener('submit', handleSearch);
    document.getElementById('searchInput').addEventListener('input', handleSearch);
    document.getElementById('provinceSelect').addEventListener('change', handleSearch);
}

document.addEventListener('DOMContentLoaded', initialize);
