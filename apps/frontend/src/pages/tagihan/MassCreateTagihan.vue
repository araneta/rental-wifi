<template>
  <div>
    <h2>Tambah Tagihan Massal</h2>
    <h6>Menu ini akan menambahkan tagihan ke semua pelanggan berdasarkan paket yang diambil</h6>

    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label for="bulan_tahun" class="form-label">Pilih Bulan & Tahun</label>
        <input type="month" v-model="form.bulan_tahun" required class="form-control" />
      </div>

      <div class="mb-3">
        <label for="petugas_id" class="form-label">Pilih Petugas</label>
        <select v-model="form.petugas_id" required class="form-control">
          <option value="">-- Pilih Petugas --</option>
          <option v-for="p in petugasList" :key="p.id" :value="p.id">
            {{ p.name }}
          </option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Tambah Tagihan</button>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { apiFetch } from '../../api'
import { useRouter } from 'vue-router'
export default {
  name: 'MassCreateTagihan',
  setup() {
    const toast = useToast()
    const router = useRouter()
    
    const form = ref({
      bulan_tahun: '',
      petugas_id: ''
    })

    const petugasList = ref([])

    const loadPetugas = async () => {
      try {
        const res = await apiFetch('/users?role=petugas')
        petugasList.value = res.users || []
      } catch (err) {
        toast.error('Gagal memuat data petugas')
      }
    }

    const submitForm = async () => {
      try {
		form.value.petugas_id = parseInt(form.value.petugas_id);
        const res = await apiFetch('/tagihans/mass', {
          method: 'POST',
          body: JSON.stringify(form.value),
          headers: {
            'Content-Type': 'application/json'
          }
        })

        if (res.count > 0) {
          toast.success(`Berhasil menambahkan tagihan untuk ${res.count} pelanggan.`)
          router.push('/tagihans')
        } else {
          toast.warning('Semua pelanggan sudah memiliki tagihan untuk bulan tersebut.')
        }
      } catch (err) {
        toast.error('Gagal membuat tagihan massal')
        console.error(err)
      }
    }

    onMounted(loadPetugas)

    return {
      form,
      petugasList,
      submitForm
    }
  }
}
</script>
