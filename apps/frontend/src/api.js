
const base_api_url = 'http://localhost:8080/api';
const base_auth_url = 'http://localhost:8080/auth';
export async function authFetch(url, options = {}) {
 console.log('api;');	
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: 'Bearer ' + token } : {})
  };
  const res = await fetch(base_auth_url+url, { ...options, headers });
  if (!res.ok) throw new Error('API error');
  return res.json();
}

export async function apiFetch(url, options = {}) {
	console.log('api;');	
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: 'Bearer ' + token } : {})
  };
  const res = await fetch(base_api_url+url, { ...options, headers });
  if (!res.ok){
	  localStorage.clear();
	  window.location.href = '/login';
		throw new Error('API error');
   }
  return res.json();
}
