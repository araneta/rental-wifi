const base_api_url = 'http://localhost:8080/api';
const base_auth_url = 'http://localhost:8080/auth';

/**
 * Utility to perform fetch with error handling and token support
 */
async function handleFetch(fullUrl, options = {}, redirectOnFail = false) {
  const token = localStorage.getItem('token');
  const headers = {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: 'Bearer ' + token } : {})
  };

  try {
    const res = await fetch(fullUrl, { ...options, headers });

    if (!res.ok) {
      const errorText = await res.text().catch(() => '');
      console.error('API Error Response:', res.status, errorText);

      alert(`API Error ${res.status}: ${errorText || res.statusText}`);

      if (redirectOnFail) {
        localStorage.clear();
        window.location.href = '/login';
      }

      throw new Error(`API error: ${res.status}`);
    }

    return await res.json();
  } catch (err) {
    console.error('Fetch failed:', err);
    alert(`Network or server error: ${err.message}`);

    if (redirectOnFail) {
      localStorage.clear();
      window.location.href = '/login';
    }

    throw err;
  }
}

export async function authFetch(url, options = {}) {
  return handleFetch(base_auth_url + url, options, false);
}

export async function apiFetch(url, options = {}) {
  return handleFetch(base_api_url + url, options, true);
}
