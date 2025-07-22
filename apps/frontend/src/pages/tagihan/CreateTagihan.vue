<template>
  <div>
    <h2>Tambah Tagihan</h2>
    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
        <select v-model="form.pelanggan_id" @change="updateJumlah" required class="form-control">
          <option value="">-- Pilih Pelanggan --</option>
          <option v-for="p in pelangganList" :key="p.id" :value="p.id" :data-harga="p.paket_harga">
            {{ p.nama }} (Paket: {{ p.paket_nama }} - Rp{{ p.paket_harga.toLocaleString('id-ID') }})
          </option>
        </select>
      </div>

      <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah Tagihan (Rp)</label>
        <input type="number" v-model="form.jumlah" placeholder="Jumlah Tagihan" required class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select v-model="form.status" class="form-control">
          <option value="belum bayar">Belum Bayar</option>
          <option value="dibayar">Lunas</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
        <input type="date" v-model="form.tanggal_bayar" class="form-control">
      </div>

      <div class="mb-3">
        <label for="bulan_tahun" class="form-label">Bulan/Tahun</label>
        <input type="month" v-model="form.bulan_tahun" required class="form-control">
      </div>

      <div class="mb-3">
        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
        <select v-model="form.metode_pembayaran" class="form-control">
          <option value=""></option>
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer</option>
          <option value="QRIS">QRIS</option>
        </select>
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

      <button type="submit" class="btn btn-success">Simpan</button>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { apiFetch } from '../../api'
import { useRouter } from 'vue-router'
export default {
  name: 'CreateTagihan',
  setup() {
    const toast = useToast()
    const router = useRouter()
    
    const pelangganList = ref([])
    const petugasList = ref([])
    const form = ref({
      pelanggan_id: '',
      jumlah: '',
      status: 'belum bayar',
      tanggal_bayar: '',
      bulan_tahun: '',
      metode_pembayaran: '',
      petugas_id: ''
    })

    const updateJumlah = () => {
      const selected = pelangganList.value.find(p => p.id === form.value.pelanggan_id)
      form.value.jumlah = selected ? selected.paket_harga : ''
    }

    const submitForm = async () => {
      try {
        
        const res = await apiFetch('/tagihans', {
          method: 'POST',
          body: JSON.stringify(form.value),
          headers: {
            'Content-Type': 'application/json'
          }
        })

        toast.success('Tagihan berhasil disimpan')
        form.value = {
          pelanggan_id: '',
          jumlah: '',
          status: 'belum bayar',
          tanggal_bayar: '',
          bulan_tahun: '',
          metode_pembayaran: '',
          petugas_id: ''
        }
        router.push('/tagihans')
      } catch (err) {
        console.error(err)
        toast.error('Gagal menyimpan tagihan')
      }
    }

    onMounted(async () => {
      try {
        const [pelangganRes, petugasRes] = await Promise.all([
          apiFetch('/pelanggans/aktif'),
          apiFetch('/users?role=petugas')
        ])
        pelangganList.value = pelangganRes.pelanggans || []
        petugasList.value = petugasRes.users || []
      } catch (err) {
        console.error('Gagal memuat data awal', err)
        toast.error('Gagal memuat data')
      }
    })

    return {
      form,
      pelangganList,
      petugasList,
      updateJumlah,
      submitForm
    }
  }
}
</script>
