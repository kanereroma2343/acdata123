let currentPage = 1;
const itemsPerPage = 10;
let allData = [];

async function fetchData() {
    try {
        const response = await fetch('/data.json');
        allData = await response.json();
        displayData(currentPage);
        setupPagination();
        populateProvinces();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function displayData(page) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';
    
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedData = allData.slice(start, end);
    
    paginatedData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.province}</td>
            <td>${item.assessment_center}</td>
            <td>${item.center_manager}</td>
            <td>${item.sector}</td>
            <td>${item.qualification}</td>
            <td>${item.accreditation_number}</td>
            <td>${item.date_of_accreditation}</td>
            <td>${item.validity}</td>
        `;
        tableBody.appendChild(row);
    });
}

function setupPagination() {
    const totalPages = Math.ceil(allData.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.innerText = i;
        button.classList.add('pagination-button');
        if (i === currentPage) button.classList.add('active');
        
        button.addEventListener('click', () => {
            currentPage = i;
            displayData(currentPage);
            document.querySelectorAll('.pagination-button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        });
        
        pagination.appendChild(button);
    }
}

function populateProvinces() {
    const provinces = [...new Set(allData.map(item => item.province))];
    const provinceSelect = document.getElementById('provinceSelect');
    
    provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        provinceSelect.appendChild(option);
    });
}

// Initialize the data fetch when the page loads
document.addEventListener('DOMContentLoaded', fetchData);

// Handle search form submission
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const selectedProvince = document.getElementById('provinceSelect').value;
    
    allData = originalData.filter(item => {
        const matchesSearch = !searchTerm || 
            item.qualification.toLowerCase().includes(searchTerm) ||
            item.assessment_center.toLowerCase().includes(searchTerm);
            
        const matchesProvince = !selectedProvince || 
            item.province === selectedProvince;
            
        return matchesSearch && matchesProvince;
    });
    
    currentPage = 1;
    displayData(currentPage);
    setupPagination();
}); 
