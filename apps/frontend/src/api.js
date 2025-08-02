import { useToast } from 'vue-toastification';
const toast = useToast();

const base_api_url = import.meta.env.VITE_API_BASE_URL;
const base_auth_url = import.meta.env.VITE_AUTH_BASE_URL;


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
      
      if(fullUrl.indexOf('/login')==1){
		  toast.error('Session expired — please log in again.');
		window.location.href = '/login';
	  }
      return;
    }

    const isJson = res.headers.get('content-type')?.includes('application/json');

    if (!res.ok) {
      const errorBody = isJson ? await res.json().catch(() => ({})) : await res.text();
      console.error('API Error Response:', res.status, errorBody.error);

      toast.error(
        `API Error ${res.status}: ` +
        (typeof errorBody === 'string' ? errorBody : (errorBody.error || res.statusText))
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
     window.location.href = '/login';
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

export async function downloadExcel(url, name){
	console.log('url',url);
	const urlx = base_api_url + url;
	try {
	  const token = localStorage.getItem('token');

	  const response = await fetch(
		urlx,
		{
		  method: 'GET',
		  headers: {
			'Authorization': token ? `Bearer ${token}` : '',
		  }
		}
	  );

	  if (!response.ok) {
		throw new Error('Download failed');
	  }

	  const blob = await response.blob();
	  const url = window.URL.createObjectURL(blob);
	  const link = document.createElement('a');
	  link.href = url;
	  link.download = name+'.xlsx';
	  document.body.appendChild(link);
	  link.click();
	  document.body.removeChild(link);
	  window.URL.revokeObjectURL(url);

	  toast.success('File berhasil diunduh.');
	} catch (err) {
	  console.error(err);
	  toast.error('Gagal mengunduh file Excel.');
	}
}
