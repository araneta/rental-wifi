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
import { useRouter } from 'vue-router'
import { apiFetch } from '../../api'
import { useToast } from 'vue-toastification'

export default {
  name: 'CreatePelanggan',
  setup() {
    const router = useRouter()
    const toast = useToast()

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
        const response = await apiFetch('/pakets', { method: 'GET' })
        pakets.value = response.pakets || []
      } catch (e) {
        console.error('Gagal memuat paket', e)
        toast.error('Gagal memuat data paket.')
      }
    })

    const submitForm = async () => {
      try {
		form.value.paket_id = parseInt(form.value.paket_id);
        await apiFetch('/pelanggans', {
          method: 'POST',
          body: JSON.stringify(form.value),
          headers: {
            'Content-Type': 'application/json'
          }
        })

        toast.success('Data berhasil disimpan!')
        router.push('/pelanggans')
      } catch (e) {
        console.error('Gagal menyimpan data', e)
        toast.error('Gagal menyimpan data.')
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

