<template>
  <div>
    <h2>Tambah Paket</h2>
    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label class="form-label">Nama Paket</label>
        <input type="text" v-model="form.nama" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Kecepatan</label>
        <input type="text" v-model="form.kecepatan" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" v-model.number="form.harga" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea v-model="form.deskripsi" class="form-control"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // adjust if needed

export default {
  name: 'CreatePaket',
  data() {
    return {
      form: {
        nama: '',
        kecepatan: '',
        harga: '',
        deskripsi: '',
      },
    };
  },
  methods: {
    async submitForm() {
      try {
        await apiFetch('/pakets', {
          method: 'POST',
          body: JSON.stringify(this.form),
          headers: {
            'Content-Type': 'application/json',
          },
        });
        this.$router.push('/pakets');
      } catch (err) {
        console.error('Gagal menyimpan paket:', err);
      }
    },
  },
};
</script>

<style scoped>
/* Add your styling here if needed */
</style>
