async function fetchDataFromGitHub() {
    const owner = 'kanereroma2343';
    const repo = 'accdata123';
    const path = 'https://github.com/kanereroma2343/acdata/blob/main/acdata.json';
    
    try {
        const response = await fetch(
            `https://api.github.com/repos/${owner}/${repo}/contents/${path}`
        );
        const data = await response.json();
        // GitHub API returns base64 encoded content
        const content = atob(data.content);
        return JSON.parse(content);
    } catch (error) {
        console.error('Error fetching from GitHub:', error);
    }
}
