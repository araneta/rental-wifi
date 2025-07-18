
const base_url = 'http://localhost:8080/api';
export async function apiFetch(url, options = {}) {
 console.log('api;');	
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: 'Bearer ' + token } : {})
  };
  const res = await fetch(base_url+url, { ...options, headers });
  if (!res.ok) throw new Error('API error');
  return res.json();
}
