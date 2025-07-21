<template>
  <div>
    <h2>Tambah Pelanggan</h2>
    <form @submit.prevent="submitForm">
      <input type="text" v-model="form.nama" placeholder="Nama" required class="form-control mb-2" />

      <textarea v-model="form.alamat" placeholder="Alamat" required class="form-control mb-2"></textarea>

      <input type="text" v-model="form.no_hp" placeholder="No HP" required class="form-control mb-2" />

      <select v-model="form.paket_id" class="form-control mb-2" required>
        <option value="">-- Pilih Paket --</option>
        <option v-for="paket in pakets" :key="paket.id" :value="paket.id">
          {{ paket.nama }} - Rp{{ paket.harga.toLocaleString('id-ID') }}
        </option>
      </select>

      <select v-model="form.status" class="form-control mb-2">
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
      </select>

      <input type="text" v-model="form.pop" placeholder="POP" class="form-control mb-2" />

      <button type="submit" class="btn btn-success">Simpan</button>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { apiFetch } from '../../api' // Your custom API handler
import { useToast } from 'vue-toastification';
const toast = useToast();
export default {
  name: 'CreatePelanggan',
  setup() {
    const form = ref({
      nama: '',
      alamat: '',
      no_hp: '',
      paket_id: '',
      status: 'aktif',
      pop: ''
    })

    const pakets = ref([])

    onMounted(async () => {
      try {
        const response = await apiFetch('/pelanggans', { method: 'GET' })
        pakets.value = response.pakets || []
      } catch (e) {
        console.error('Gagal memuat paket', e)
      }
    })

    const submitForm = async () => {
      try {
        const response = await apiFetch('/pelanggans', {
          method: 'POST',
          body: JSON.stringify(form.value),
          headers: {
            'Content-Type': 'application/json'
          }
        })

        alert('Data berhasil disimpan!')
        // Optionally redirect
      } catch (e) {
        console.error('Gagal menyimpan data', e)
        alert('Gagal menyimpan data.')
      }
    }

    return {
      form,
      pakets,
      submitForm
    }
  }
}
</script>
