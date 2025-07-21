<template>
  <div>
    <h2>Edit Paket</h2>
    <form @submit.prevent="submitForm">
      <div class="mb-3">
        <label class="form-label">Nama Paket</label>
        <input
          type="text"
          v-model="form.nama"
          class="form-control"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Kecepatan</label>
        <input
          type="text"
          v-model="form.kecepatan"
          class="form-control"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input
          type="number"
          v-model.number="form.harga"
          class="form-control"
          required
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea
          v-model="form.deskripsi"
          class="form-control"
        ></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // adjust path if needed

export default {
  name: 'UpdatePaket',
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
  async created() {
    const id = this.$route.params.id;
    try {
      const data = await apiFetch(`/paket/${id}`);
      this.form = {
        nama: data.nama,
        kecepatan: data.kecepatan,
        harga: data.harga,
        deskripsi: data.deskripsi,
      };
    } catch (err) {
      console.error('Gagal memuat data paket:', err);
    }
  },
  methods: {
    async submitForm() {
      const id = this.$route.params.id;
      try {
        await apiFetch(`/paket/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.form),
        });
        this.$router.push('/paket');
      } catch (err) {
        console.error('Gagal menyimpan paket:', err);
      }
    },
  },
};
</script>
