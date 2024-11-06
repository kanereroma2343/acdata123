async function fetchDataFromGitHub() {
    const owner = 'kanereroma2343';
    const repo = 'acdata';  // Changed to match your actual repo name
    const path = 'acdata.json';  // Just the file name, not the full URL
    
    try {
        const response = await fetch(
            `https://api.github.com/repos/${owner}/${repo}/contents/${path}`,
            {
                headers: {
                    'Accept': 'application/vnd.github.v3.raw'
                }
            }
        );
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        return data;  // No need for atob() when using raw content

    } catch (error) {
        console.error('Error fetching from GitHub:', error);
        return [];
    }
}

// Add this to populate the table
async function populateTable() {
    const data = await fetchDataFromGitHub();
    const tableBody = document.getElementById('tableBody');
    
    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8">No data available</td></tr>';
        return;
    }

    tableBody.innerHTML = data.map(item => `
        <tr>
            <td>${item.province || ''}</td>
            <td>${item.assessmentCenter || ''}</td>
            <td>${item.centerManager || ''}</td>
            <td>${item.sector || ''}</td>
            <td>${item.qualification || ''}</td>
            <td>${item.accreditationNumber || ''}</td>
            <td>${item.dateOfAccreditation || ''}</td>
            <td>${item.validity || ''}</td>
        </tr>
    `).join('');
}

// Call populateTable when the page loads
document.addEventListener('DOMContentLoaded', populateTable);
