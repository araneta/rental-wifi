import { useToast } from 'vue-toastification';
const toast = useToast();

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

  let res = null;

  try {
    res = await fetch(fullUrl, { ...options, headers });

    if (res.status === 401) {
      localStorage.clear();
      toast.error('Session expired — please log in again.');
      window.location.href = '/login';
      return;
    }

    const isJson = res.headers.get('content-type')?.includes('application/json');

    if (!res.ok) {
      const errorBody = isJson ? await res.json().catch(() => ({})) : await res.text();
      console.error('API Error Response:', res.status, errorBody);

      toast.error(
        `API Error ${res.status}: ` +
        (typeof errorBody === 'string' ? errorBody : (errorBody.message || res.statusText))
      );

      if (redirectOnFail) {
        // Optional: window.location.href = '/error';
      }

      throw new Error(`API error: ${res.statusText}`);
    }

    return isJson ? await res.json() : await res.text();

  } catch (err) {
    console.error('Fetch failed:', err);

    // Only treat as CORS/network error if no HTTP response was received
    const isCorsOrNetworkError = !res && err instanceof TypeError;

    if (isCorsOrNetworkError) {
      toast.error(
        'Network/CORS error: Could not connect to the server.\n' +
        'Possible causes:\n' +
        '- Server is offline\n' +
        '- CORS is not allowed\n\n' +
        'Check your server or try again later.'
      );
      //window.location.href = '/login';
    } else {
      //toast.error(
        //`Unexpected error: ${err.message || 'Unknown error occurred.'}`
      //);
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
