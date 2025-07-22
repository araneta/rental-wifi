<template>
  <div>
    <h2>Edit Tagihan</h2>
    <div class="mb-3">
        <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
        <input type="text" v-model="form.pelanggan" placeholder="Nama" required class="form-control mb-2" readOnly />
    </div>
	<div class="mb-3">
		<label for="petugas_id" class="form-label">Pilih Petugas</label>
		<input type="text" v-model="form.petugas" placeholder="Nama" required class="form-control mb-2" readOnly />
	</div>
	  
    <label>Pelanggan</label>
    
    
    <form @submit.prevent="updateTagihan">
      <select v-model="form.status" class="form-control mb-2" required>
        <option value="belum bayar">Belum Bayar</option>
        <option value="dibayar">Lunas</option>
      </select>

      <input type="date" v-model="form.tanggal_bayar" class="form-control mb-2" />

      <select v-model="form.metode_pembayaran" class="form-control mb-2" required>
        <option value="tunai">Tunai</option>
        <option value="transfer">Transfer</option>
        <option value="QRIS">QRIS</option>
        <option value="Lainnya">Lainnya</option>
      </select>

      <button type="submit" class="btn btn-warning">Update</button>
    </form>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import { apiFetch } from '../../api' // your custom fetch wrapper

const toast = useToast()

export default {
  name: 'TagihanEdit',
  data() {
    return {
      tagihanId: this.$route.params.id, // assuming you're using vue-router
      form: {
        status: '',
        tanggal_bayar: '',
        metode_pembayaran: '',
      }
    }
  },
  async mounted() {
    const id = this.$route.params.id;
    try {
      const data = await apiFetch(`/tagihans/${id}`, { method: 'GET' });
      this.form = data.tagihan;

    } catch (err) {
      console.error('Error fetching data:', err);
    }
  },
  methods: {
    async updateTagihan() {
      try {
        await apiFetch(`/tagihans/${this.tagihanId}`, {
          method: 'PUT',
          body: JSON.stringify(this.form)
        });
        toast.success('Tagihan berhasil diperbarui!');
        this.$router.push('/tagihans'); // redirect back to tagihan list
      } catch (error) {
        console.error('Update failed:', error);
        toast.error('Gagal memperbarui tagihan.');
      }
    }
  }
}
</script>
