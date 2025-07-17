export async function apiFetch(url, options = {}) {
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: 'Bearer ' + token } : {})
  };
  const res = await fetch(url, { ...options, headers });
  if (!res.ok) throw new Error('API error');
  return res.json();
}
