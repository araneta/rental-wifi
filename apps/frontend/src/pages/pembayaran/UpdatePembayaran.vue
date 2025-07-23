<template>
  <div>
    <h2>Edit Pembayaran</h2>

    <form @submit.prevent="submitForm">
      <div class="mb-2">
        <label>Jumlah</label>
        <input type="number" v-model="form.jumlah" class="form-control" required />
      </div>

      <div class="mb-2">
        <label>Metode</label>
        <select v-model="form.metode_pembayaran" class="form-control mb-2">
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer</option>
          <option value="QRIS">QRIS</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>

      <div class="mb-2">
        <label>Tanggal</label>
        <input type="date" v-model="form.tanggal_pembayaran" class="form-control" required />
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</template>
<script>
import { apiFetch } from '../../api'
import { useToast } from 'vue-toastification'

export default {
  name: 'UpdatePembayaran',
  data() {
    return {
      form: {
        jumlah: '',
        metode_pembayaran: 'tunai',
        tanggal_pembayaran: ''
      },
      id: this.$route.params.id,
      toast: null
    }
  },
  mounted() {
    this.toast = useToast()
    this.fetchPembayaran()
  },
  methods: {
    async fetchPembayaran() {
      try {
        const response = await apiFetch(`/pembayarans/${this.id}`)
        this.form = {
          jumlah: response.pembayaran.jumlah,
          metode_pembayaran: response.pembayaran.metode_pembayaran,
          tanggal_pembayaran: response.pembayaran.tanggal_pembayaran.split(' ')[0]
        }
      } catch (err) {
        console.error('Gagal memuat data pembayaran', err)
        this.toast.error('Gagal memuat data pembayaran')
      }
    },
    async submitForm() {
      try {
        await apiFetch(`/pembayarans/${this.id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        })
        this.toast.success('Pembayaran berhasil diperbarui!')
        this.$router.push('/pembayarans')
      } catch (err) {
        console.error('Gagal memperbarui pembayaran', err)
        this.toast.error('Terjadi kesalahan saat memperbarui.')
      }
    }
  }
}
</script>
